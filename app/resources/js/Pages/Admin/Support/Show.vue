<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    ticket: Object,
    messages: Array
});

const replyForm = useForm({
    message: ''
});

const submitReply = () => {
    replyForm.post(route('managit.support.reply', props.ticket.id), {
        onSuccess: () => {
            replyForm.reset();
        }
    });
};

const updateStatus = (status) => {
    router.post(route('managit.support.updateStatus', props.ticket.id), {
        status: status
    });
};
</script>

<template>
    <Head :title="`Ticket: ${ticket.ticket_number}`" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="mb-0">{{ ticket.subject }}</h3>
                    <p class="text-muted mb-0">Ticket #{{ ticket.ticket_number }}</p>
                </div>
                <Link :href="route('managit.support.index')" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Tickets
                </Link>
            </div>

            <div class="row g-4">
                <!-- Messages -->
                <div class="col-md-8">
                    <!-- Ticket Messages -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Conversation</h5>
                        </div>
                        <div class="card-body">
                            <div v-for="message in messages" :key="message.id" class="mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-circle" :class="message.is_admin ? 'bg-primary' : 'bg-secondary'">
                                            {{ message.user_name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong>{{ message.user_name }}</strong>
                                            <small class="text-muted">{{ message.created_at }}</small>
                                        </div>
                                        <div class="message-content p-3 bg-light rounded">
                                            {{ message.message }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="messages.length === 0" class="text-center text-muted py-4">
                                No messages yet
                            </div>
                        </div>
                    </div>

                    <!-- Reply Form -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="fas fa-reply me-2"></i>Reply to Ticket</h5>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitReply">
                                <div class="mb-3">
                                    <textarea 
                                        v-model="replyForm.message" 
                                        class="form-control" 
                                        rows="5" 
                                        placeholder="Type your reply..."
                                        required
                                    ></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" :disabled="replyForm.processing">
                                    <i class="fas fa-paper-plane me-2"></i>Send Reply
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-md-4">
                    <!-- Ticket Info -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Ticket Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">Status</label>
                                <div>
                                    <span class="badge" :class="{
                                        'bg-success': ticket.status === 'open',
                                        'bg-primary': ticket.status === 'in_progress',
                                        'bg-warning': ticket.status === 'waiting',
                                        'bg-secondary': ticket.status === 'closed'
                                    }">
                                        {{ ticket.status }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Priority</label>
                                <div>
                                    <span class="badge" :class="{
                                        'bg-info': ticket.priority === 'low',
                                        'bg-primary': ticket.priority === 'medium',
                                        'bg-warning': ticket.priority === 'high',
                                        'bg-danger': ticket.priority === 'urgent'
                                    }">
                                        {{ ticket.priority }}
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Department</label>
                                <div>{{ ticket.department }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted small">Created</label>
                                <div>{{ ticket.created_at }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Client Info -->
                    <div v-if="ticket.client" class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Client</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <strong>{{ ticket.client.name }}</strong>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">{{ ticket.client.email }}</small>
                            </div>
                            <Link :href="route('managit.clients.show', ticket.client.id)" class="btn btn-sm btn-outline-primary w-100">
                                View Client
                            </Link>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button @click="updateStatus('open')" class="btn btn-success btn-sm" :disabled="ticket.status === 'open'">
                                    <i class="fas fa-folder-open me-2"></i>Open
                                </button>
                                <button @click="updateStatus('in_progress')" class="btn btn-primary btn-sm" :disabled="ticket.status === 'in_progress'">
                                    <i class="fas fa-spinner me-2"></i>In Progress
                                </button>
                                <button @click="updateStatus('waiting')" class="btn btn-warning btn-sm" :disabled="ticket.status === 'waiting'">
                                    <i class="fas fa-clock me-2"></i>Waiting
                                </button>
                                <button @click="updateStatus('closed')" class="btn btn-secondary btn-sm" :disabled="ticket.status === 'closed'">
                                    <i class="fas fa-check me-2"></i>Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>

<style scoped>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.message-content {
    white-space: pre-wrap;
    word-wrap: break-word;
}
</style>
