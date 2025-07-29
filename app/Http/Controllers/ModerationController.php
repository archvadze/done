<?php

namespace App\Http\Controllers;

use App\Models\ModerationReport;
use App\Models\ModerationAction;
use App\Models\SecurityLog;
use App\Models\User;
use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!($user->isAdmin() || $user->isModerator())) {
                abort(403, 'Access denied. Moderator privileges required.');
            }
            return $next($request);
        });
    }

    /**
     * Show moderation dashboard
     */
    public function dashboard()
    {
        $stats = [
            'pending_reports' => ModerationReport::pending()->count(),
            'unassigned_reports' => ModerationReport::unassigned()->count(),
            'active_actions' => ModerationAction::active()->count(),
            'recent_security_events' => SecurityLog::critical()->recent(24)->count(),
        ];

        $recent_reports = ModerationReport::with(['reporter', 'reportedUser', 'assignedTo'])
            ->latest()
            ->limit(10)
            ->get();

        $recent_actions = ModerationAction::with(['moderator', 'targetUser'])
            ->latest()
            ->limit(10)
            ->get();

        $recent_security = SecurityLog::with('user')
            ->where('severity', '>=', 'warning')
            ->latest()
            ->limit(10)
            ->get();

        return view('moderation.dashboard', compact('stats', 'recent_reports', 'recent_actions', 'recent_security'));
    }

    /**
     * Show all reports
     */
    public function reports(Request $request)
    {
        $query = ModerationReport::with(['reporter', 'reportedUser', 'assignedTo', 'reportable']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            if ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->reason);
        }

        $reports = $query->latest()->paginate(20);

        $moderators = User::where('role', 'admin')
            ->orWhere('role', 'moderator')
            ->get();

        return view('moderation.reports.index', compact('reports', 'moderators'));
    }

    /**
     * Show specific report
     */
    public function showReport(ModerationReport $report)
    {
        $report->load(['reporter', 'reportedUser', 'assignedTo', 'reportable', 'actions.moderator']);

        return view('moderation.reports.show', compact('report'));
    }

    /**
     * Assign report to moderator
     */
    public function assignReport(Request $request, ModerationReport $report)
    {
        $request->validate([
            'moderator_id' => 'required|exists:users,id',
        ]);

        $moderator = User::findOrFail($request->moderator_id);

        if (!$moderator->hasRole(['admin', 'moderator'])) {
            return back()->withErrors(['moderator_id' => 'Selected user is not a moderator.']);
        }

        $report->assignTo($moderator);

        return back()->with('success', "Report assigned to {$moderator->name}.");
    }

    /**
     * Take moderation action
     */
    public function takeAction(Request $request, ModerationReport $report)
    {
        $request->validate([
            'action_type' => [
                'required',
                Rule::in(['warning', 'hide_content', 'remove_content', 'suspend', 'ban', 'copyright_takedown'])
            ],
            'reason' => 'required|string|max:1000',
            'duration_hours' => 'nullable|integer|min:1|max:8760', // Max 1 year
            'is_permanent' => 'boolean',
        ]);

        // Create the moderation action
        $action = ModerationAction::create([
            'moderator_id' => Auth::id(),
            'target_user_id' => $report->reported_user_id,
            'target_type' => $report->reportable_type,
            'target_id' => $report->reportable_id,
            'report_id' => $report->id,
            'action_type' => $request->action_type,
            'reason' => $request->reason,
            'duration_hours' => $request->is_permanent ? null : $request->duration_hours,
            'expires_at' => $request->is_permanent ? null : ($request->duration_hours ? now()->addHours($request->duration_hours) : null),
            'metadata' => [
                'report_reason' => $report->reason,
                'evidence' => $report->evidence,
            ],
            'is_active' => true,
        ]);

        // Apply the action
        $action->apply();

        // Mark report as resolved
        $report->resolve([
            'action_taken' => $request->action_type,
            'action_id' => $action->id,
            'resolution_notes' => $request->reason,
        ]);

        return back()->with('success', 'Moderation action applied successfully.');
    }

    /**
     * Dismiss report
     */
    public function dismissReport(Request $request, ModerationReport $report)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $report->dismiss($request->reason);

        return back()->with('success', 'Report dismissed.');
    }

    /**
     * Show all actions
     */
    public function actions(Request $request)
    {
        $query = ModerationAction::with(['moderator', 'targetUser', 'target', 'report']);

        // Apply filters
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('moderator_id')) {
            $query->where('moderator_id', $request->moderator_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        if ($request->filled('is_reversed')) {
            if ($request->is_reversed === 'true') {
                $query->whereNotNull('reversed_at');
            } else {
                $query->whereNull('reversed_at');
            }
        }

        $actions = $query->latest()->paginate(20);

        $moderators = User::where('role', 'admin')
            ->orWhere('role', 'moderator')
            ->get();

        return view('moderation.actions.index', compact('actions', 'moderators'));
    }

    /**
     * Reverse moderation action
     */
    public function reverseAction(Request $request, ModerationAction $action)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if ($action->isReversed()) {
            return back()->withErrors(['error' => 'Action has already been reversed.']);
        }

        $action->reverse(Auth::user(), $request->reason);

        return back()->with('success', 'Moderation action reversed successfully.');
    }

    /**
     * Show security logs
     */
    public function securityLogs(Request $request)
    {
        $query = SecurityLog::with('user');

        // Apply filters
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('event_category')) {
            $query->where('event_category', $request->event_category);
        }

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->event_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . $request->ip_address . '%');
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
        }

        $logs = $query->latest()->paginate(20);

        return view('moderation.security.logs', compact('logs'));
    }

    /**
     * Create new report
     */
    public function createReport(Request $request)
    {
        $request->validate([
            'reported_user_id' => 'required|exists:users,id',
            'reportable_type' => 'required|string',
            'reportable_id' => 'required|integer',
            'reason' => 'required|string',
            'description' => 'required|string|max:1000',
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'evidence' => 'nullable|array',
            'evidence.*' => 'string',
        ]);

        // Validate reportable exists
        $reportable = null;
        switch ($request->reportable_type) {
            case 'App\\Models\\Artwork':
                $reportable = Artwork::find($request->reportable_id);
                break;
                // Add other reportable types as needed
        }

        if (!$reportable) {
            return back()->withErrors(['reportable_id' => 'Invalid content reference.']);
        }

        $report = ModerationReport::create([
            'reporter_id' => Auth::id(),
            'reported_user_id' => $request->reported_user_id,
            'reportable_type' => $request->reportable_type,
            'reportable_id' => $request->reportable_id,
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
            'priority' => $request->priority,
            'evidence' => $request->evidence ?? [],
        ]);

        // Log the report creation
        SecurityLog::create([
            'user_id' => Auth::id(),
            'event_type' => 'report_created',
            'event_category' => 'moderation',
            'description' => "New moderation report created",
            'metadata' => [
                'report_id' => $report->id,
                'reason' => $request->reason,
                'priority' => $request->priority,
            ],
            'severity' => 'info',
        ]);

        return back()->with('success', 'Report submitted successfully.');
    }

    /**
     * Bulk actions on reports
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', Rule::in(['assign', 'dismiss', 'change_priority'])],
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:moderation_reports,id',
            'moderator_id' => 'required_if:action,assign|exists:users,id',
            'priority' => 'required_if:action,change_priority|in:low,normal,high,urgent',
            'reason' => 'required_if:action,dismiss|string|max:500',
        ]);

        $reports = ModerationReport::whereIn('id', $request->report_ids)->get();

        foreach ($reports as $report) {
            switch ($request->action) {
                case 'assign':
                    $moderator = User::find($request->moderator_id);
                    if ($moderator && $moderator->hasRole(['admin', 'moderator'])) {
                        $report->assignTo($moderator);
                    }
                    break;

                case 'dismiss':
                    $report->dismiss($request->reason);
                    break;

                case 'change_priority':
                    $report->update(['priority' => $request->priority]);
                    break;
            }
        }

        return back()->with('success', 'Bulk action completed successfully.');
    }
}
