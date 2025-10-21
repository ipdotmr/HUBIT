<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const form = useForm({
    name: '',
    description: '',
    sort_order: 0,
});

const submit = () => {
    form.post(route('managit.product-groups.store'));
};
</script>

<template>
    <Head title="Add Product Group" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.product-groups.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Product Groups
                            </Link>
                        </div>
                        <h4 class="page-title">Add Product Group</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label for="name" class="form-label">Group Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.name }"
                                        id="name"
                                        placeholder="e.g., Web Hosting, VPS Hosting, Domain Services"
                                        required
                                    >
                                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label for="sort_order" class="form-label">Sort Order *</label>
                                    <input 
                                        v-model.number="form.sort_order" 
                                        type="number" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.sort_order }"
                                        id="sort_order"
                                        min="0"
                                        required
                                    >
                                    <div v-if="form.errors.sort_order" class="invalid-feedback">{{ form.errors.sort_order }}</div>
                                    <small class="text-muted">Lower numbers appear first</small>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea 
                                        v-model="form.description" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.description }"
                                        id="description"
                                        rows="4"
                                        placeholder="Brief description of this product group..."
                                    ></textarea>
                                    <div v-if="form.errors.description" class="invalid-feedback">{{ form.errors.description }}</div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Product Group' }}
                                    </button>
                                    <Link :href="route('managit.product-groups.index')" class="btn btn-secondary ms-2">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
