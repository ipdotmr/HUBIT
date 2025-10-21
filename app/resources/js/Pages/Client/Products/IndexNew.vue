<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    productGroups: Array,
});

const selectedCurrency = ref('USD');
</script>

<template>
    <Head title="Sites web services" />

    <ClientLayout>
        <template #sidebar>
            <!-- Catégories (Categories) -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">Catégories</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a v-for="group in productGroups" :key="group.id" href="#" class="list-group-item list-group-item-action">
                        {{ group.name }}
                    </a>
                </div>
            </div>

            <!-- Actions -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">Actions</h6>
                </div>
                <div class="list-group list-group-flush">
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-globe me-2"></i>Enregistrer un nom de domaine
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">
                        <i class="fas fa-exchange-alt me-2"></i>Transférer un nom de domaine
                    </a>
                    <Link :href="route('cart.index')" class="list-group-item list-group-item-action">
                        <i class="fas fa-shopping-cart me-2"></i>Afficher le panier
                    </Link>
                </div>
            </div>

            <!-- Sélectionnez la devise (Currency Selector) -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">Sélectionnez la devise</h6>
                </div>
                <div class="card-body">
                    <select v-model="selectedCurrency" class="form-select">
                        <option value="USD">USD</option>
                        <option value="EUR">EUR</option>
                        <option value="MRU">MRU</option>
                    </select>
                </div>
            </div>
        </template>

        <!-- Main Content -->
        <div>
            <h2 class="mb-3">Sites web services</h2>
            <p class="text-muted mb-4">Choisissez parmi une variété de services d'hébergement web</p>

            <!-- Product Groups -->
            <div v-for="group in productGroups" :key="group.id" class="mb-5">
                <h4 class="mb-4">{{ group.name }}</h4>
                
                <div class="row">
                    <div v-for="(product, index) in group.products" :key="product.id" class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm position-relative">
                            <!-- PLUS POPULAIRES Badge -->
                            <div v-if="index === 0" class="position-absolute top-0 start-50 translate-middle">
                                <span class="badge bg-danger px-3 py-2">PLUS POPULAIRES</span>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-center mb-3 mt-2">{{ product.name }}</h5>

                                <!-- Price -->
                                <div class="text-center mb-4">
                                    <h3 class="text-primary mb-0">${{ product.base_price }}</h3>
                                    <small class="text-muted">par mois</small>
                                </div>

                                <!-- Features -->
                                <ul class="list-unstyled mb-4 flex-grow-1">
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>cPanel:</strong> Oui
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Espace disque:</strong> 50GB SSD
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Bande passante:</strong> Illimité
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Domaines:</strong> 1 Domaine
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Comptes e-mail:</strong> Illimité
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <strong>Bases de données MySQL:</strong> Illimité
                                    </li>
                                </ul>

                                <!-- Order Button -->
                                <Link :href="route('products.show', product.slug)" class="btn btn-primary btn-lg w-100">
                                    Commander maintenant
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!productGroups || productGroups.length === 0" class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Aucun produit disponible</h4>
                <p class="text-muted">Il n'y a actuellement aucun produit disponible. Veuillez revenir plus tard.</p>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
.card {
    border: 1px solid #dee2e6;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}

.badge {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.list-group-item-action:hover {
    background-color: #f8f9fa;
    color: #0066cc;
}
</style>
