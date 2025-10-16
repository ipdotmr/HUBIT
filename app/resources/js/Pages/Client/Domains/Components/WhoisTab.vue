<script setup>
const props = defineProps({
    domain: Object,
});

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text);
};

const exportPdf = () => {
    window.print();
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-end space-x-3">
            <button
                type="button"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                @click="copyToClipboard(JSON.stringify(domain.whois_data, null, 2))"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
                {{ $t('common.copy') }}
            </button>
            <button
                type="button"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                @click="exportPdf"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                {{ $t('common.export_pdf') }}
            </button>
        </div>

        <div v-if="domain.whois_data" class="rounded-lg border border-gray-200 bg-white p-6">
            <h3 class="mb-4 text-lg font-medium text-gray-900">
                {{ $t('domains.whois_information') }}
            </h3>
            
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div v-for="(value, key) in domain.whois_data" :key="key" class="border-b border-gray-200 pb-4">
                    <dt class="text-sm font-medium text-gray-500">
                        {{ key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) }}
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ value || '-' }}
                    </dd>
                </div>
            </dl>
        </div>

        <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-4 text-gray-500">
                {{ $t('domains.no_whois_data') }}
            </p>
        </div>

        <div class="rounded-lg bg-blue-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        {{ $t('domains.whois_note') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
