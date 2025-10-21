<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();
const page = usePage();
const showQuickCreate = ref(false);

// Submenu states
const showClientsMenu = ref(false);
const showOrdersMenu = ref(false);
const showBillingMenu = ref(false);
const showSupportMenu = ref(false);
const showDomainsMenu = ref(false);
const showProductsMenu = ref(false);
const showReportsMenu = ref(false);
const showUtilitiesMenu = ref(false);
const showSettingsMenu = ref(false);
</script>

<template>
    <div class="whmcs-admin-layout">
        <!-- Top Header Bar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top">
            <div class="container-fluid">
                <!-- Logo -->
                <Link href="/managit/dashboard" class="navbar-brand">
                    <strong style="color: #0066cc;">HUBIT</strong>
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
                            <i class="fas fa-plus"></i> {{ t('admin.quick_create') || 'Quick Create' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><Link :href="route('managit.clients.create')" class="dropdown-item"><i class="fas fa-user me-2"></i>{{ t('admin.clients.add_new') }}</Link></li>
                            <li><Link :href="route('managit.orders.create')" class="dropdown-item"><i class="fas fa-shopping-cart me-2"></i>{{ t('admin.orders.add_new') || 'New Order' }}</Link></li>
                            <li><Link :href="route('managit.invoices.create')" class="dropdown-item"><i class="fas fa-file-invoice me-2"></i>{{ t('billing.invoices') }}</Link></li>
                            <li><Link :href="route('managit.support.create')" class="dropdown-item"><i class="fas fa-ticket-alt me-2"></i>{{ t('support.open_ticket') }}</Link></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><Link :href="route('managit.domains.create')" class="dropdown-item"><i class="fas fa-globe me-2"></i>{{ t('domains.register') }}</Link></li>
                            <li><Link :href="route('managit.products.create')" class="dropdown-item"><i class="fas fa-box me-2"></i>{{ t('admin.products.add_new') }}</Link></li>
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
                            <li><Link :href="route('profile.edit')" class="dropdown-item">{{ t('account.title') }}</Link></li>
                            <li><Link href="/dashboard" class="dropdown-item">Client Area</Link></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><Link :href="route('logout')" method="post" as="button" class="dropdown-item">{{ t('common.logout') }}</Link></li>
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
                            <i class="fas fa-home me-2"></i> {{ t('common.home') }}
                        </Link>
                    </li>

                    <!-- Clients with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showClientsMenu = !showClientsMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-users me-2"></i> {{ t('nav.clients') }}</span>
                            <i class="fas" :class="showClientsMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showClientsMenu" class="submenu">
                            <li><Link :href="route('managit.clients.index')" class="submenu-link">{{ t('admin.clients.view_all') }}</Link></li>
                            <li><Link :href="route('managit.clients.create')" class="submenu-link">{{ t('admin.clients.add_new') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Orders with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showOrdersMenu = !showOrdersMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-shopping-cart me-2"></i> {{ t('nav.orders') }}</span>
                            <i class="fas" :class="showOrdersMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showOrdersMenu" class="submenu">
                            <li><Link :href="route('managit.orders.index')" class="submenu-link">{{ t('admin.orders.view_all') }}</Link></li>
                            <li><Link :href="route('managit.orders.create')" class="submenu-link">{{ t('admin.orders.add_new') || 'Add New Order' }}</Link></li>
                        </ul>
                    </li>

                    <!-- Billing with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showBillingMenu = !showBillingMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-file-invoice-dollar me-2"></i> {{ t('nav.billing') }}</span>
                            <i class="fas" :class="showBillingMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showBillingMenu" class="submenu">
                            <li><Link :href="route('managit.invoices.index')" class="submenu-link">{{ t('billing.invoices') }}</Link></li>
                            <li><Link :href="route('managit.transactions.index')" class="submenu-link">{{ t('billing.payment_methods') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Support with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showSupportMenu = !showSupportMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-life-ring me-2"></i> {{ t('nav.support') }}</span>
                            <i class="fas" :class="showSupportMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showSupportMenu" class="submenu">
                            <li><Link :href="route('managit.support.index')" class="submenu-link">{{ t('support.tickets') }}</Link></li>
                            <li><Link :href="route('managit.support.create')" class="submenu-link">{{ t('support.open_ticket') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Domains with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showDomainsMenu = !showDomainsMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-globe me-2"></i> {{ t('nav.domains') }}</span>
                            <i class="fas" :class="showDomainsMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showDomainsMenu" class="submenu">
                            <li><Link :href="route('managit.domains.index')" class="submenu-link">{{ t('domains.view_all') }}</Link></li>
                            <li><Link :href="route('managit.domains.create')" class="submenu-link">{{ t('domains.register') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Products with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showProductsMenu = !showProductsMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-box me-2"></i> {{ t('nav.products') }}</span>
                            <i class="fas" :class="showProductsMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showProductsMenu" class="submenu">
                            <li><Link :href="route('managit.products.index')" class="submenu-link">{{ t('admin.products.view_all') }}</Link></li>
                            <li><Link :href="route('managit.products.create')" class="submenu-link">{{ t('admin.products.add_new') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Reports with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showReportsMenu = !showReportsMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-chart-bar me-2"></i> {{ t('nav.reports') }}</span>
                            <i class="fas" :class="showReportsMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showReportsMenu" class="submenu">
                            <li><Link :href="route('managit.reports.services')" class="submenu-link">{{ t('services.title') }}</Link></li>
                            <li><Link :href="route('managit.reports.wallet')" class="submenu-link">{{ t('billing.wallet') }}</Link></li>
                        </ul>
                    </li>

                    <!-- Utilities with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showUtilitiesMenu = !showUtilitiesMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-tools me-2"></i> {{ t('nav.utilities') }}</span>
                            <i class="fas" :class="showUtilitiesMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showUtilitiesMenu" class="submenu">
                            <li><Link href="/managit/utilities/system-cleanup" class="submenu-link">System Cleanup</Link></li>
                            <li><Link href="/managit/utilities/logs" class="submenu-link">Activity Logs</Link></li>
                        </ul>
                    </li>

                    <!-- Settings with Submenu -->
                    <li class="nav-item mb-2">
                        <a @click="showSettingsMenu = !showSettingsMenu" class="nav-link d-flex justify-content-between align-items-center" style="cursor: pointer;">
                            <span><i class="fas fa-cog me-2"></i> {{ t('nav.settings') }}</span>
                            <i class="fas" :class="showSettingsMenu ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                        </a>
                        <ul v-show="showSettingsMenu" class="submenu">
                            <li><Link :href="route('managit.settings.index')" class="submenu-link">{{ t('nav.settings') }}</Link></li>
                            <li><Link :href="route('managit.settings.company')" class="submenu-link">Company Info</Link></li>
                            <li><Link :href="route('managit.settings.email')" class="submenu-link">Email Settings</Link></li>
                            <li><Link :href="route('managit.settings.themes')" class="submenu-link">Client Theme</Link></li>
                        </ul>
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

.submenu {
    list-style: none;
    padding-left: 2.5rem;
    margin: 0.5rem 0;
}

.submenu-link {
    display: block;
    padding: 0.5rem 1rem;
    color: #666;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.9rem;
    transition: all 0.2s;
}

.submenu-link:hover {
    background-color: #e9ecef;
    color: #0066cc;
}
</style>
