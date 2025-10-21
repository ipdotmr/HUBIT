<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    quotes: Object,
    filters: Object,
});

const statusFilter = ref(props.filters?.status || 'all');

const filterByStatus = () => {
    router.get(route('managit.quotes.index'), { status: statusFilter.value }, { preserveState: true });
};

const deleteQuote = (id) => {
    if (confirm('Are you sure you want to delete this quote?')) {
        router.delete(route('managit.quotes.destroy', id));
    }
};

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
    <Head title="Quotes" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.quotes.create')" class="btn btn-primary">
                                <i class="ti ti-plus me-1"></i> Create Quote
                            </Link>
                        </div>
                        <h4 class="page-title">Quotes</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <select v-model="statusFilter" @change="filterByStatus" class="form-select">
                                    <option value="all">All Statuses</option>
                                    <option value="draft">Draft</option>
                                    <option value="sent">Sent</option>
                                    <option value="accepted">Accepted</option>
                                    <option value="declined">Declined</option>
                                    <option value="expired">Expired</option>
                                </select>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-centered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Quote #</th>
                                        <th>Client</th>
                                        <th>Subject</th>
                                        <th>Total</th>
                                        <th>Valid Until</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="quote in quotes.data" :key="quote.id">
                                        <td><strong>{{ quote.quote_number }}</strong></td>
                                        <td>{{ quote.user?.name || 'N/A' }}</td>
                                        <td>{{ quote.subject }}</td>
                                        <td>{{ quote.currency }} {{ quote.total }}</td>
                                        <td>{{ quote.valid_until || '-' }}</td>
                                        <td>
                                            <span :class="['badge', getStatusBadge(quote.status)]">
                                                {{ quote.status.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <Link 
                                                    :href="route('managit.quotes.show', quote.id)" 
                                                    class="btn btn-sm btn-info"
                                                    title="View"
                                                >
                                                    <i class="ti ti-eye"></i>
                                                </Link>
                                                <Link 
                                                    :href="route('managit.quotes.edit', quote.id)" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit"
                                                >
                                                    <i class="ti ti-edit"></i>
                                                </Link>
                                                <button 
                                                    @click="deleteQuote(quote.id)" 
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete"
                                                >
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="quotes.data.length === 0">
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No quotes found. Create your first quote to get started.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
