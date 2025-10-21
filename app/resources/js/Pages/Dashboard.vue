<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    services: Array,
    invoices: Array,
    tickets: Array,
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <ClientLayout>
        <div class="mb-4">
            <h2>Dashboard</h2>
            <p class="text-muted">Welcome to your HUBIT dashboard</p>
        </div>

        <div class="row">
            <div class="col">
                <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 text-center">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Active Services</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-primary rounded-circle fs-22">
                                            <i class="ti ti-server"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ stats?.active_services || 0 }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Total active services</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Credit Balance</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-success rounded-circle fs-22">
                                            <i class="ti ti-wallet"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">${{ stats?.credit_balance || '0.00' }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Available balance</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Unpaid Invoices</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-warning rounded-circle fs-22">
                                            <i class="ti ti-file-invoice"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ stats?.unpaid_invoices || 0 }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Pending payment</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-muted fs-13 text-uppercase">Open Tickets</h5>
                                <div class="d-flex align-items-center justify-content-center gap-2 my-2 py-1">
                                    <div class="user-img fs-42 flex-shrink-0">
                                        <span class="avatar-title text-bg-info rounded-circle fs-22">
                                            <i class="ti ti-message"></i>
                                        </span>
                                    </div>
                                    <h3 class="mb-0 fw-bold">{{ stats?.open_tickets || 0 }}</h3>
                                </div>
                                <p class="mb-0 text-muted">
                                    <span class="text-nowrap">Support tickets</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xxl-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">Recent Services</h4>
                                <Link :href="route('client.services.index')" class="btn btn-sm btn-light">
                                    View All <i class="ti ti-arrow-right ms-1"></i>
                                </Link>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" v-if="services && services.length > 0">
                                    <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                        <tbody>
                                            <tr v-for="service in services.slice(0, 5)" :key="service.id">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-md flex-shrink-0 me-2">
                                                            <span class="avatar-title bg-primary-subtle rounded-circle">
                                                                <i class="ti ti-server"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="text-muted fs-12">{{ service.provisioner || 'Service' }}</span>
                                                            <h5 class="fs-14 mt-1">{{ service.product?.name || 'N/A' }}</h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-12">Status</span>
                                                    <h5 class="fs-14 mt-1 fw-normal">
                                                        <i class="ti ti-circle-filled fs-12" :class="{
                                                            'text-success': service.status === 'active',
                                                            'text-warning': service.status === 'pending',
                                                            'text-danger': service.status === 'suspended'
                                                        }"></i>
                                                        {{ service.status }}
                                                    </h5>
                                                </td>
                                                <td>
                                                    <Link :href="route('client.services.show', service.id)" class="btn btn-sm btn-light">
                                                        Manage
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-center py-4 text-muted">
                                    No services yet
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-6">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="header-title">Recent Invoices</h4>
                                <Link :href="route('client.invoices.index')" class="btn btn-sm btn-light">
                                    View All <i class="ti ti-arrow-right ms-1"></i>
                                </Link>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive" v-if="invoices && invoices.length > 0">
                                    <table class="table table-custom table-centered table-nowrap table-hover mb-0">
                                        <tbody>
                                            <tr v-for="invoice in invoices.slice(0, 5)" :key="invoice.id">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-md flex-shrink-0 me-2">
                                                            <span class="avatar-title bg-success-subtle rounded-circle">
                                                                <i class="ti ti-file-invoice"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <span class="text-muted fs-12">Invoice</span>
                                                            <h5 class="fs-14 mt-1">#{{ invoice.number }}</h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-12">Amount</span>
                                                    <h5 class="fs-14 mt-1 fw-normal">${{ invoice.total }}</h5>
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-12">Status</span>
                                                    <h5 class="fs-14 mt-1 fw-normal">
                                                        <i class="ti ti-circle-filled fs-12" :class="{
                                                            'text-success': invoice.status === 'paid',
                                                            'text-warning': invoice.status === 'open',
                                                            'text-danger': invoice.status === 'overdue'
                                                        }"></i>
                                                        {{ invoice.status }}
                                                    </h5>
                                                </td>
                                                <td>
                                                    <Link :href="route('invoices.show', invoice.id)" class="btn btn-sm btn-light">
                                                        View
                                                    </Link>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div v-else class="text-center py-4 text-muted">
                                    No invoices yet
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>
