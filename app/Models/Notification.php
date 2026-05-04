<?php
// app/Models/Notification.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    protected $table      = 'notifications';
    protected $primaryKey = 'notification_id';

    // Real DB columns — no 'user_id', no 'ref_id', no 'ref_type'
    protected $fillable = [
        'recipient_id',
        'sender_id',
        'type',
        'title',
        'message',
        'reference_id',
        'reference_type',
        'is_read',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function recipient()
    {
        return $this->belongsTo(Employee::class, 'recipient_id', 'employee_id');
    }

    public function sender()
    {
        return $this->belongsTo(Employee::class, 'sender_id', 'employee_id');
    }

    // ── Computed attributes ────────────────────────────────────
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at?->diffForHumans() ?? '';
    }

    public function getIconColorAttribute(): string
    {
        return match($this->type) {
            'leave_approved'       => '#16a34a',
            'leave_rejected'       => '#dc2626',
            'leave_pending'        => '#ca8a04',
            'leave_cancelled'      => '#6b7280',
            'leave_status_change',
            'leave_status_changed' => '#2563eb',
            'halfday_approved'     => '#16a34a',
            'halfday_rejected'     => '#dc2626',
            'halfday_pending'      => '#ca8a04',
            'halfday_cancelled'    => '#6b7280',
            'halfday_submitted'    => '#2563eb',
            default                => '#6b7280',
        };
    }

    public function getIconBgAttribute(): string
    {
        return match($this->type) {
            'leave_approved'       => '#dcfce7',
            'leave_rejected'       => '#fee2e2',
            'leave_pending'        => '#fef9c3',
            'leave_cancelled'      => '#f3f4f6',
            'leave_status_change',
            'leave_status_changed' => '#dbeafe',
            'halfday_approved'     => '#dcfce7',
            'halfday_rejected'     => '#fee2e2',
            'halfday_pending'      => '#fef9c3',
            'halfday_cancelled'    => '#f3f4f6',
            'halfday_submitted'    => '#ede9fe',
            default                => '#f3f4f6',
        };
    }

    // ── Static: Notify on leave application status change ─────
    // Called by AdminLeaveController when approving/rejecting/etc.
    public static function notifyLeaveStatusChange($leaveApp, string $newStatus, ?int $actorId = null): void
    {
        $employeeId = $leaveApp->employee_id;
        $leaveId    = $leaveApp->leave_id;
        $leaveName  = $leaveApp->leaveType?->type_name ?? 'Leave';
        $empName    = strtoupper($leaveApp->employee->last_name ?? '') . ', ' . ($leaveApp->employee->first_name ?? '');

        $labels = [
            'PENDING'    => 'Pending',
            'RECEIVED'   => 'Received',
            'ON-PROCESS' => 'On-Process',
            'APPROVED'   => 'Approved',
            'REJECTED'   => 'Rejected',
            'CANCELLED'  => 'Cancelled',
        ];
        $label = $labels[$newStatus] ?? $newStatus;

        $type = match($newStatus) {
            'APPROVED'  => 'leave_approved',
            'REJECTED'  => 'leave_rejected',
            'CANCELLED' => 'leave_cancelled',
            default     => 'leave_status_change',
        };

        $now = now();

        // Notify the employee who filed
        DB::table('notifications')->insert([
            'recipient_id'   => $employeeId,
            'sender_id'      => $actorId,
            'type'           => $type,
            'title'          => "Leave Application {$label}",
            'message'        => "Your {$leaveName} application has been marked as {$label}.",
            'reference_id'   => $leaveId,
            'reference_type' => $leaveApp->is_monetization ? 'monetization' : 'leave_application',
            'is_read'        => 0,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);

        // Notify all OTHER admins via user_access table
        $otherAdminIds = DB::table('user_access')
            ->where('user_access', 'admin')
            ->where('is_active', 1)
            ->where('employee_id', '!=', $actorId ?? 0)
            ->pluck('employee_id');

        foreach ($otherAdminIds as $adminId) {
            DB::table('notifications')->insert([
                'recipient_id'   => $adminId,
                'sender_id'      => $actorId,
                'type'           => 'leave_status_change',
                'title'          => "Leave Updated: {$label}",
                'message'        => "{$empName}'s {$leaveName} application is now {$label}.",
                'reference_id'   => $leaveId,
                'reference_type' => $leaveApp->is_monetization ? 'monetization' : 'leave_application',
                'is_read'        => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    // ── Static: New leave filed (notify all admins) ────────────
    // Called by LeaveApplicationController when employee files new leave
    public static function notifyNewLeave($leaveApp): void
    {
        $leaveName = $leaveApp->leaveType?->type_name ?? 'Leave';
        $empName   = strtoupper($leaveApp->employee->last_name ?? '') . ', ' . ($leaveApp->employee->first_name ?? '');
        $leaveId   = $leaveApp->leave_id;
        $now       = now();

        $adminIds = DB::table('user_access')
            ->where('user_access', 'admin')
            ->where('is_active', 1)
            ->pluck('employee_id');

        foreach ($adminIds as $adminId) {
            DB::table('notifications')->insert([
                'recipient_id'   => $adminId,
                'sender_id'      => $leaveApp->employee_id,
                'type'           => 'leave_pending',
                'title'          => 'New Leave Application',
                'message'        => "{$empName} filed a {$leaveName} application.",
                'reference_id'   => $leaveId,
                'reference_type' => $leaveApp->is_monetization ? 'monetization' : 'leave_application',
                'is_read'        => 0,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }
}