<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    tickets: Object,
    filters: Object
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const priority = ref(props.filters.priority || '');

const filterTickets = () => {
    router.get(route('managit.support.index'), {
        search: search.value,
        status: status.value,
        priority: priority.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Support Tickets" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Support Tickets</h3>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>New Ticket
                </button>
            </div>

            <!-- Filters -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input 
                                v-model="search" 
                                type="text" 
                                class="form-control" 
                                placeholder="Search tickets..."
                                @input="filterTickets"
                            >
                        </div>
                        <div class="col-md-3">
                            <select v-model="status" class="form-select" @change="filterTickets">
                                <option value="">All Status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="waiting">Waiting</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select v-model="priority" class="form-select" @change="filterTickets">
                                <option value="">All Priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button @click="search = ''; status = ''; priority = ''; filterTickets();" class="btn btn-secondary w-100">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Client</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="ticket in tickets.data" :key="ticket.id">
                                    <td><strong>{{ ticket.ticket_number }}</strong></td>
                                    <td>{{ ticket.subject }}</td>
                                    <td>{{ ticket.client_name }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-info': ticket.priority === 'low',
                                            'bg-primary': ticket.priority === 'medium',
                                            'bg-warning': ticket.priority === 'high',
                                            'bg-danger': ticket.priority === 'urgent'
                                        }">
                                            {{ ticket.priority }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': ticket.status === 'open',
                                            'bg-primary': ticket.status === 'in_progress',
                                            'bg-warning': ticket.status === 'waiting',
                                            'bg-secondary': ticket.status === 'closed'
                                        }">
                                            {{ ticket.status }}
                                        </span>
                                    </td>
                                    <td>{{ ticket.created_at }}</td>
                                    <td>
                                        <Link :href="route('managit.support.show', ticket.id)" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="tickets.data.length === 0">
                                    <td colspan="7" class="text-center text-muted py-4">
                                        No tickets found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Pagination -->
                <div v-if="tickets.links && tickets.links.length > 3" class="card-footer bg-white">
                    <nav>
                        <ul class="pagination mb-0 justify-content-center">
                            <li v-for="link in tickets.links" :key="link.label" 
                                class="page-item" 
                                :class="{ active: link.active, disabled: !link.url }">
                                <Link v-if="link.url" :href="link.url" class="page-link" v-html="link.label"></Link>
                                <span v-else class="page-link" v-html="link.label"></span>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
