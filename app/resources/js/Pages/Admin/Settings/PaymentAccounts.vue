<script setup>
import { Head } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    accounts: Array,
    settings: Object,
    auditLogs: Array
});
</script>

<template>
    <Head title="Payment Accounts - Settings" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">Payment Accounts</h3>
                    <p class="text-muted">Manage payment gateway accounts and configurations</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Account
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Configured Accounts</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Gateway</th>
                                    <th>Account Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="account in accounts" :key="account.id">
                                    <td><strong>{{ account.gateway }}</strong></td>
                                    <td>{{ account.name }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': account.status === 'active',
                                            'bg-danger': account.status === 'inactive'
                                        }">
                                            {{ account.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="!accounts || accounts.length === 0">
                                    <td colspan="4" class="text-center text-muted py-4">No payment accounts configured</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
