<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CompanyDetail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BbpsTransactionController extends Controller
{
    /**
     *  Display BBPS Transaction Report with filters and summary KPI cards.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $userType = $request->query('user_type', '');
        $companyId = $request->query('company_id', '');
        $dateFrom = $request->query('date_from', '');
        $dateTo = $request->query('date_to', '');

        // Base Query with Relationships
        $query = Transaction::with(['employee.company', 'company'])->latest('id');

        // Filter: Search Keyword
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('txnid', 'like', "%{$search}%")
                  ->orWhere('payu_payment_id', 'like', "%{$search}%")
                  ->orWhere('product_info', 'like', "%{$search}%");
            });
        }

        // Filter: Status
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter: User Type
        if (!empty($userType) && $userType !== 'all') {
            $query->where('user_type', $userType);
        }

        // Filter: Company
        if (!empty($companyId) && $companyId !== 'all') {
            $query->where(function ($q) use ($companyId) {
                // Either user is the company
                $q->where(function ($sub) use ($companyId) {
                    $sub->where('user_type', 'company')
                        ->where('user_id', $companyId);
                })
                // Or user is an employee belonging to this company
                ->orWhere(function ($sub) use ($companyId) {
                    $sub->where('user_type', 'employee')
                        ->whereHas('employee', function ($empQ) use ($companyId) {
                            $empQ->where('company_id', $companyId);
                        });
                });
            });
        }

        // Filter: Date Range
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        // Summary KPI Metrics (clone before pagination)
        $statsQuery = clone $query;
        $totalCount = $statsQuery->count();
        $totalAmount = (float)$statsQuery->sum('amount');
        $totalCommission = (float)$statsQuery->sum('commission');

        $successQuery = (clone $statsQuery)->where('status', 'success');
        $successCount = $successQuery->count();
        $successAmount = (float)$successQuery->sum('amount');

        $pendingQuery = (clone $statsQuery)->where('status', 'pending');
        $pendingCount = $pendingQuery->count();
        $pendingAmount = (float)$pendingQuery->sum('amount');

        $failedQuery = (clone $statsQuery)->where(function ($q) {
            $q->where('status', 'failed')->orWhere('status', 'failure');
        });
        $failedCount = $failedQuery->count();
        $failedAmount = (float)$failedQuery->sum('amount');

        // Paginate Results
        $transactions = $query->paginate(20)->withQueryString();

        // Dropdown data
        $companies = CompanyDetail::select('id', 'company_name')
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->orderBy('company_name', 'asc')
            ->get();

        return view('admin.bbps_transactions.index', compact(
            'transactions',
            'companies',
            'search',
            'status',
            'userType',
            'companyId',
            'dateFrom',
            'dateTo',
            'totalCount',
            'totalAmount',
            'totalCommission',
            'successCount',
            'successAmount',
            'pendingCount',
            'pendingAmount',
            'failedCount',
            'failedAmount'
        ));
    }

    /**
     * Get transaction details for modal view.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['employee.company', 'company'])->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => false,
                'message' => 'Transaction not found.'
            ], 404);
        }

        // Resolve user display info
        $userName = '-';
        $userEmail = '-';
        $userPhone = '-';
        $companyName = '-';

        if ($transaction->user_type === 'employee' && $transaction->employee) {
            $userName = $transaction->employee->name ?? 'Employee #' . $transaction->user_id;
            $userEmail = $transaction->employee->email ?? '-';
            $userPhone = $transaction->employee->phone ?? '-';
            $companyName = $transaction->employee->company->company_name ?? 'Company #' . ($transaction->employee->company_id ?? '-');
        } elseif ($transaction->user_type === 'company' && $transaction->company) {
            $userName = $transaction->company->company_name ?? 'Company #' . $transaction->user_id;
            $userEmail = $transaction->company->email ?? '-';
            $userPhone = $transaction->company->mobile_no ?? '-';
            $companyName = $transaction->company->company_name ?? '-';
        }

        // Parse PayU response JSON if valid
        $payuResponseFormatted = null;
        if (!empty($transaction->payu_response)) {
            $decoded = json_decode($transaction->payu_response, true);
            $payuResponseFormatted = $decoded ? json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $transaction->payu_response;
        }

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $transaction->id,
                'txnid' => $transaction->txnid,
                'amount' => number_format((float)$transaction->amount, 2),
                'bill_amount' => number_format((float)($transaction->bill_amount ?? $transaction->amount), 2),
                'commission' => number_format((float)($transaction->commission ?? 0), 2),
                'product_info' => $transaction->product_info ?? 'N/A',
                'status' => strtolower($transaction->status ?? 'pending'),
                'payu_payment_id' => $transaction->payu_payment_id ?? 'N/A',
                'hash_received' => $transaction->hash_received ?? 'N/A',
                'hash_calculated' => $transaction->hash_calculated ?? 'N/A',
                'payu_response' => $payuResponseFormatted,
                'user_id' => $transaction->user_id,
                'user_type' => ucfirst($transaction->user_type ?? 'N/A'),
                'user_name' => $userName,
                'user_email' => $userEmail,
                'user_phone' => $userPhone,
                'company_name' => $companyName,
                'created_at' => $transaction->created_at ? $transaction->created_at->format('d M Y, h:i A') : 'N/A',
                'updated_at' => $transaction->updated_at ? $transaction->updated_at->format('d M Y, h:i A') : 'N/A',
            ]
        ]);
    }

    /**
     * Export filtered BBPS transactions to CSV.
     */
    public function export(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $userType = $request->query('user_type', '');
        $companyId = $request->query('company_id', '');
        $dateFrom = $request->query('date_from', '');
        $dateTo = $request->query('date_to', '');

        $query = Transaction::with(['employee.company', 'company'])->latest('id');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('txnid', 'like', "%{$search}%")
                  ->orWhere('payu_payment_id', 'like', "%{$search}%")
                  ->orWhere('product_info', 'like', "%{$search}%");
            });
        }
        if (!empty($status) && $status !== 'all') {
            $query->where('status', $status);
        }
        if (!empty($userType) && $userType !== 'all') {
            $query->where('user_type', $userType);
        }
        if (!empty($companyId) && $companyId !== 'all') {
            $query->where(function ($q) use ($companyId) {
                $q->where(function ($sub) use ($companyId) {
                    $sub->where('user_type', 'company')->where('user_id', $companyId);
                })->orWhere(function ($sub) use ($companyId) {
                    $sub->where('user_type', 'employee')->whereHas('employee', function ($empQ) use ($companyId) {
                        $empQ->where('company_id', $companyId);
                    });
                });
            });
        }
        if (!empty($dateFrom)) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $filename = 'bbps_transactions_' . date('Y_m_d_His') . '.csv';

        $response = new StreamedResponse(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // CSV Headers
            fputcsv($handle, [
                'ID',
                'Transaction ID',
                'User Type',
                'Payer Name',
                'Company Name',
                'Product / Category Info',
                'Bill Amount (INR)',
                'Commission (INR)',
                'Paid Amount (INR)',
                'Status',
                'PayU Payment ID',
                'Created At',
                'Updated At'
            ]);

            $query->chunk(200, function ($transactions) use ($handle) {
                foreach ($transactions as $txn) {
                    $userName = '-';
                    $compName = '-';

                    if ($txn->user_type === 'employee' && $txn->employee) {
                        $userName = $txn->employee->name ?? 'Employee #' . $txn->user_id;
                        $compName = $txn->employee->company->company_name ?? '-';
                    } elseif ($txn->user_type === 'company' && $txn->company) {
                        $userName = $txn->company->company_name ?? 'Company #' . $txn->user_id;
                        $compName = $txn->company->company_name ?? '-';
                    }

                    fputcsv($handle, [
                        $txn->id,
                        $txn->txnid,
                        ucfirst($txn->user_type ?? ''),
                        $userName,
                        $compName,
                        $txn->product_info ?? '',
                        number_format((float)($txn->bill_amount ?? $txn->amount), 2, '.', ''),
                        number_format((float)($txn->commission ?? 0), 2, '.', ''),
                        number_format((float)$txn->amount, 2, '.', ''),
                        strtoupper($txn->status ?? 'PENDING'),
                        $txn->payu_payment_id ?? '',
                        $txn->created_at ? $txn->created_at->format('Y-m-d H:i:s') : '',
                        $txn->updated_at ? $txn->updated_at->format('Y-m-d H:i:s') : '',
                    ]);
                }
            });

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }
}
