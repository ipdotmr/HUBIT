<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
const showQuickCreate = ref(false);
</script>

<template>
    <div class="whmcs-admin-layout">
        <!-- Top Header Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
            <div class="container-fluid">
                <!-- Logo -->
                <Link href="/managit/dashboard" class="navbar-brand">
                    <strong style="color: #0066cc;">WHMCS</strong>
                </Link>

                <!-- Search Bar -->
                <form class="d-flex mx-auto" style="width: 40%;">
                    <input class="form-control" type="search" placeholder="Search clients, orders, invoices..." />
                </form>

                <!-- Right Side Icons -->
                <div class="d-flex align-items-center gap-3">
                    <!-- Quick Create Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-plus"></i> Quick Create
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><Link :href="route('managit.clients.create')" class="dropdown-item"><i class="fas fa-user me-2"></i>New Client</Link></li>
                            <li><Link :href="route('managit.orders.index')" class="dropdown-item"><i class="fas fa-shopping-cart me-2"></i>New Order</Link></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-file-invoice me-2"></i>New Invoice</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-ticket-alt me-2"></i>New Ticket</a></li>
                        </ul>
                    </div>

                    <!-- Language Switcher -->
                    <LanguageSwitcher />

                    <!-- Settings Icon -->
                    <Link :href="route('managit.settings.index')" class="text-dark">
                        <i class="fas fa-cog fs-5"></i>
                    </Link>

                    <!-- Help Icon -->
                    <a href="#" class="text-dark">
                        <i class="fas fa-question-circle fs-5"></i>
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-decoration-none dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <img src="https://via.placeholder.com/32" class="rounded-circle me-2" width="32" height="32" />
                            <span>{{ page.props.auth.user.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><Link :href="route('profile.edit')" class="dropdown-item">My Account</Link></li>
                            <li><Link href="/dashboard" class="dropdown-item">Client Area</Link></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><Link :href="route('logout')" method="post" as="button" class="dropdown-item">Logout</Link></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Container -->
        <div class="d-flex" style="margin-top: 56px;">
            <!-- Left Sidebar -->
            <div class="sidebar bg-light border-end" style="width: 250px; min-height: calc(100vh - 56px);">
                <ul class="nav flex-column p-3">
                    <!-- Home -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.dashboard')" class="nav-link" :class="{ 'active': route().current('managit.dashboard') }">
                            <i class="fas fa-home me-2"></i> Home
                        </Link>
                    </li>

                    <!-- Clients -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.clients.index')" class="nav-link" :class="{ 'active': route().current('managit.clients.*') }">
                            <i class="fas fa-users me-2"></i> Clients
                        </Link>
                    </li>

                    <!-- Orders -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.orders.index')" class="nav-link" :class="{ 'active': route().current('managit.orders.*') }">
                            <i class="fas fa-shopping-cart me-2"></i> Orders
                            <span class="badge bg-primary float-end">1</span>
                        </Link>
                    </li>

                    <!-- Billing -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.transactions.index')" class="nav-link" :class="{ 'active': route().current('managit.transactions.*') }">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Billing
                            <span class="badge bg-danger float-end">74</span>
                        </Link>
                    </li>

                    <!-- Support -->
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link">
                            <i class="fas fa-life-ring me-2"></i> Support
                        </a>
                    </li>

                    <!-- Reports -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.reports.services')" class="nav-link" :class="{ 'active': route().current('managit.reports.*') }">
                            <i class="fas fa-chart-bar me-2"></i> Reports
                        </Link>
                    </li>

                    <!-- Utilities -->
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link">
                            <i class="fas fa-tools me-2"></i> Utilities
                        </a>
                    </li>

                    <!-- Addons -->
                    <li class="nav-item mb-2">
                        <Link :href="route('managit.settings.index')" class="nav-link" :class="{ 'active': route().current('managit.settings.*') }">
                            <i class="fas fa-puzzle-piece me-2"></i> Settings
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- Main Content Area -->
            <div class="flex-grow-1 p-4">
                <slot />
            </div>
        </div>
    </div>
</template>

<style scoped>
.whmcs-admin-layout {
    min-height: 100vh;
    background-color: #f8f9fa;
}

.navbar {
    z-index: 1030;
}

.sidebar {
    position: sticky;
    top: 56px;
    height: calc(100vh - 56px);
    overflow-y: auto;
}

.nav-link {
    color: #333;
    border-radius: 4px;
    padding: 0.75rem 1rem;
    transition: all 0.2s;
}

.nav-link:hover {
    background-color: #e9ecef;
    color: #0066cc;
}

.nav-link.active {
    background-color: #0066cc;
    color: white !important;
}

.nav-link i {
    width: 20px;
    text-align: center;
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
</style>
