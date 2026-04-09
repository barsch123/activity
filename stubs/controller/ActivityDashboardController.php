<?php

namespace App\Http\Controllers;

use Gottvergessen\Activity\Models\Activity;
use Illuminate\Pagination\Paginator;

class ActivityDashboardController extends Controller
{
    public function index()
    {
        $activities = Activity::with(['subject', 'causer'])
            ->latest('created_at')
            ->paginate(25);

        return view('activity.dashboard', [
            'activities' => $activities,
        ]);
    }

    public function apiList()
    {
        $activities = Activity::with(['subject', 'causer'])
            ->latest('created_at')
            ->paginate(25);

        return response()->json([
            'data' => $activities->items(),
            'pagination' => [
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
            ],
        ]);
    }

    public function show(Activity $activity)
    {
        $activity->load(['subject', 'causer']);

        return response()->json($activity);
    }

    public function delete(Activity $activity)
    {
        $activity->delete();

        return response()->json(['message' => 'Activity deleted']);
    }

    public function filterByEvent(string $event)
    {
        $activities = Activity::forEvent($event)
            ->with(['subject', 'causer'])
            ->latest('created_at')
            ->paginate(25);

        return response()->json([
            'data' => $activities->items(),
            'pagination' => [
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
            ],
        ]);
    }

    public function filterBySubject(string $subjectType)
    {
        $activities = Activity::where('subject_type', $subjectType)
            ->with(['subject', 'causer'])
            ->latest('created_at')
            ->paginate(25);

        return response()->json([
            'data' => $activities->items(),
            'pagination' => [
                'total' => $activities->total(),
                'per_page' => $activities->perPage(),
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
            ],
        ]);
    }
}
