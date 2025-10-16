<script setup>
const props = defineProps({
    domain: Object,
});

const formatDate = (date) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString();
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};
</script>

<template>
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-medium text-gray-500">
                    {{ $t('domains.domain_name') }}
                </h3>
                <div class="mt-2 flex items-center">
                    <p class="text-lg font-semibold text-gray-900">{{ domain.domain }}</p>
                    <button
                        type="button"
                        class="ml-2 text-gray-400 hover:text-gray-600"
                        @click="copyToClipboard(domain.domain)"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-medium text-gray-500">
                    {{ $t('domains.registrar') }}
                </h3>
                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ domain.registrar }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-medium text-gray-500">
                    {{ $t('domains.registered_at') }}
                </h3>
                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ formatDate(domain.registered_at) }}
                </p>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <h3 class="text-sm font-medium text-gray-500">
                    {{ $t('domains.expires_at') }}
                </h3>
                <p class="mt-2 text-lg font-semibold text-gray-900">
                    {{ formatDate(domain.expires_at) }}
                </p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <h3 class="mb-4 text-sm font-medium text-gray-900">
                {{ $t('domains.nameservers') }}
            </h3>
            <div v-if="domain.nameservers && domain.nameservers.length > 0" class="space-y-2">
                <div
                    v-for="(ns, index) in domain.nameservers"
                    :key="index"
                    class="flex items-center justify-between rounded bg-gray-50 px-3 py-2"
                >
                    <span class="font-mono text-sm text-gray-700">{{ ns }}</span>
                    <button
                        type="button"
                        class="text-gray-400 hover:text-gray-600"
                        @click="copyToClipboard(ns)"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                </div>
            </div>
            <p v-else class="text-sm text-gray-500">
                {{ $t('domains.no_nameservers') }}
            </p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500">
                        {{ $t('domains.privacy_protection') }}
                    </h3>
                    <span
                        :class="domain.privacy_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                        class="rounded-full px-2 py-1 text-xs font-semibold"
                    >
                        {{ domain.privacy_enabled ? $t('common.enabled') : $t('common.disabled') }}
                    </span>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-medium text-gray-500">
                        {{ $t('domains.domain_lock') }}
                    </h3>
                    <span
                        :class="domain.lock_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                        class="rounded-full px-2 py-1 text-xs font-semibold"
                    >
                        {{ domain.lock_enabled ? $t('common.enabled') : $t('common.disabled') }}
                    </span>
                </div>
            </div>
        </div>

        <div v-if="domain.auto_renew" class="rounded-lg bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ $t('domains.auto_renew_enabled') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
