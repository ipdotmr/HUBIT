<script setup>
import { Head, Link } from '@inertiajs/vue3';
import OsenLayout from '@/Layouts/OsenLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    productGroups: Array,
});

const selectedCurrency = ref('USD');

const formatPrice = (price, currency = 'USD') => {
    const symbols = {
        'USD': '$',
        'MRU': 'MRU',
        'EUR': '€'
    };
    return `${symbols[currency] || currency}${Number(price).toFixed(2)}`;
};

const getBillingCycleLabel = (cycle) => {
    const labels = {
        'monthly': 'Monthly',
        'quarterly': 'Quarterly',
        'semi-annually': 'Semi-Annually',
        'annually': 'Annually',
        'biennially': 'Biennially',
        'triennially': 'Triennially',
    };
    return labels[cycle] || cycle;
};

const getProductFeatures = (product) => {
    return [
        { icon: 'ti-server', label: 'cPanel', value: 'Yes' },
        { icon: 'ti-database', label: 'Disk Space', value: '50GB SSD' },
        { icon: 'ti-chart-line', label: 'Bandwidth', value: 'Unlimited' },
        { icon: 'ti-world', label: 'Domains', value: '1 Domain' },
        { icon: 'ti-mail', label: 'Email Accounts', value: 'Unlimited' },
        { icon: 'ti-database', label: 'MySQL Databases', value: 'Unlimited' },
    ];
};
</script>

<template>
    <Head title="Order New Services" />

    <OsenLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Order New Services</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <!-- Left Sidebar -->
            <div class="col-lg-3 col-md-4 mb-4">
                <!-- Categories Card -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ti ti-category me-2"></i>Categories</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a 
                            v-for="group in productGroups" 
                            :key="group.slug"
                            href="#"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        >
                            {{ group.name }}
                            <span class="badge bg-primary rounded-pill">{{ group.products.length }}</span>
                        </a>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card mb-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="ti ti-bolt me-2"></i>Actions</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <Link href="/domains/register" class="list-group-item list-group-item-action">
                            <i class="ti ti-world me-2"></i>Register Domain
                        </Link>
                        <Link href="/domains/transfer" class="list-group-item list-group-item-action">
                            <i class="ti ti-transfer me-2"></i>Transfer Domain
                        </Link>
                        <Link :href="route('cart.index')" class="list-group-item list-group-item-action">
                            <i class="ti ti-shopping-cart me-2"></i>View Cart
                        </Link>
                    </div>
                </div>

                <!-- Currency Selector Card -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="ti ti-currency-dollar me-2"></i>Currency</h5>
                    </div>
                    <div class="card-body">
                        <select v-model="selectedCurrency" class="form-select">
                            <option value="USD">USD - US Dollar</option>
                            <option value="MRU">MRU - Mauritanian Ouguiya</option>
                            <option value="EUR">EUR - Euro</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="col-lg-9 col-md-8">
                <!-- Product Groups -->
                <div v-if="productGroups.length > 0">
                    <div v-for="group in productGroups" :key="group.slug" class="mb-5">
                        <div class="mb-4">
                            <h3 class="mb-2">
                                <i class="ti ti-box me-2 text-primary"></i>
                                {{ group.name }}
                            </h3>
                            <p class="text-muted">{{ group.description }}</p>
                        </div>

                        <div class="row">
                            <div v-for="(product, index) in group.products" :key="product.id" class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm border-0 position-relative">
                                    <!-- Most Popular Badge -->
                                    <div v-if="index === 0" class="position-absolute top-0 start-50 translate-middle">
                                        <span class="badge bg-danger px-3 py-2">MOST POPULAR</span>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-center mb-3 mt-2">{{ product.name }}</h5>
                                        
                                        <!-- Price -->
                                        <div class="text-center mb-4">
                                            <h2 class="text-primary mb-0">{{ formatPrice(product.base_price, selectedCurrency) }}</h2>
                                            <small class="text-muted">per month</small>
                                        </div>

                                        <!-- Features List -->
                                        <ul class="list-unstyled mb-4 flex-grow-1">
                                            <li v-for="feature in getProductFeatures(product)" :key="feature.label" class="mb-2">
                                                <i :class="['ti', feature.icon, 'text-success', 'me-2']"></i>
                                                <strong>{{ feature.label }}:</strong> {{ feature.value }}
                                            </li>
                                        </ul>

                                        <!-- Billing Cycles -->
                                        <div class="mb-3">
                                            <small class="text-muted d-block mb-2">Available billing cycles:</small>
                                            <div class="d-flex flex-wrap gap-1">
                                                <span 
                                                    v-for="cycle in product.billing_cycles" 
                                                    :key="cycle"
                                                    class="badge bg-light text-dark"
                                                >
                                                    {{ getBillingCycleLabel(cycle) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Stock Info -->
                                        <div v-if="product.stock !== null" class="mb-3 text-center">
                                            <small :class="product.stock > 0 ? 'text-success' : 'text-danger'">
                                                <i class="ti ti-package me-1"></i>
                                                {{ product.stock > 0 ? product.stock + ' in stock' : 'Out of stock' }}
                                            </small>
                                        </div>

                                        <!-- Order Button -->
                                        <Link 
                                            :href="route('products.show', product.slug)" 
                                            class="btn btn-primary btn-lg w-100"
                                            :class="{ 'disabled': product.stock === 0 }"
                                        >
                                            <i class="ti ti-shopping-cart me-2"></i>
                                            Order Now
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="card">
                    <div class="card-body text-center py-5">
                        <i class="ti ti-box-off display-4 text-muted mb-3"></i>
                        <h4 class="text-muted">No Products Available</h4>
                        <p class="text-muted">There are currently no products available for order. Please check back later.</p>
                    </div>
                </div>
            </div>
        </div>
    </OsenLayout>
</template>
