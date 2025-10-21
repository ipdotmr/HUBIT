<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import OsenLayout from '@/Layouts/OsenLayout.vue';

const props = defineProps({
    product: Object,
});

const form = useForm({
    product_id: props.product.id,
    billing_cycle: props.product.billing_cycles?.[0] || 'monthly',
    config_options: {},
    quantity: 1,
});

const selectedCycle = ref(props.product.billing_cycles?.[0] || 'monthly');

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(price);
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

const totalPrice = computed(() => {
    let price = parseFloat(props.product.base_price);
    
    // Add config options prices
    if (props.product.config_options) {
        Object.keys(form.config_options).forEach(optionKey => {
            const option = props.product.config_options.find(opt => opt.key === optionKey);
            if (option && option.price) {
                price += parseFloat(option.price);
            }
        });
    }
    
    return price * form.quantity;
});

const addToCart = () => {
    form.billing_cycle = selectedCycle.value;
    form.post(route('cart.add'), {
        preserveScroll: true,
        onSuccess: () => {
            // Redirect to cart
            window.location.href = route('cart.index');
        },
    });
};
</script>

<template>
    <Head :title="product.name" />

    <OsenLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('products.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i>
                                Back to Products
                            </Link>
                        </div>
                        <h4 class="page-title">{{ product.name }}</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Product Details</h5>
                        <p class="text-muted">{{ product.description }}</p>

                        <!-- Billing Cycle Selection -->
                        <div class="mb-4">
                            <label class="form-label">Select Billing Cycle</label>
                            <div class="row g-2">
                                <div 
                                    v-for="cycle in product.billing_cycles" 
                                    :key="cycle"
                                    class="col-md-4 col-sm-6"
                                >
                                    <div 
                                        class="card border cursor-pointer"
                                        :class="{ 'border-primary bg-light': selectedCycle === cycle }"
                                        @click="selectedCycle = cycle"
                                    >
                                        <div class="card-body p-3">
                                            <div class="form-check">
                                                <input 
                                                    class="form-check-input" 
                                                    type="radio" 
                                                    :value="cycle"
                                                    v-model="selectedCycle"
                                                    :id="'cycle-' + cycle"
                                                >
                                                <label class="form-check-label" :for="'cycle-' + cycle">
                                                    <strong>{{ getBillingCycleLabel(cycle) }}</strong>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuration Options -->
                        <div v-if="product.config_options && product.config_options.length > 0" class="mb-4">
                            <h5 class="card-title mb-3">Configuration Options</h5>
                            <div v-for="option in product.config_options" :key="option.key" class="mb-3">
                                <label class="form-label">{{ option.name }}</label>
                                
                                <!-- Select dropdown -->
                                <select 
                                    v-if="option.type === 'select'"
                                    v-model="form.config_options[option.key]"
                                    class="form-select"
                                >
                                    <option value="">Select {{ option.name }}</option>
                                    <option 
                                        v-for="choice in option.choices" 
                                        :key="choice.value"
                                        :value="choice.value"
                                    >
                                        {{ choice.label }}
                                        <span v-if="choice.price"> (+{{ formatPrice(choice.price) }})</span>
                                    </option>
                                </select>

                                <!-- Text input -->
                                <input 
                                    v-else-if="option.type === 'text'"
                                    v-model="form.config_options[option.key]"
                                    type="text"
                                    class="form-control"
                                    :placeholder="option.placeholder || ''"
                                >

                                <!-- Checkbox -->
                                <div v-else-if="option.type === 'checkbox'" class="form-check">
                                    <input 
                                        v-model="form.config_options[option.key]"
                                        type="checkbox"
                                        class="form-check-input"
                                        :id="'option-' + option.key"
                                    >
                                    <label class="form-check-label" :for="'option-' + option.key">
                                        {{ option.label }}
                                        <span v-if="option.price"> (+{{ formatPrice(option.price) }})</span>
                                    </label>
                                </div>

                                <small v-if="option.description" class="text-muted d-block mt-1">
                                    {{ option.description }}
                                </small>
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label class="form-label">Quantity</label>
                            <input 
                                v-model.number="form.quantity"
                                type="number"
                                class="form-control"
                                min="1"
                                :max="product.stock || 999"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Order Summary</h5>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Product:</span>
                                <span>{{ product.name }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Billing Cycle:</span>
                                <span>{{ getBillingCycleLabel(selectedCycle) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Quantity:</span>
                                <span>{{ form.quantity }}</span>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary fs-4">{{ formatPrice(totalPrice) }}</strong>
                        </div>

                        <button 
                            @click="addToCart"
                            class="btn btn-primary w-100 mb-2"
                            :disabled="form.processing || (product.stock !== null && product.stock === 0)"
                        >
                            <i class="ti ti-shopping-cart me-1"></i>
                            Add to Cart
                        </button>

                        <Link :href="route('products.index')" class="btn btn-light w-100">
                            Continue Shopping
                        </Link>

                        <div v-if="product.stock !== null" class="mt-3">
                            <small class="text-muted">
                                <i class="ti ti-package me-1"></i>
                                {{ product.stock > 0 ? product.stock + ' in stock' : 'Out of stock' }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Product Features -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Product Features</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                24/7 Support
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                99.9% Uptime Guarantee
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                Easy Setup
                            </li>
                            <li class="mb-2">
                                <i class="ti ti-check text-success me-2"></i>
                                Money Back Guarantee
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </OsenLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>
