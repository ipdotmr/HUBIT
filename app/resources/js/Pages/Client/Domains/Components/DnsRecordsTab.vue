<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    domain: Object,
});

const showAddForm = ref(false);
const editingRecord = ref(null);

const form = useForm({
    type: 'A',
    host: '',
    value: '',
    ttl: 3600,
    priority: null,
});

const recordTypes = ['A', 'AAAA', 'CNAME', 'MX', 'TXT', 'CAA'];

const openAddForm = () => {
    form.reset();
    form.type = 'A';
    editingRecord.value = null;
    showAddForm.value = true;
};

const editRecord = (record) => {
    form.type = record.type;
    form.host = record.host;
    form.value = record.value;
    form.ttl = record.ttl;
    form.priority = record.priority;
    editingRecord.value = record;
    showAddForm.value = true;
};

const submitRecord = () => {
    if (editingRecord.value) {
        form.put(route('client.domains.dns.update', [props.domain.id, editingRecord.value.id]), {
            preserveScroll: true,
            onSuccess: () => {
                showAddForm.value = false;
                router.reload({ only: ['domain'] });
            },
        });
    } else {
        form.post(route('client.domains.dns.store', props.domain.id), {
            preserveScroll: true,
            onSuccess: () => {
                showAddForm.value = false;
                router.reload({ only: ['domain'] });
            },
        });
    }
};

const deleteRecord = (record) => {
    if (confirm('Are you sure you want to delete this DNS record?')) {
        router.delete(route('client.domains.dns.destroy', [props.domain.id, record.id]), {
            preserveScroll: true,
        });
    }
};

const validateValue = () => {
    const { type, value } = form;
    if (!value) return true;
    
    if (type === 'A') {
        const ipv4 = /^(\d{1,3}\.){3}\d{1,3}$/;
        return ipv4.test(value);
    }
    
    if (type === 'AAAA') {
        const ipv6 = /^([0-9a-fA-F]{1,4}:){7}[0-9a-fA-F]{1,4}$/;
        return ipv6.test(value) || value.includes('::');
    }
    
    return true;
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $t('domains.dns_records') }}
            </h3>
            <button
                type="button"
                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                @click="openAddForm"
            >
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ $t('domains.add_dns_record') }}
            </button>
        </div>

        <div v-if="showAddForm" class="rounded-lg border border-gray-200 bg-gray-50 p-4">
            <h4 class="mb-4 text-sm font-medium text-gray-900">
                {{ editingRecord ? $t('domains.edit_dns_record') : $t('domains.add_dns_record') }}
            </h4>
            
            <form @submit.prevent="submitRecord" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $t('domains.record_type') }}
                        </label>
                        <select
                            v-model="form.type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        >
                            <option v-for="type in recordTypes" :key="type" :value="type">
                                {{ type }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $t('domains.host') }}
                        </label>
                        <input
                            v-model="form.host"
                            type="text"
                            :placeholder="'@'"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ $t('domains.value') }}
                    </label>
                    <input
                        v-model="form.value"
                        type="text"
                        :class="[
                            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                            !validateValue() ? 'border-red-300' : ''
                        ]"
                    />
                    <p v-if="!validateValue()" class="mt-1 text-sm text-red-600">
                        {{ $t('domains.invalid_value_format') }}
                    </p>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div v-if="form.type === 'MX' || form.type === 'SRV'">
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $t('domains.priority') }}
                        </label>
                        <input
                            v-model.number="form.priority"
                            type="number"
                            min="0"
                            max="65535"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ $t('domains.ttl') }}
                        </label>
                        <input
                            v-model.number="form.ttl"
                            type="number"
                            min="60"
                            max="86400"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        @click="showAddForm = false"
                    >
                        {{ $t('common.cancel') }}
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                    >
                        {{ form.processing ? $t('common.saving') : $t('common.save') }}
                    </button>
                </div>
            </form>
        </div>

        <div v-if="domain.dns_records && domain.dns_records.length > 0" class="overflow-hidden rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ $t('domains.type') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ $t('domains.host') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ $t('domains.value') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ $t('domains.ttl') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                            {{ $t('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="record in domain.dns_records" :key="record.id" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                            {{ record.type }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ record.host }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span class="truncate">{{ record.value }}</span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                            {{ record.ttl }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                            <button
                                type="button"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                                @click="editRecord(record)"
                            >
                                {{ $t('common.edit') }}
                            </button>
                            <button
                                type="button"
                                class="text-red-600 hover:text-red-900"
                                @click="deleteRecord(record)"
                            >
                                {{ $t('common.delete') }}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="rounded-lg border border-gray-200 bg-gray-50 p-12 text-center">
            <p class="text-gray-500">
                {{ $t('domains.no_dns_records') }}
            </p>
        </div>
    </div>
</template>
