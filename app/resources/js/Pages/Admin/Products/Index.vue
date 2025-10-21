<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

const props = defineProps({
    products: Object,
    groups: Array,
    filters: Object
});

const search = ref(props.filters.search || '');
const group = ref(props.filters.group || '');

const filterProducts = () => {
    router.get(route('managit.products.index'), {
        search: search.value,
        group: group.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Products" />
    
    <WhmcsAdminLayout>
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Product Management</h3>
                <Link :href="route('managit.products.create')" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Product
                </Link>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <input 
                                v-model="search" 
                                type="text" 
                                class="form-control" 
                                placeholder="Search products..."
                                @input="filterProducts"
                            >
                        </div>
                        <div class="col-md-5">
                            <select v-model="group" class="form-select" @change="filterProducts">
                                <option value="">All Groups</option>
                                <option v-for="g in groups" :key="g.id" :value="g.id">{{ g.name }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button @click="search = ''; group = ''; filterProducts();" class="btn btn-secondary w-100">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Group</th>
                                    <th>Price</th>
                                    <th>Billing Cycle</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in products.data" :key="product.id">
                                    <td><strong>{{ product.name }}</strong></td>
                                    <td>{{ product.group_name }}</td>
                                    <td><strong>${{ product.price }}</strong></td>
                                    <td>{{ product.billing_cycle }}</td>
                                    <td>
                                        <span class="badge" :class="{
                                            'bg-success': product.status === 'active',
                                            'bg-secondary': product.status === 'inactive'
                                        }">
                                            {{ product.status }}
                                        </span>
                                    </td>
                                    <td>
                                        <Link :href="route('managit.products.show', product.id)" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-eye"></i>
                                        </Link>
                                        <Link :href="route('managit.products.edit', product.id)" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i>
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="products.data.length === 0">
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No products found
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div v-if="products.links && products.links.length > 3" class="card-footer bg-white">
                    <nav>
                        <ul class="pagination mb-0 justify-content-center">
                            <li v-for="link in products.links" :key="link.label" 
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
