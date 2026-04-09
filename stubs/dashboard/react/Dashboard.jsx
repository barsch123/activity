import React, { useState, useEffect } from 'react';
import Layout from '@/Layouts/Layout';

export default function Dashboard() {
    const [activities, setActivities] = useState([]);
    const [loading, setLoading] = useState(true);
    const [stats, setStats] = useState({
        total: 0,
        created: 0,
        updated: 0,
        deleted: 0,
    });
    const [currentPage, setCurrentPage] = useState(1);
    const [pagination, setPagination] = useState({});

    useEffect(() => {
        fetchActivities(currentPage);
    }, [currentPage]);

    const fetchActivities = async (page = 1) => {
        try {
            setLoading(true);
            const res = await fetch(`/app/logs/api?page=${page}`);
            const data = await res.json();
            
            setActivities(data.data);
            setPagination(data.pagination);
            
            // Calculate stats
            const response = await fetch('/app/logs/stats');
            const statsData = await response.json();
            setStats(statsData);
        } catch (error) {
            console.error('Failed to fetch activities:', error);
        } finally {
            setLoading(false);
        }
    };

    const eventBadgeColor = (event) => {
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
    };

    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };

    return (
        <Layout>
            <div className="container mx-auto px-4 py-8">
                <div className="mb-8">
                    <h1 className="text-3xl font-bold text-gray-900 dark:text-white">
                        Activity Dashboard
                    </h1>
                    <p className="text-gray-600 dark:text-gray-400 mt-2">
                        Track all model changes and audit logs
                    </p>
                </div>

                {/* Stats Cards */}
                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Total Activities
                        </div>
                        <div className="text-2xl font-bold text-gray-900 dark:text-white mt-2">
                            {stats.total}
                        </div>
                    </div>
                    
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Created
                        </div>
                        <div className="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">
                            {stats.created}
                        </div>
                    </div>
                    
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Updated
                        </div>
                        <div className="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">
                            {stats.updated}
                        </div>
                    </div>
                    
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div className="text-sm font-medium text-gray-600 dark:text-gray-400">
                            Deleted
                        </div>
                        <div className="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">
                            {stats.deleted}
                        </div>
                    </div>
                </div>

                {/* Activity Table */}
                <div className="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead className="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Event
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Subject
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Causer
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Action
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                        Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-gray-600">
                                {loading ? (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-8 text-center">
                                            <p className="text-gray-500 dark:text-gray-400">Loading...</p>
                                        </td>
                                    </tr>
                                ) : activities.length === 0 ? (
                                    <tr>
                                        <td colSpan="5" className="px-6 py-8 text-center">
                                            <p className="text-gray-500 dark:text-gray-400">No activities yet</p>
                                        </td>
                                    </tr>
                                ) : (
                                    activities.map((activity) => (
                                        <tr key={activity.id} className="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${eventBadgeColor(activity.event)}`}>
                                                    {activity.event.charAt(0).toUpperCase() + activity.event.slice(1)}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <div className="text-sm text-gray-900 dark:text-white">
                                                    {activity.subject_type.split('\\').pop()} #{activity.subject_id}
                                                </div>
                                                <div className="text-xs text-gray-500 dark:text-gray-400">
                                                    {activity.subject_type}
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                {activity.causer ? (
                                                    <>
                                                        <div className="text-sm text-gray-900 dark:text-white">
                                                            {activity.causer.name || 'Unknown'}
                                                        </div>
                                                        <div className="text-xs text-gray-500 dark:text-gray-400">
                                                            {activity.causer_type.split('\\').pop()}
                                                        </div>
                                                    </>
                                                ) : (
                                                    <span className="text-sm text-gray-500 dark:text-gray-400">System</span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span className="text-sm text-gray-900 dark:text-white">
                                                    {activity.action || '—'}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                <time title={activity.created_at}>
                                                    {formatDate(activity.created_at)}
                                                </time>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {pagination.last_page > 1 && (
                        <div className="bg-gray-50 dark:bg-gray-700 px-6 py-4 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between">
                            <button
                                onClick={() => setCurrentPage(Math.max(1, currentPage - 1))}
                                disabled={currentPage === 1}
                                className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                            >
                                Previous
                            </button>
                            <span className="text-sm text-gray-600 dark:text-gray-400">
                                Page {pagination.current_page} of {pagination.last_page}
                            </span>
                            <button
                                onClick={() => setCurrentPage(Math.min(pagination.last_page, currentPage + 1))}
                                disabled={currentPage === pagination.last_page}
                                className="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>
                    )}
                </div>
            </div>
        </Layout>
    );
}
