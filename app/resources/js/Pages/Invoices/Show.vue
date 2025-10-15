<template>
    <Head :title="'Invoice ' + invoice.number" />
    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Invoice {{ invoice.number }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Issued: {{ formatDate(invoice.issue_date) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900">{{ invoice.total }} {{ invoice.currency }}</div>
                        <span :class="{
                            'bg-green-100 text-green-800': invoice.status === 'paid',
                            'bg-yellow-100 text-yellow-800': invoice.status === 'open',
                            'bg-red-100 text-red-800': invoice.status === 'overdue'
                        }" class="mt-2 inline-block px-3 py-1 text-sm rounded-full">
                            {{ invoice.status }}
                        </span>
                    </div>
                </div>

                <div v-if="invoice.status !== 'paid'" class="border-t pt-6">
                    <h3 class="text-lg font-semibold mb-4">Payment Options</h3>
                    
                    <div class="space-y-4">
                        <button @click="showBankTransferForm = !showBankTransferForm" 
                                class="w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">Bank Transfer</div>
                                    <div class="text-sm text-gray-600">Pay via bank transfer in any supported currency</div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>

                        <div v-if="showBankTransferForm" class="bg-gray-50 p-4 rounded-lg">
                            <form @submit.prevent="submitBankTransfer">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Select Currency</label>
                                        <select v-model="bankTransferForm.currency" required
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            <option v-for="curr in enabledCurrencies" :key="curr.code" :value="curr.code">
                                                {{ curr.code }} - {{ curr.name }}
                                            </option>
                                        </select>
                                    </div>
                                    
                                    <div v-if="bankTransferForm.currency && paymentAccounts[bankTransferForm.currency]">
                                        <label class="block text-sm font-medium text-gray-700">Bank Account</label>
                                        <select v-model="bankTransferForm.payment_account_id" required
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            <option v-for="acc in paymentAccounts[bankTransferForm.currency]" 
                                                    :key="acc.id" :value="acc.id">
                                                {{ acc.details.bank_name }} - {{ acc.details.account_number }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input v-model="bankTransferForm.amount" type="number" step="0.01" required
                                               class="mt-1 block w-full rounded-md border-gray-300" />
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Upload Proof (optional)</label>
                                        <input @change="handleFileUpload" type="file" accept=".pdf,.jpg,.jpeg,.png"
                                               class="mt-1 block w-full" />
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Submit Payment
                                    </button>
                                </div>
                            </form>
                        </div>

                        <button @click="showCashForm = !showCashForm" 
                                class="w-full p-4 text-left border rounded-lg hover:bg-gray-50 transition">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium">Cash Payment</div>
                                    <div class="text-sm text-gray-600">Pay in cash at our office</div>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </button>

                        <div v-if="showCashForm" class="bg-gray-50 p-4 rounded-lg">
                            <form @submit.prevent="submitCash">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Currency</label>
                                        <select v-model="cashForm.currency" required
                                                class="mt-1 block w-full rounded-md border-gray-300">
                                            <option v-for="curr in enabledCurrencies" :key="curr.code" :value="curr.code">
                                                {{ curr.code }} - {{ curr.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Amount</label>
                                        <input v-model="cashForm.amount" type="number" step="0.01" required
                                               class="mt-1 block w-full rounded-md border-gray-300" />
                                    </div>

                                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        Submit Cash Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    invoice: Object,
    enabledCurrencies: Array,
    paymentAccounts: Object,
    defaultCurrency: String
});

const showBankTransferForm = ref(false);
const showCashForm = ref(false);

const bankTransferForm = ref({
    payment_account_id: null,
    amount: props.invoice.total,
    currency: props.defaultCurrency,
    evidence: []
});

const cashForm = ref({
    amount: props.invoice.total,
    currency: props.defaultCurrency,
    evidence: []
});

const formatDate = (date) => new Date(date).toLocaleDateString();

const handleFileUpload = (event) => {
    bankTransferForm.value.evidence = event.target.files;
};

const submitBankTransfer = () => {
    router.post(route('invoices.pay.bank-transfer', props.invoice.id), bankTransferForm.value);
};

const submitCash = () => {
    router.post(route('invoices.pay.cash', props.invoice.id), cashForm.value);
};
</script>
