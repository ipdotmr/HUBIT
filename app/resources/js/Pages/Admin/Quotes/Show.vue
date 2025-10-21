<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    quote: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        draft: 'bg-secondary',
        sent: 'bg-info',
        accepted: 'bg-success',
        declined: 'bg-danger',
        expired: 'bg-warning',
    };
    return badges[status] || 'bg-secondary';
};
</script>

<template>
    <Head title="View Quote" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.quotes.edit', quote.id)" class="btn btn-warning me-2">
                                <i class="ti ti-edit me-1"></i> Edit
                            </Link>
                            <Link :href="route('managit.quotes.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back
                            </Link>
                        </div>
                        <h4 class="page-title">Quote #{{ quote.quote_number }}</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Quote Details</h5>
                            <span :class="['badge', getStatusBadge(quote.status)]">
                                {{ quote.status.toUpperCase() }}
                            </span>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Client:</strong></p>
                                <p>{{ quote.user?.name || 'N/A' }}</p>
                                <p class="text-muted">{{ quote.user?.email || '' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Quote Number:</strong></p>
                                <p>{{ quote.quote_number }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Subject:</strong></p>
                                <p>{{ quote.subject }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Valid Until:</strong></p>
                                <p>{{ quote.valid_until || 'No expiry date' }}</p>
                            </div>
                        </div>

                        <div v-if="quote.notes" class="mb-3">
                            <p class="mb-2"><strong>Notes:</strong></p>
                            <p class="text-muted">{{ quote.notes }}</p>
                        </div>

                        <hr class="my-4">

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <td><strong>Subtotal:</strong></td>
                                            <td class="text-end">{{ quote.currency }} {{ quote.subtotal }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tax:</strong></td>
                                            <td class="text-end">{{ quote.currency }} {{ quote.tax }}</td>
                                        </tr>
                                        <tr class="table-active">
                                            <td><strong>Total:</strong></td>
                                            <td class="text-end"><strong>{{ quote.currency }} {{ quote.total }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Quote Timeline</h5>
                        
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-circle-check text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1"><strong>Created</strong></p>
                                    <p class="text-muted small">{{ quote.created_at }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="quote.sent_at" class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-send text-info"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1"><strong>Sent</strong></p>
                                    <p class="text-muted small">{{ quote.sent_at }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="quote.accepted_at" class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-check text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1"><strong>Accepted</strong></p>
                                    <p class="text-muted small">{{ quote.accepted_at }}</p>
                                </div>
                            </div>
                        </div>

                        <div v-if="quote.declined_at" class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <i class="ti ti-x text-danger"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="mb-1"><strong>Declined</strong></p>
                                    <p class="text-muted small">{{ quote.declined_at }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
