<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();
const page = usePage();

// Submenu states - all collapsed by default except Home
const showHomeMenu = ref(false);
const showClientsMenu = ref(false);
const showOrdersMenu = ref(false);
const showBillingMenu = ref(false);
const showSupportMenu = ref(false);
const showReportsMenu = ref(false);
const showUtilitiesMenu = ref(false);
const showAddonsMenu = ref(false);

// Badge counts from shared Inertia props
const pendingOrders = computed(() => page.props.badgeCounts?.pending_orders || 0);
const unpaidInvoices = computed(() => page.props.badgeCounts?.unpaid_invoices || 0);
</script>

<template>
    <div class="whmcs-admin-layout">
        <!-- Top Header Bar -->
        <nav class="whmcs-header">
            <div class="header-container">
                <!-- Hamburger Menu -->
                <button class="hamburger-btn">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Logo -->
                <Link href="/managit/dashboard" class="logo">
                    <img src="https://via.placeholder.com/120x40/2C3E7D/FFFFFF?text=WHMCS" alt="WHMCS" />
                </Link>

                <!-- Search Bar -->
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="search-input" placeholder="Enter search term..." />
                </div>

                <!-- Right Side Icons -->
                <div class="header-actions">
                    <!-- Intelligence Icon -->
                    <button class="icon-btn">
                        <i class="fas fa-lightbulb"></i>
                    </button>

                    <!-- Help Icon -->
                    <button class="icon-btn">
                        <i class="fas fa-question-circle"></i>
                    </button>

                    <!-- Language Switcher -->
                    <LanguageSwitcher />

                    <!-- User Profile Dropdown -->
                    <div class="dropdown">
                        <button class="user-btn" data-bs-toggle="dropdown">
                            <img src="https://via.placeholder.com/32" class="user-avatar" />
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><Link :href="route('profile.edit')" class="dropdown-item">{{ t('account.title') }}</Link></li>
                            <li><Link href="/dashboard" class="dropdown-item">Client Area</Link></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><Link :href="route('logout')" method="post" as="button" class="dropdown-item">{{ t('common.logout') }}</Link></li>
                        </ul>
                    </div>

                    <!-- Menu Toggle -->
                    <button class="icon-btn">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- Main Container -->
        <div class="main-container">
            <!-- Left Sidebar -->
            <aside class="whmcs-sidebar">
                <nav class="sidebar-nav">
                    <!-- Home with Submenu -->
                    <div class="nav-item">
                        <a @click="showHomeMenu = !showHomeMenu" class="nav-link" :class="{ 'active': showHomeMenu }">
                            <i class="fas fa-home nav-icon"></i>
                            <span class="nav-text">{{ t('common.home') }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showHomeMenu }"></i>
                        </a>
                        <div v-show="showHomeMenu" class="submenu">
                            <Link :href="route('managit.clients.create')" class="submenu-link">
                                {{ t('admin.clients.new_client') || 'New Client' }}
                            </Link>
                            <Link :href="route('managit.orders.create')" class="submenu-link">
                                {{ t('admin.orders.new_order') || 'New Order' }}
                            </Link>
                            <Link :href="route('managit.invoices.create')" class="submenu-link">
                                {{ t('admin.invoices.new_invoice') || 'New Invoice' }}
                            </Link>
                            <Link :href="route('managit.support.create')" class="submenu-link">
                                {{ t('admin.support.new_ticket') || 'New Ticket' }}
                            </Link>
                        </div>
                    </div>

                    <!-- Clients -->
                    <div class="nav-item">
                        <a @click="showClientsMenu = !showClientsMenu" class="nav-link" :class="{ 'active': showClientsMenu }">
                            <i class="fas fa-users nav-icon"></i>
                            <span class="nav-text">{{ t('nav.clients') }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showClientsMenu }"></i>
                        </a>
                    </div>

                    <!-- Orders -->
                    <div class="nav-item">
                        <a @click="showOrdersMenu = !showOrdersMenu" class="nav-link" :class="{ 'active': showOrdersMenu }">
                            <i class="fas fa-shopping-cart nav-icon"></i>
                            <span class="nav-text">{{ t('nav.orders') }}</span>
                            <span v-if="pendingOrders > 0" class="badge-count">{{ pendingOrders }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showOrdersMenu }"></i>
                        </a>
                    </div>

                    <!-- Billing -->
                    <div class="nav-item">
                        <a @click="showBillingMenu = !showBillingMenu" class="nav-link" :class="{ 'active': showBillingMenu }">
                            <i class="fas fa-credit-card nav-icon"></i>
                            <span class="nav-text">{{ t('nav.billing') }}</span>
                            <span v-if="unpaidInvoices > 0" class="badge-count">{{ unpaidInvoices }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showBillingMenu }"></i>
                        </a>
                    </div>

                    <!-- Support -->
                    <div class="nav-item">
                        <a @click="showSupportMenu = !showSupportMenu" class="nav-link" :class="{ 'active': showSupportMenu }">
                            <i class="fas fa-life-ring nav-icon"></i>
                            <span class="nav-text">{{ t('nav.support') }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showSupportMenu }"></i>
                        </a>
                    </div>

                    <!-- Reports -->
                    <div class="nav-item">
                        <a @click="showReportsMenu = !showReportsMenu" class="nav-link" :class="{ 'active': showReportsMenu }">
                            <i class="fas fa-chart-bar nav-icon"></i>
                            <span class="nav-text">{{ t('nav.reports') }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showReportsMenu }"></i>
                        </a>
                    </div>

                    <!-- Utilities -->
                    <div class="nav-item">
                        <a @click="showUtilitiesMenu = !showUtilitiesMenu" class="nav-link" :class="{ 'active': showUtilitiesMenu }">
                            <i class="fas fa-file-alt nav-icon"></i>
                            <span class="nav-text">{{ t('nav.utilities') }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showUtilitiesMenu }"></i>
                        </a>
                    </div>

                    <!-- Addons -->
                    <div class="nav-item">
                        <a @click="showAddonsMenu = !showAddonsMenu" class="nav-link" :class="{ 'active': showAddonsMenu }">
                            <i class="fas fa-puzzle-piece nav-icon"></i>
                            <span class="nav-text">{{ t('nav.addons') || 'Addons' }}</span>
                            <i class="fas fa-chevron-down chevron" :class="{ 'rotated': showAddonsMenu }"></i>
                        </a>
                    </div>
                </nav>
            </aside>

            <!-- Main Content Area -->
            <main class="content-area">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* WHMCS Color Scheme */
