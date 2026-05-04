<?php
// app/Http/Controllers/NotificationController.php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    // GET /notifications
    public function index()
    {
        // Auth::user() is a UserCredentials model — employee_id is directly on it
        $empId = Auth::user()->employee_id ?? null;

        if (!$empId) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $rows = DB::table('notifications')
            ->where('recipient_id', $empId)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $notifications = $rows->map(fn($n) => [
            'id'         => $n->notification_id,
            'type'       => $n->type,
            'title'      => $n->title,
            'message'    => $n->message,
            'is_read'    => (bool) $n->is_read,
            'time_ago'   => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
            'icon_color' => $this->iconColor($n->type),
            'icon_bg'    => $this->iconBg($n->type),
            'ref_id'     => $n->reference_id,
            'ref_type'   => $n->reference_type,
        ]);

        $unread = DB::table('notifications')
            ->where('recipient_id', $empId)
            ->where('is_read', 0)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unread,
        ]);
    }

    // POST /notifications/{id}/read
    public function markRead($id)
    {
        $empId = Auth::user()->employee_id ?? null;

        if ($empId) {
            DB::table('notifications')
                ->where('notification_id', $id)
                ->where('recipient_id', $empId)
                ->update(['is_read' => 1]);
        }

        return response()->json(['success' => true]);
    }

    // POST /notifications/read-all
    public function markAllRead()
    {
        $empId = Auth::user()->employee_id ?? null;

        if ($empId) {
            DB::table('notifications')
                ->where('recipient_id', $empId)
                ->where('is_read', 0)
                ->update(['is_read' => 1]);
        }

        return response()->json(['success' => true]);
    }

    // GET /notifications/unread-count
    public function unreadCount()
    {
        $empId = Auth::user()->employee_id ?? null;

        $count = $empId
            ? DB::table('notifications')->where('recipient_id', $empId)->where('is_read', 0)->count()
            : 0;

        return response()->json(['count' => $count]);
    }

    // ── Icon helpers ──────────────────────────────────────────────
    private function iconColor(string $type): string
    {
        return match($type) {
            'leave_approved'                    => '#16a34a',
            'leave_rejected'                    => '#dc2626',
            'leave_pending'                     => '#ca8a04',
            'leave_cancelled'                   => '#6b7280',
            'leave_status_change',
            'leave_status_changed'              => '#2563eb',
            'halfday_approved'                  => '#16a34a',
            'halfday_rejected'                  => '#dc2626',
            'halfday_pending'                   => '#ca8a04',
            'halfday_cancelled'                 => '#6b7280',
            'halfday_submitted'                 => '#2563eb',
            default                             => '#6b7280',
        };
    }

    private function iconBg(string $type): string
    {
        return match($type) {
            'leave_approved'                    => '#dcfce7',
            'leave_rejected'                    => '#fee2e2',
            'leave_pending'                     => '#fef9c3',
            'leave_cancelled'                   => '#f3f4f6',
            'leave_status_change',
            'leave_status_changed'              => '#dbeafe',
            'halfday_approved'                  => '#dcfce7',
            'halfday_rejected'                  => '#fee2e2',
            'halfday_pending'                   => '#fef9c3',
            'halfday_cancelled'                 => '#f3f4f6',
            'halfday_submitted'                 => '#ede9fe',
            default                             => '#f3f4f6',
        };
    }
}