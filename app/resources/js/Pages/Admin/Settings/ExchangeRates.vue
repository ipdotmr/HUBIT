<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    rates: Array,
    settings: Object,
    auditLogs: Array
});

const formatDate = (date) => new Date(date).toLocaleString();
</script>

<template>
    <Head title="Exchange Rates - Settings" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Exchange Rates</h3>
                    <p class="text-muted">View and manage currency exchange rates for multi-currency billing</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-sync me-2"></i>Refresh Rates
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Current Rates</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Pair</th>
                                    <th>Rate</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="rate in rates" :key="rate.id">
                                    <td><strong>{{ rate.base }} → {{ rate.quote }}</strong></td>
                                    <td>{{ rate.rate }}</td>
                                    <td class="text-muted">{{ formatDate(rate.fetched_at) }}</td>
                                </tr>
                                <tr v-if="!rates || rates.length === 0">
                                    <td colspan="3" class="text-center text-muted py-4">No exchange rates available</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
