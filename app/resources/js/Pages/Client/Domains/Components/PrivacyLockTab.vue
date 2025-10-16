<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    domain: Object,
});

const privacyForm = useForm({
    enabled: props.domain.privacy_enabled,
});

const lockForm = useForm({
    enabled: props.domain.lock_enabled,
});

const togglePrivacy = () => {
    privacyForm.put(route('client.domains.privacy.update', props.domain.id), {
        preserveScroll: true,
    });
};

const toggleLock = () => {
    lockForm.put(route('client.domains.lock.update', props.domain.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $t('domains.privacy_protection') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $t('domains.privacy_description') }}
                    </p>
                </div>
                <button
                    type="button"
                    :disabled="privacyForm.processing"
                    :class="[
                        privacyForm.enabled ? 'bg-blue-600' : 'bg-gray-200',
                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50'
                    ]"
                    @click="privacyForm.enabled = !privacyForm.enabled; togglePrivacy()"
                >
                    <span
                        :class="[
                            privacyForm.enabled ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                    />
                </button>
            </div>
            <div v-if="privacyForm.processing" class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $t('common.processing') }}
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h3 class="text-lg font-medium text-gray-900">
                        {{ $t('domains.domain_lock') }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ $t('domains.lock_description') }}
                    </p>
                </div>
                <button
                    type="button"
                    :disabled="lockForm.processing"
                    :class="[
                        lockForm.enabled ? 'bg-blue-600' : 'bg-gray-200',
                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50'
                    ]"
                    @click="lockForm.enabled = !lockForm.enabled; toggleLock()"
                >
                    <span
                        :class="[
                            lockForm.enabled ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                    />
                </button>
            </div>
            <div v-if="lockForm.processing" class="mt-4">
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ $t('common.processing') }}
                </div>
            </div>
        </div>

        <div class="rounded-lg bg-yellow-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        {{ $t('domains.security_warning') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
