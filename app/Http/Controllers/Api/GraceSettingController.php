<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GraceSetting;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * @OA\Tag(
 *     name="Company Grace Settings",
 *     description="Only 2 APIs for Company Panel Grace Settings: List and Update"
 * )
 */
class GraceSettingController extends Controller
{
    /**
     * Resolve company_id from request or authenticated user.
     */
    private function resolveCompanyId(Request $request)
    {
        // 1. Explicitly passed in request (query or body)
        if ($request->filled('company_id') && is_numeric($request->company_id)) {
            return (int)$request->company_id;
        }

        // 2. Authenticated user (Sanctum)
        $user = Auth::user();
        if ($user) {
            if (!empty($user->company_id)) {
                return (int)$user->company_id;
            }
            return (int)$user->id;
        }

        // 3. Fallback: check user_id and user_type if provided in request
        if ($request->filled('user_id')) {
            $userType = $request->input('user_type', 'company');
            if ($userType === 'employee') {
                $employee = Employee::find($request->user_id);
                if ($employee && !empty($employee->company_id)) {
                    return (int)$employee->company_id;
                }
            }
            return (int)$request->user_id;
        }

        return null;
    }

    /**
     * Ensure default settings exist for a company (Grace Time, Grace Days, Over Time Charges).
     */
    private function ensureDefaultSettings($companyId)
    {
        if (!$companyId) {
            return;
        }

        $count = GraceSetting::where('company_id', $companyId)->count();
        if ($count === 0) {
            GraceSetting::create([
                'company_id' => $companyId,
                'name'       => 'Grace Time',
                'label'      => 'grace_time',
                'value'      => '15',
                'status'     => 'Active',
            ]);
            GraceSetting::create([
                'company_id' => $companyId,
                'name'       => 'Grace Days',
                'label'      => 'grace_days',
                'value'      => '3',
                'status'     => 'Active',
            ]);
            GraceSetting::create([
                'company_id' => $companyId,
                'name'       => 'Over Time Charges',
                'label'      => 'over_time_charges',
                'value'      => '3',
                'status'     => 'Active',
            ]);
        }
    }

    /**
     * Build formatted key-value map for mobile apps.
     */
    private function formatSettingsMap($settings)
    {
        $map = [];
        foreach ($settings as $s) {
            $key = !empty($s->label) ? $s->label : str_replace(' ', '_', strtolower($s->name));
            $map[$key] = $s->value;
        }
        return $map;
    }

    /**
     * 1. LIST API: Fetch all grace settings for the company.
     * 
     * @OA\Get(
     *     path="/api/company/grace-settings/list",
     *     summary="1. List company grace settings",
     *     tags={"Company Grace Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="company_id",
     *         in="query",
     *         required=false,
     *         description="Company ID (optional if authenticated as company/employee)",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(response=200, description="Grace settings retrieved successfully")
     * )
     */
    public function index(Request $request)
    {
        try {
            $companyId = $this->resolveCompanyId($request);

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'status'  => false,
                    'message' => 'Company ID is required. Please provide company_id or pass bearer token.',
                ], 400);
            }

            // Ensure company defaults exist if first time
            $this->ensureDefaultSettings($companyId);

            $settings = GraceSetting::where('company_id', $companyId)
                ->orderBy('id', 'asc')
                ->get();

            $formatted = $this->formatSettingsMap($settings);

            return response()->json([
                'success'    => true,
                'status'     => true,
                'message'    => 'Grace settings retrieved successfully.',
                'company_id' => $companyId,
                'data'       => $settings,
                'formatted'  => $formatted,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'An error occurred while retrieving grace settings.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 2. UPDATE API: Update grace settings for the company (supports single setting or all settings).
     * 
     * @OA\Post(
     *     path="/api/company/grace-settings/update",
     *     summary="2. Update company grace settings",
     *     tags={"Company Grace Settings"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="company_id", type="integer", example=1),
     *             @OA\Property(property="grace_time", type="string", example="15"),
     *             @OA\Property(property="grace_days", type="string", example="3"),
     *             @OA\Property(property="over_time_charges", type="string", example="3")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Grace settings updated successfully")
     * )
     */
    public function update(Request $request, $id = null)
    {
        try {
            $companyId = $this->resolveCompanyId($request);
            $targetId = $id ?? $request->input('id');

            // Case A: Single setting update by ID (e.g. /update/1 or { "id": 1, "value": "20" })
            if ($targetId) {
                $setting = GraceSetting::find($targetId);

                if (!$setting) {
                    return response()->json([
                        'success' => false,
                        'status'  => false,
                        'message' => 'Grace setting not found.'
                    ], 404);
                }

                if ($request->has('value')) {
                    $setting->value = (string)$request->value;
                }
                if ($request->filled('name')) {
                    $setting->name = $request->name;
                    if (!$request->filled('label')) {
                        $setting->label = str_replace(' ', '_', strtolower($request->name));
                    }
                }
                if ($request->filled('status')) {
                    $setting->status = $request->status;
                }
                $setting->save();

                $companyId = $setting->company_id;
            } else {
                // Case B: Update all settings via key-value or array
                if (!$companyId) {
                    return response()->json([
                        'success' => false,
                        'status'  => false,
                        'message' => 'Company ID is required.',
                    ], 400);
                }

                $this->ensureDefaultSettings($companyId);
                $payload = $request->all();

                // Format 1: Direct key-value fields
                $knownFields = [
                    'grace_time'        => 'Grace Time',
                    'grace_days'        => 'Grace Days',
                    'over_time_charges' => 'Over Time Charges',
                ];

                foreach ($knownFields as $key => $displayName) {
                    if (isset($payload[$key])) {
                        GraceSetting::updateOrCreate(
                            ['company_id' => $companyId, 'label' => $key],
                            [
                                'name'   => $displayName,
                                'value'  => (string)$payload[$key],
                                'status' => 'Active',
                            ]
                        );
                    }
                }

                // Format 2: Array of settings [{ "id": 1, "value": "20" }] or [{ "label": "grace_time", "value": "20" }]
                if (isset($payload['settings']) && is_array($payload['settings'])) {
                    foreach ($payload['settings'] as $item) {
                        if (!empty($item['id'])) {
                            $s = GraceSetting::where('company_id', $companyId)->find($item['id']);
                            if ($s) {
                                if (isset($item['value']))  $s->value = (string)$item['value'];
                                if (isset($item['name']))   $s->name = $item['name'];
                                if (isset($item['status'])) $s->status = $item['status'];
                                $s->save();
                            }
                        } elseif (!empty($item['label'])) {
                            GraceSetting::updateOrCreate(
                                ['company_id' => $companyId, 'label' => $item['label']],
                                [
                                    'name'   => $item['name'] ?? ucwords(str_replace('_', ' ', $item['label'])),
                                    'value'  => (string)($item['value'] ?? '0'),
                                    'status' => $item['status'] ?? 'Active',
                                ]
                            );
                        }
                    }
                }
            }

            // Return latest updated list and formatted map
            $updatedSettings = GraceSetting::where('company_id', $companyId)
                ->orderBy('id', 'asc')
                ->get();

            return response()->json([
                'success'    => true,
                'status'     => true,
                'message'    => 'Grace settings updated successfully.',
                'company_id' => $companyId,
                'data'       => $updatedSettings,
                'formatted'  => $this->formatSettingsMap($updatedSettings),
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'status'  => false,
                'message' => 'An error occurred while updating grace settings.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
