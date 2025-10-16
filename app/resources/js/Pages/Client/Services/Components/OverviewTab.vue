<script setup>
const props = defineProps({
    service: Object,
    panelUrl: String,
});
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Service Details</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Product</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ service.product?.name || 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Provisioner</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ service.provisioner || 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Billing Cycle</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ service.billing_cycle || 'monthly' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Next Due Date</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ service.next_due_at ? new Date(service.next_due_at).toLocaleDateString() : 'N/A' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Recurring Amount</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            ${{ service.recurring_amount || '0.00' }}
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Credentials</h3>
                <div v-if="service.status === 'active' && service.provision_ref" class="space-y-3">
                    <div v-if="service.provision_ref.username">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Username</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900 dark:text-gray-100">{{ service.provision_ref.username }}</dd>
                    </div>
                    <div v-if="service.provision_ref.domain">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Domain</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900 dark:text-gray-100">{{ service.provision_ref.domain }}</dd>
                    </div>
                    <div v-if="service.provision_ref.ip">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
                        <dd class="mt-1 font-mono text-sm text-gray-900 dark:text-gray-100">{{ service.provision_ref.ip }}</dd>
                    </div>
                    <div v-if="panelUrl" class="pt-4">
                        <a
                            :href="panelUrl"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Open Control Panel
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                    Service must be active to view credentials
                </div>
            </div>
        </div>

        <div v-if="service.provision_ref?.usage" class="mt-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Resource Usage</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Disk Usage</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ service.provision_ref.usage.disk || '0 MB' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Bandwidth</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ service.provision_ref.usage.bandwidth || '0 GB' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Inodes</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ service.provision_ref.usage.inodes || '0' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
