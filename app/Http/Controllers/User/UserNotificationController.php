<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserNotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated company
     */
    public function index(Request $request)
    {
        $companyId = Auth::id();

        $query = Notification::with('employee')
            ->where('company_id', $companyId)
            ->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('message', 'like', "%{$search}%")
                  ->orWhereHas('employee', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%")
                         ->orWhere('emp_id', 'like', "%{$search}%");
                  });
            });
        }

        $notifications = $query->paginate(20)->withQueryString();
        $unreadCount = Notification::where('company_id', $companyId)->where('status', 'unread')->count();

        return view('user.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Fetch unread count and latest notifications feed (for real-time topbar polling & browser push)
     */
    public function getNotifications(Request $request)
    {
        try {
            $companyId = Auth::id();
            if (!$companyId) {
                return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
            }

            $unreadCount = Notification::where('company_id', $companyId)
                ->where('status', 'unread')
                ->count();

            $notifications = Notification::with('employee:id,name,emp_id,image')
                ->where('company_id', $companyId)
                ->orderBy('id', 'desc')
                ->take(15)
                ->get()
                ->map(function ($notif) {
                    $msg = strtolower($notif->message ?? '');
                    
                    // Determine contextual category, icon & destination route
                    $type = 'general';
                    $icon = 'fa-solid fa-bell text-primary';
                    $url = route('user.dashboard');

                    if (str_contains($msg, 'punch-out') || str_contains($msg, 'regularisation') || str_contains($msg, 'regularization')) {
                        $type = 'missed_punchout';
                        $icon = 'fa-solid fa-business-time text-warning';
                        $url = route('user.attendanceRequests.index');
                    } elseif (str_contains($msg, 'punch in') || str_contains($msg, 'punched in')) {
                        $type = 'punch_in';
                        $icon = 'fa-solid fa-arrow-right-to-bracket text-success';
                        $url = route('attendance.index');
                    } elseif (str_contains($msg, 'punch out') || str_contains($msg, 'punched out')) {
                        $type = 'punch_out';
                        $icon = 'fa-solid fa-arrow-right-from-bracket text-danger';
                        $url = route('attendance.index');
                    } elseif (str_contains($msg, 'leave')) {
                        $type = 'leave';
                        $icon = 'fa-solid fa-calendar-minus text-warning';
                        $url = route('leaveList');
                    } elseif (str_contains($msg, 'location') || str_contains($msg, 'tracking') || str_contains($msg, 'geo')) {
                        $type = 'location';
                        $icon = 'fa-solid fa-location-dot text-info';
                        $url = route('employee.location');
                    } elseif (str_contains($msg, 'expense') || str_contains($msg, 'claim')) {
                        $type = 'expense';
                        $icon = 'fa-solid fa-receipt text-success';
                        $url = route('expenseList');
                    } elseif (str_contains($msg, 'device')) {
                        $type = 'device';
                        $icon = 'fa-solid fa-fingerprint text-primary';
                        $url = route('deviceList');
                    }

                    return [
                        'id' => $notif->id,
                        'message' => $notif->message,
                        'status' => $notif->status,
                        'type' => $type,
                        'icon' => $icon,
                        'url' => $url,
                        'time_ago' => $notif->created_at ? $notif->created_at->diffForHumans() : 'Just now',
                        'created_at' => $notif->created_at ? $notif->created_at->toIso8601String() : null,
                        'employee' => $notif->employee ? [
                            'id' => $notif->employee->id,
                            'name' => $notif->employee->name,
                            'emp_id' => $notif->employee->emp_id,
                            'image' => $notif->employee->image,
                        ] : null,
                    ];
                });

            return response()->json([
                'status' => true,
                'unread_count' => $unreadCount,
                'notifications' => $notifications,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a single notification or all notifications as read
     */
    public function markAsRead(Request $request, $id = null)
    {
        try {
            $companyId = Auth::id();

            if ($id && $id !== 'all') {
                Notification::where('company_id', $companyId)
                    ->where('id', $id)
                    ->update(['status' => 'read']);
            } else {
                Notification::where('company_id', $companyId)
                    ->where('status', 'unread')
                    ->update(['status' => 'read']);
            }

            return response()->json([
                'status' => true,
                'message' => 'Notification(s) marked as read.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
