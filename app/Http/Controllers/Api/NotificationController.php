<?php

namespace App\Http\Controllers\Api;

use App\Models\Employee;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/send-notification",
     *      operationId="sendNotification",
     *      tags={"Notifications"},
     *      security={{"sanctum":{}}},
     *      summary="Send notification to an employee",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1),
     *              @OA\Property(property="message", type="string", example="Please submit your expense reports.")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Notification sent successfully")
     * )
     */
    public function sendNotification(Request $request)
    {
        try {

            $companyId = Auth::id();
            $employeeId = $request->input('employee_id');
            $message = $request->input('message');

            $employee = Employee::find($employeeId);
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found.',
                ], 200);
            }

            if ($employee->company_id !== $companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee does not belong to the company.',
                ], 200);
            }

            $company = CompanyDetail::find($companyId);
            if (!$company) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company not found.',
                ], 200);
            }

            // Create the notification for the employee
            $notification = Notification::create([
                'employee_id' => $employeeId,
                'company_id' => $companyId,
                'message' => $message,

            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully.',
                'data' => $notification,


            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the notification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *      path="/api/notifications",
     *      operationId="getNotifications",
     *      tags={"Notifications"},
     *      security={{"sanctum":{}}},
     *      summary="Get notifications for an employee",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="employee_id", type="integer", example=1)
     *          )
     *      ),
     *      @OA\Response(response=200, description="Notifications retrieved successfully")
     * )
     */
    public function getNotifications(Request $request)
    {
        try {
            $employeeId = $request->input('employee_id');

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee ID is required.',
                ], 200);
            }

            // Retrieve both read and unread notifications
            $notifications = Notification::where('employee_id', $employeeId)
                ->whereIn('status', ['unread', 'read'])
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Notifications retrieved successfully.',
                'data' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving notifications.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * @OA\Post(
     *      path="/api/notifications/read",
     *      operationId="markNotificationAsRead",
     *      tags={"Notifications"},
     *      security={{"sanctum":{}}},
     *      summary="Mark a notification status (read/unread)",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="notification_id", type="integer", example=1),
     *              @OA\Property(property="status", type="string", enum={"read", "unread"}, example="read")
     *          )
     *      ),
     *      @OA\Response(response=200, description="Notification marked status")
     * )
     */
    public function markAsRead(Request $request)
    {
        try {

            $companyId = Auth::id();
            $status = $request->input('status', 'unread');
            $notificationId = $request->input('notification_id');

            // Validate the presence of notification_id
            if (!$notificationId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification ID is required.',
                ], 200);
            }

            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.',
                ], 200);
            }
            $notification = Notification::find($notificationId);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification not found.',
                ], 200);
            }

            // Check if the notification belongs to the authenticated user (security check)
            if ($notification->company_id !== $companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee does not belong to the company.',
                ], 200);
            }
            // Update the notification's status
            $notification->status = $status;
            $notification->save();

            return response()->json([
                'success' => true,
                'message' => 'Notification marked as ' . $status . '.',
                'data' => $notification,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking the notification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}