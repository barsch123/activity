<template>
    <Layout>
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Activity Dashboard
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    Track all model changes and audit logs
                </p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Total Activities
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                        {{ stats.total }}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Created
                    </div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">
                        {{ stats.created }}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Updated
                    </div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                        {{ stats.updated }}
                    </div>
                </div>
                
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                        Deleted
                    </div>
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">
                        {{ stats.deleted }}
                    </div>
                </div>
            </div>

            <!-- Activity Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Event
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Causer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Action
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                    Date
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                            <template v-if="loading">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">Loading...</p>
                                    </td>
                                </tr>
                            </template>
                            <template v-else-if="activities.length === 0">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center">
                                        <p class="text-gray-500 dark:text-gray-400">No activities yet</p>
                                    </td>
                                </tr>
                            </template>
                            <template v-else>
                                <tr v-for="activity in activities" :key="activity.id" class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span :class="`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${eventBadgeColor(activity.event)}`">
                                            {{ activity.event.charAt(0).toUpperCase() + activity.event.slice(1) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ activity.subject_type.split('\\').pop() }} #{{ activity.subject_id }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ activity.subject_type }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <template v-if="activity.causer">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ activity.causer.name || 'Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ activity.causer_type.split('\\').pop() }}
                                            </div>
                                        </template>
                                        <template v-else>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">System</span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900 dark:text-white">
                                            {{ activity.action || '—' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <time :title="activity.created_at">
                                            {{ formatDate(activity.created_at) }}
                                        </time>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="pagination.last_page > 1" class="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                    <button
                        @click="setCurrentPage(Math.max(1, currentPage - 1))"
                        :disabled="currentPage === 1"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        Previous
                    </button>
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Page {{ currentPage }} of {{ pagination.last_page }}
                    </span>
                    <button
                        @click="setCurrentPage(Math.min(pagination.last_page, currentPage + 1))"
                        :disabled="currentPage === pagination.last_page"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </Layout>
</template>

<script>
export default {
    data() {
        return {
            activities: [],
            loading: true,
            stats: {
                total: 0,
                created: 0,
                updated: 0,
                deleted: 0,
            },
            currentPage: 1,
            pagination: {},
        };
    },
    mounted() {
        this.fetchActivities(this.currentPage);
    },
    watch: {
        currentPage() {
            this.fetchActivities(this.currentPage);
        },
    },
    methods: {
        async fetchActivities(page = 1) {
            try {
                this.loading = true;
                const res = await fetch(`/app/logs/api?page=${page}`);
                const data = await res.json();
                
                this.activities = data.data;
                this.pagination = data.pagination;
                
                // Calculate stats
                const response = await fetch('/app/logs/stats');
                const statsData = await response.json();
                this.stats = statsData;
            } catch (error) {
                console.error('Failed to fetch activities:', error);
            } finally {
                this.loading = false;
            }
        },
        eventBadgeColor(event) {
            switch (event) {
                case 'created':
                    return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
                case 'updated':
                    return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
                case 'deleted':
                    return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
                default:
                    return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
            }
        },
        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            });
        },
        setCurrentPage(page) {
            this.currentPage = page;
        },
    },
};
</script>
