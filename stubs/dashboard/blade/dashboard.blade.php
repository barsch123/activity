<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Activity Dashboard</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Track all model changes and audit logs</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Activities</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                {{ $activities->total() }}
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Created</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">
                {{ \Gottvergessen\Activity\Models\Activity::forEvent('created')->count() }}
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Updated</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                {{ \Gottvergessen\Activity\Models\Activity::forEvent('updated')->count() }}
            </div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Deleted</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">
                {{ \Gottvergessen\Activity\Models\Activity::forEvent('deleted')->count() }}
            </div>
        </div>
    </div>

    {{-- Activity Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Causer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($activity->event === 'created')
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($activity->event === 'updated')
                                        bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($activity->event === 'deleted')
                                        bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else
                                        bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @endif
                                ">
                                    {{ ucfirst($activity->event) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if($activity->subject)
                                        <a href="{{ route('activity.show', $activity->id) }}" class="hover:underline">
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </a>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->subject_type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->causer)
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $activity->causer->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ class_basename($activity->causer_type) }}</div>
                                @else
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $activity->action ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                <time title="{{ $activity->created_at }}">
                                    {{ $activity->created_at->diffForHumans() }}
                                </time>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No activities yet
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
</body>
</html>
