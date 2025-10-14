<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    services: Array,
    invoices: Array,
    tickets: Array,
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Active Services
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                {{ stats?.active_services || 0 }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Credit Balance
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                ${{ stats?.credit_balance || '0.00' }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Unpaid Invoices
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                {{ stats?.unpaid_invoices || 0 }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center p-4 bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:text-purple-100 dark:bg-purple-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 5a2 2 0 012-2h7a2 2 0 012 2v4a2 2 0 01-2 2H9l-3 3v-3H4a2 2 0 01-2-2V5z"/>
                                <path d="M15 7v2a4 4 0 01-4 4H9.828l-1.766 1.767c.28.149.599.233.938.233h2l3 3v-3h2a2 2 0 002-2V9a2 2 0 00-2-2h-1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                                Open Tickets
                            </p>
                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                {{ stats?.open_tickets || 0 }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 mb-8 md:grid-cols-2">
                    <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                Recent Services
                            </h3>
                        </div>
                        <div class="p-4">
                            <div v-if="services && services.length > 0" class="space-y-3">
                                <div v-for="service in services.slice(0, 5)" :key="service.id" 
                                     class="flex items-center justify-between p-3 border border-gray-200 rounded dark:border-gray-700">
                                    <div>
                                        <p class="font-medium text-gray-700 dark:text-gray-200">
                                            {{ service.product?.name }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ service.provision_ref?.domain || 'Pending Setup' }}
                                        </p>
                                    </div>
                                    <span :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100': service.status === 'active',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100': service.status === 'pending',
                                        'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100': service.status === 'suspended'
                                    }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ service.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 dark:text-gray-400">
                                No services yet
                            </div>
                            <Link href="/client/services" 
                                  class="block mt-4 text-sm text-center text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                View All Services →
                            </Link>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm dark:bg-gray-800">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">
                                Recent Invoices
                            </h3>
                        </div>
                        <div class="p-4">
                            <div v-if="invoices && invoices.length > 0" class="space-y-3">
                                <div v-for="invoice in invoices.slice(0, 5)" :key="invoice.id"
                                     class="flex items-center justify-between p-3 border border-gray-200 rounded dark:border-gray-700">
                                    <div>
                                        <p class="font-medium text-gray-700 dark:text-gray-200">
                                            #{{ invoice.number }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            ${{ invoice.total }} - Due {{ invoice.due_date }}
                                        </p>
                                    </div>
                                    <span :class="{
                                        'bg-green-100 text-green-800': invoice.status === 'paid',
                                        'bg-yellow-100 text-yellow-800': invoice.status === 'open',
                                        'bg-red-100 text-red-800': invoice.status === 'overdue'
                                    }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ invoice.status }}
                                    </span>
                                </div>
                            </div>
                            <div v-else class="text-center text-gray-500 dark:text-gray-400">
                                No invoices yet
                            </div>
                            <Link href="/client/invoices" 
                                  class="block mt-4 text-sm text-center text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                View All Invoices →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
