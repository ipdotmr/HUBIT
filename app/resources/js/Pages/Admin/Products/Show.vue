<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    product: Object
});

const deleteProduct = () => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('managit.products.destroy', props.product.id));
    }
};
</script>

<template>
    <Head title="Product Details" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Product Details</h3>
                <div>
                    <Link :href="route('managit.products.edit', product.id)" class="btn btn-primary me-2">
                        <i class="fas fa-edit me-2"></i>Edit
                    </Link>
                    <button @click="deleteProduct" class="btn btn-danger me-2">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                    <Link :href="route('managit.products.index')" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Products
                    </Link>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Product Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Product Name:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ product.name }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Product Group:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ product.group_name }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Description:</strong>
                                </div>
                                <div class="col-md-8">
                                    {{ product.description || 'N/A' }}
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="badge" :class="{
                                        'bg-success': product.status === 'active',
                                        'bg-secondary': product.status === 'inactive'
                                    }">
                                        {{ product.status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Pricing</h5>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Price:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    <span class="text-success fw-bold">${{ product.price }}</span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-6">
                                    <strong>Billing Cycle:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    {{ product.billing_cycle }}
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-6">
                                    <strong>Setup Fee:</strong>
                                </div>
                                <div class="col-6 text-end">
                                    ${{ product.setup_fee }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