:root {
    --whmcs-blue: #2C3E7D;
    --whmcs-blue-dark: #1f2d5a;
    --whmcs-blue-light: #3a4f9a;
    --whmcs-orange: #FF6B35;
    --whmcs-bg: #f4f6f9;
}

.whmcs-admin-layout {
    min-height: 100vh;
    background-color: var(--whmcs-bg);
}

/* Header */
.whmcs-header {
    background: #2C3E7D;
    height: 60px;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1030;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.header-container {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 1rem;
    gap: 1rem;
}

.hamburger-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.5rem;
}

.logo img {
    height: 32px;
}

.search-container {
    flex: 1;
    max-width: 600px;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.search-input {
    width: 100%;
    padding: 0.5rem 1rem 0.5rem 2.5rem;
    border: none;
    border-radius: 4px;
    background: white;
    font-size: 0.9rem;
}

.search-input:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto;
}

.icon-btn {
    background: none;
    border: none;
    color: white;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background 0.2s;
}

.icon-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.user-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid white;
}

/* Main Container */
.main-container {
    display: flex;
    margin-top: 60px;
    min-height: calc(100vh - 60px);
}

/* Sidebar */
.whmcs-sidebar {
    width: 240px;
    background: #2C3E7D;
    color: white;
    position: fixed;
    top: 60px;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    box-shadow: 2px 0 4px rgba(0,0,0,0.1);
}

.sidebar-nav {
    padding: 0.5rem 0;
}

.nav-item {
    margin-bottom: 2px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

.nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.nav-icon {
    width: 20px;
    text-align: center;
    margin-right: 0.75rem;
    font-size: 1rem;
}

.nav-text {
    flex: 1;
    font-size: 0.9rem;
}

.chevron {
    font-size: 0.75rem;
    transition: transform 0.2s;
}

.chevron.rotated {
    transform: rotate(180deg);
}

.badge-count {
    background: #FF6B35;
    color: white;
    font-size: 0.7rem;
    padding: 0.15rem 0.4rem;
    border-radius: 10px;
    margin-right: 0.5rem;
    font-weight: 600;
}

.submenu {
    background: rgba(0, 0, 0, 0.2);
    padding: 0.25rem 0;
}

.submenu-link {
    display: block;
    padding: 0.5rem 1rem 0.5rem 3rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.85rem;
    transition: all 0.2s;
}

.submenu-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: white;
}

/* Content Area */
.content-area {
    flex: 1;
    margin-left: 240px;
    padding: 2rem;
    background: var(--whmcs-bg);
}

/* Scrollbar Styling */
.whmcs-sidebar::-webkit-scrollbar {
    width: 6px;
}

.whmcs-sidebar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.whmcs-sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 3px;
}

.whmcs-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
