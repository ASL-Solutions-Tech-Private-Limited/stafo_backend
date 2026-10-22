<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\BbpsCommission;
use App\Models\BbpsServiceCategory;
use App\Models\CompanyDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class BbpsCommissionController extends Controller
{
    /**
     * Display the company-wise BBPS Commission Setup page.
     */
    public function index(Request $request)
    {
        $companies = CompanyDetail::select('id', 'company_name', 'package_id')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->where('company_name', 'not like', '.%')
            ->orderBy('company_name', 'asc')
            ->get();

        $selectedCompanyId = $request->query('company_id', null);
        $selectedCompany = $selectedCompanyId ? CompanyDetail::find($selectedCompanyId) : null;

        $search = $request->query('brandtext', '');
        $categoriesQuery = BbpsServiceCategory::where('service_code', 'bbps')
            ->where('status', 1);

        if (!empty($search)) {
            $categoriesQuery->where('name', 'like', '%' . $search . '%');
        }

        $categories = $categoriesQuery->orderBy('orderBy', 'asc')->get();

        $commissions = collect();
        if ($selectedCompany) {
            $commissions = BbpsCommission::where('company_id', $selectedCompany->id)
                ->get()
                ->keyBy('billtypeid');
        }

        return view('admin.bbps_commission.index', compact('companies', 'selectedCompany', 'categories', 'commissions', 'search'));
    }

    /**
     * Fetch rendered table rows via AJAX (matching legacy brandtext / mode filter).
     */
    public function getTable(Request $request)
    {
        $companyId = $request->input('company_id');
        $mode = $request->input('mode', '');
        $brandtext = $request->input('brandtext', '');

        $company = CompanyDetail::find($companyId);
        if (!$company) {
            return response()->json([
                'status' => false,
                'message' => 'Selected company not found!'
            ], 404);
        }

        $query = BbpsServiceCategory::where('service_code', 'bbps')
            ->where('status', 1);

        if ($mode == '1' && !empty($brandtext)) {
            $query->where('name', 'like', '%' . $brandtext . '%');
        }

        $categories = $query->orderBy('orderBy', 'asc')->get();

        $commissions = BbpsCommission::where('company_id', $company->id)
            ->get()
            ->keyBy('billtypeid');

        $html = view('admin.bbps_commission.table_rows', [
            'categories' => $categories,
            'commissions' => $commissions,
            'companyId' => $company->id
        ])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'count' => $categories->count()
        ]);
    }

    /**
     * Save/update a single BBPS commission record for a company and billtype.
     */
    public function saveSingle(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'billtypeid' => 'required|integer',
            'comper' => 'nullable|numeric|min:0',
            'gstper' => 'nullable|numeric|min:0',
        ]);

        $companyId = $request->company_id;
        $billtypeid = $request->billtypeid;
        $comper = $request->filled('comper') ? (float)$request->comper : 0.0;
        $gstper = $request->filled('gstper') ? (float)$request->gstper : 0.0;
        $issurcharge = ($request->issurcharge == '1' || $request->issurcharge === true || $request->issurcharge === 'true') ? '1' : '0';
        $isflat = ($request->isflat == '1' || $request->isflat === true || $request->isflat === 'true') ? '1' : '0';

        $company = CompanyDetail::find($companyId);
        $pacid = $company->package_id ?? 0;
        $userName = Auth::guard('admin')->user()->name ?? 'admin';
        $now = date('Y-m-d H:i:s');

        $commission = BbpsCommission::where('company_id', $companyId)
            ->where('billtypeid', $billtypeid)
            ->first();

        if ($commission) {
            $commission->comper = $comper;
            $commission->gstper = $gstper;
            $commission->issurcharge = $issurcharge;
            $commission->isflat = $isflat;
            $commission->optid = $billtypeid;
            if ($pacid) {
                $commission->pacid = $pacid;
            }
            $commission->update_date = $now;
            $commission->update_user = $userName;
            $commission->save();
        } else {
            $commission = BbpsCommission::create([
                'pacid' => $pacid,
                'company_id' => $companyId,
                'optid' => $billtypeid,
                'billtypeid' => $billtypeid,
                'gift_id' => null,
                'comper' => $comper,
                'gstper' => $gstper,
                'issurcharge' => $issurcharge,
                'isflat' => $isflat,
                'status' => '1',
                'insert_date' => $now,
                'insert_user' => $userName,
                'update_date' => $now,
                'update_user' => $userName,
            ]);
        }

        $category = BbpsServiceCategory::find($billtypeid);
        $categoryName = $category ? $category->name : 'Bill Type';

        return response()->json([
            'status' => true,
            'message' => "Commission for {$categoryName} saved successfully!",
            'data' => $commission
        ]);
    }

    /**
     * Save/update multiple BBPS commission records at once.
     */
    public function saveAll(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_details,id',
            'rows' => 'required|array',
        ]);

        $companyId = $request->company_id;
        $rows = $request->rows;
        $company = CompanyDetail::find($companyId);
        $pacid = $company->package_id ?? 0;
        $userName = Auth::guard('admin')->user()->name ?? 'admin';
        $now = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $billtypeid = $row['billtypeid'] ?? null;
                if (!$billtypeid) continue;

                $comper = isset($row['comper']) && $row['comper'] !== '' ? (float)$row['comper'] : 0.0;
                $gstper = isset($row['gstper']) && $row['gstper'] !== '' ? (float)$row['gstper'] : 0.0;
                $issurcharge = (!empty($row['issurcharge']) && ($row['issurcharge'] == '1' || $row['issurcharge'] === true || $row['issurcharge'] === 'true')) ? '1' : '0';
                $isflat = (!empty($row['isflat']) && ($row['isflat'] == '1' || $row['isflat'] === true || $row['isflat'] === 'true')) ? '1' : '0';

                $commission = BbpsCommission::where('company_id', $companyId)
                    ->where('billtypeid', $billtypeid)
                    ->first();

                if ($commission) {
                    $commission->comper = $comper;
                    $commission->gstper = $gstper;
                    $commission->issurcharge = $issurcharge;
                    $commission->isflat = $isflat;
                    $commission->optid = $billtypeid;
                    if ($pacid) {
                        $commission->pacid = $pacid;
                    }
                    $commission->update_date = $now;
                    $commission->update_user = $userName;
                    $commission->save();
                } else {
                    BbpsCommission::create([
                        'pacid' => $pacid,
                        'company_id' => $companyId,
                        'optid' => $billtypeid,
                        'billtypeid' => $billtypeid,
                        'gift_id' => null,
                        'comper' => $comper,
                        'gstper' => $gstper,
                        'issurcharge' => $issurcharge,
                        'isflat' => $isflat,
                        'status' => '1',
                        'insert_date' => $now,
                        'insert_user' => $userName,
                        'update_date' => $now,
                        'update_user' => $userName,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'All BBPS commissions saved successfully!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error saving commissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get modal HTML for a specific company (for launching from Company List or anywhere).
     */
    public function getCompanyModal(Request $request, $companyId)
    {
        $company = CompanyDetail::find($companyId);
        if (!$company) {
            return response()->json(['status' => false, 'message' => 'Company not found'], 404);
        }

        $categories = BbpsServiceCategory::where('service_code', 'bbps')
            ->where('status', 1)
            ->orderBy('orderBy', 'asc')
            ->get();

        $commissions = BbpsCommission::where('company_id', $company->id)
            ->get()
            ->keyBy('billtypeid');

        $html = view('admin.bbps_commission.modal_content', [
            'company' => $company,
            'categories' => $categories,
            'commissions' => $commissions
        ])->render();

        return response()->json([
            'status' => true,
            'html' => $html,
            'company_name' => $company->company_name
        ]);
    }
}
