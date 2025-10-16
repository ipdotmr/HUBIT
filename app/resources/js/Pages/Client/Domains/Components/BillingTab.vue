<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    domain: Object,
    renewalPricing: Object,
});

const selectedYears = ref(1);

const form = useForm({
    years: 1,
});

const calculatePrice = (years) => {
    if (!props.renewalPricing) return { total: 0, currency: 'USD' };
    const pricePerYear = props.renewalPricing.price_per_year || 12;
    const subtotal = pricePerYear * years;
    const tax = subtotal * (props.renewalPricing.tax_rate || 0.15);
    return {
        subtotal: subtotal.toFixed(2),
        tax: tax.toFixed(2),
        total: (subtotal + tax).toFixed(2),
        currency: props.renewalPricing.currency || 'USD',
    };
};

const renewDomain = () => {
    form.years = selectedYears.value;
    form.post(route('client.domains.renew', props.domain.id), {
        onSuccess: () => {
            alert('Renewal invoice created successfully');
        },
    });
};
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $t('domains.renewal') }}
            </h3>
            <p class="mt-2 text-sm text-gray-500">
                {{ $t('domains.renewal_description') }}
            </p>

            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <button
                    v-for="years in [1, 2, 3]"
                    :key="years"
                    type="button"
                    :class="[
                        selectedYears === years
                            ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500'
                            : 'border-gray-300 bg-white hover:bg-gray-50',
                        'relative rounded-lg border p-4 flex flex-col items-center focus:outline-none'
                    ]"
                    @click="selectedYears = years"
                >
                    <span class="text-2xl font-bold text-gray-900">{{ years }}</span>
                    <span class="text-sm text-gray-500">
                        {{ years === 1 ? $t('common.year') : $t('common.years') }}
                    </span>
                    <span class="mt-2 text-lg font-semibold text-blue-600">
                        {{ calculatePrice(years).total }} {{ calculatePrice(years).currency }}
                    </span>
                </button>
            </div>

            <div class="mt-6 rounded-lg bg-gray-50 p-4">
                <dl class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">{{ $t('domains.subtotal') }}</dt>
                        <dd class="font-medium text-gray-900">
                            {{ calculatePrice(selectedYears).subtotal }} {{ calculatePrice(selectedYears).currency }}
                        </dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-600">{{ $t('domains.tax') }}</dt>
                        <dd class="font-medium text-gray-900">
                            {{ calculatePrice(selectedYears).tax }} {{ calculatePrice(selectedYears).currency }}
                        </dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-semibold">
                        <dt class="text-gray-900">{{ $t('domains.total') }}</dt>
                        <dd class="text-blue-600">
                            {{ calculatePrice(selectedYears).total }} {{ calculatePrice(selectedYears).currency }}
                        </dd>
                    </div>
                </dl>
            </div>

            <button
                type="button"
                :disabled="form.processing"
                class="mt-6 w-full inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                @click="renewDomain"
            >
                <svg v-if="form.processing" class="mr-2 h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ form.processing ? $t('common.processing') : $t('domains.create_renewal_invoice') }}
            </button>
        </div>

        <div class="rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $t('domains.renewal_history') }}
            </h3>
            <div v-if="domain.renewals && domain.renewals.length > 0" class="mt-4 space-y-4">
                <div
                    v-for="renewal in domain.renewals"
                    :key="renewal.id"
                    class="flex items-center justify-between border-b border-gray-200 pb-4 last:border-0"
                >
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ renewal.years }} {{ renewal.years === 1 ? $t('common.year') : $t('common.years') }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ new Date(renewal.created_at).toLocaleDateString() }}
                        </p>
                    </div>
                    <span
                        :class="[
                            renewal.status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800',
                            'inline-flex rounded-full px-2 py-1 text-xs font-semibold'
                        ]"
                    >
                        {{ $t(`domains.renewal_status_${renewal.status}`) }}
                    </span>
                </div>
            </div>
            <p v-else class="mt-4 text-sm text-gray-500">
                {{ $t('domains.no_renewal_history') }}
            </p>
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
                        {{ $t('domains.expiry_warning', { date: new Date(domain.expires_at).toLocaleDateString() }) }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
