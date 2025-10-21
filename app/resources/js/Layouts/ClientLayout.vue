<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
const showMagasinDropdown = ref(false);
</script>

<template>
    <div class="client-layout">
        <!-- Top Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <!-- Logo -->
                <Link href="/" class="navbar-brand">
                    <strong>HUBIT</strong>
                </Link>

                <!-- Navbar Toggle for Mobile -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Main Navigation -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <!-- Espace client (Client Area) -->
                        <li class="nav-item">
                            <Link :href="route('dashboard')" class="nav-link">
                                Espace client
                            </Link>
                        </li>

                        <!-- Magasin (Store) Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                Magasin
                            </a>
                            <ul class="dropdown-menu">
                                <li><Link :href="route('products.index')" class="dropdown-item">Tout parcourir</Link></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Hébergement cPanel</a></li>
                                <li><a class="dropdown-item" href="#">Solutions</a></li>
                                <li><a class="dropdown-item" href="#">Développement d'applications</a></li>
                                <li><a class="dropdown-item" href="#">Marketing</a></li>
                                <li><a class="dropdown-item" href="#">Serveurs</a></li>
                                <li><a class="dropdown-item" href="#">iEntreprises</a></li>
                                <li><a class="dropdown-item" href="#">Business Pack</a></li>
                                <li><a class="dropdown-item" href="#">eMail</a></li>
                                <li><a class="dropdown-item" href="#">Certificats SSL</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Enregistrer un nom de domaine</a></li>
                                <li><a class="dropdown-item" href="#">Transférer un nom de domaine</a></li>
                            </ul>
                        </li>

                        <!-- Actualités (News) -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Actualités</a>
                        </li>

                        <!-- Base de connaissances (Knowledge Base) -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Base de connaissances</a>
                        </li>

                        <!-- État du réseau (Network Status) -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">État du réseau</a>
                        </li>

                        <!-- Contactez-nous (Contact Us) -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">Contactez-nous</a>
                        </li>
                    </ul>

                    <!-- Right Side Icons -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Language Switcher -->
                        <li class="nav-item">
                            <LanguageSwitcher />
                        </li>

                        <!-- User Icon -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><Link :href="route('profile.edit')" class="dropdown-item">Mon compte</Link></li>
                                <li><Link :href="route('client.services.index')" class="dropdown-item">Mes services</Link></li>
                                <li><Link :href="route('client.domains.index')" class="dropdown-item">Mes domaines</Link></li>
                                <li><Link :href="route('client.invoices.index')" class="dropdown-item">Mes factures</Link></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><Link :href="route('logout')" method="post" as="button" class="dropdown-item">Déconnexion</Link></li>
                            </ul>
                        </li>

                        <!-- Cart Icon -->
                        <li class="nav-item">
                            <Link :href="route('cart.index')" class="nav-link">
                                <i class="fas fa-shopping-cart"></i>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="container-fluid mt-4">
            <div class="row">
                <!-- Left Sidebar (if slot provided) -->
                <div v-if="$slots.sidebar" class="col-lg-3 col-md-4 mb-4">
                    <slot name="sidebar" />
                </div>

                <!-- Main Content Area -->
                <div :class="$slots.sidebar ? 'col-lg-9 col-md-8' : 'col-12'">
                    <slot />
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-light text-center text-muted py-4 mt-5">
            <div class="container">
                <p class="mb-0">© {{ new Date().getFullYear() }} HUBIT. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.client-layout {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.navbar {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.navbar-brand {
    font-size: 1.5rem;
    color: #0066cc !important;
}

.nav-link {
    color: #333 !important;
    padding: 0.5rem 1rem !important;
}

.nav-link:hover {
    color: #0066cc !important;
}

.dropdown-menu {
    border: 1px solid #ddd;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #0066cc;
}

footer {
    margin-top: auto;
    border-top: 1px solid #dee2e6;
}
</style>
