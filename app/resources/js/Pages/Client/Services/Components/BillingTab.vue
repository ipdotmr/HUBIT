<script setup>
const props = defineProps({
    service: Object,
});
</script>

<template>
    <div class="space-y-6">
        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Billing Information</h3>
            <dl class="mt-4 space-y-4">
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Billing Cycle</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">{{ service.billing_cycle || 'monthly' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Recurring Amount</dt>
                    <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">${{ service.recurring_amount || '0.00' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Next Due Date</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ service.next_due_at ? new Date(service.next_due_at).toLocaleDateString() : 'N/A' }}
                    </dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Order ID</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">#{{ service.order_id || 'N/A' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                    <dd class="text-sm text-gray-900 dark:text-gray-100">
                        {{ new Date(service.created_at).toLocaleDateString() }}
                    </dd>
                </div>
            </dl>
        </div>

        <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Payment History</h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                View your complete payment history and invoices in the billing section.
            </p>
            <div class="mt-4">
                <a
                    href="/dashboard/billing"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    View Invoices
                </a>
            </div>
        </div>
    </div>
</template>
