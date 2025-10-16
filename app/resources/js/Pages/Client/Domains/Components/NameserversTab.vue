<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    domain: Object,
});

const form = useForm({
    nameservers: props.domain.nameservers || ['', ''],
});

const addNameserver = () => {
    if (form.nameservers.length < 5) {
        form.nameservers.push('');
    }
};

const removeNameserver = (index) => {
    if (form.nameservers.length > 2) {
        form.nameservers.splice(index, 1);
    }
};

const submit = () => {
    const nameservers = form.nameservers.filter(ns => ns.trim() !== '');
    
    if (nameservers.length < 2) {
        alert('At least 2 nameservers are required');
        return;
    }
    
    form.transform(data => ({
        nameservers: nameservers
    })).put(route('client.domains.nameservers.update', props.domain.id), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Nameservers update queued successfully');
        },
        onError: (errors) => {
            console.error('Validation errors:', errors);
        },
    });
};

const validateNs = (ns) => {
    if (!ns) return true;
    const pattern = /^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*$/i;
    return pattern.test(ns);
};
</script>

<template>
    <div class="space-y-6">
        <div class="rounded-lg bg-blue-50 p-4">
            <p class="text-sm text-blue-700">
                {{ $t('domains.nameservers_help') }}
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div v-for="(ns, index) in form.nameservers" :key="index" class="flex items-start space-x-2">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700">
                        {{ $t('domains.nameserver') }} {{ index + 1 }}
                    </label>
                    <input
                        v-model="form.nameservers[index]"
                        type="text"
                        :placeholder="`ns${index + 1}.example.com`"
                        :class="[
                            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500',
                            !validateNs(form.nameservers[index]) && form.nameservers[index] ? 'border-red-300' : ''
                        ]"
                    />
                    <p v-if="!validateNs(form.nameservers[index]) && form.nameservers[index]" class="mt-1 text-sm text-red-600">
                        {{ $t('domains.invalid_nameserver_format') }}
                    </p>
                </div>
                <button
                    v-if="form.nameservers.length > 2"
                    type="button"
                    class="mt-7 rounded-md bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700"
                    @click="removeNameserver(index)"
                >
                    {{ $t('common.remove') }}
                </button>
            </div>

            <div v-if="form.nameservers.length < 5" class="flex justify-start">
                <button
                    type="button"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    @click="addNameserver"
                >
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ $t('domains.add_nameserver') }}
                </button>
            </div>

            <div v-if="form.errors" class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            {{ $t('common.validation_errors') }}
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc space-y-1 pl-5">
                                <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 border-t pt-4">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50"
                >
                    <svg v-if="form.processing" class="mr-2 h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ form.processing ? $t('common.saving') : $t('common.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</template>
