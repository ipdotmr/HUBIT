<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const { t } = useI18n();
const page = usePage();
const sidebarCollapsed = ref(false);
</script>

<template>
    <div class="phox-client-layout">
        <!-- Left Sidebar with Purple Gradient -->
        <aside class="phox-sidebar" :class="{ 'collapsed': sidebarCollapsed }">
            <!-- Logo -->
            <div class="sidebar-logo">
                <Link href="/" class="logo-link">
                    <strong>HUBIT</strong>
                </Link>
            </div>

            <!-- Navigation Menu -->
            <nav class="sidebar-nav">
                <Link :href="route('dashboard')" class="nav-item" :class="{ 'active': route().current('dashboard') }">
                    <i class="fas fa-home"></i>
                    <span class="nav-text">{{ t('common.home') }}</span>
                </Link>

                <Link :href="route('products.index')" class="nav-item" :class="{ 'active': route().current('products.*') }">
                    <i class="fas fa-store"></i>
                    <span class="nav-text">{{ t('nav.products') }}</span>
                </Link>

                <a href="#" class="nav-item">
                    <i class="fas fa-newspaper"></i>
                    <span class="nav-text">{{ t('support.announcements') }}</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-book"></i>
                    <span class="nav-text">{{ t('support.knowledgebase') }}</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-network-wired"></i>
                    <span class="nav-text">{{ t('support.network_status') }}</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-handshake"></i>
                    <span class="nav-text">Affiliates</span>
                </a>

                <a href="#" class="nav-item">
                    <i class="fas fa-envelope"></i>
                    <span class="nav-text">Contact</span>
                </a>

                <div class="nav-divider"></div>

                <Link :href="route('profile.edit')" class="nav-item" :class="{ 'active': route().current('profile.*') }">
                    <i class="fas fa-user-circle"></i>
                    <span class="nav-text">{{ t('nav.account') }}</span>
                </Link>

                <Link :href="route('cart.index')" class="nav-item" :class="{ 'active': route().current('cart.*') }">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-text">{{ t('cart.title') }}</span>
                </Link>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <LanguageSwitcher />
                <Link :href="route('logout')" method="post" as="button" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-text">{{ t('common.logout') }}</span>
                </Link>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="phox-main-content">
            <!-- Top Bar -->
            <header class="phox-topbar">
                <div class="topbar-left">
                    <button @click="sidebarCollapsed = !sidebarCollapsed" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="page-title">{{ page.props.title || 'Dashboard' }}</h1>
                </div>
                <div class="topbar-right">
                    <div class="user-info">
                        <span class="user-name">{{ page.props.auth.user.name }}</span>
                        <img src="https://via.placeholder.com/40" alt="User" class="user-avatar" />
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="phox-content">
                <slot />
            </main>

            <!-- Footer -->
            <footer class="phox-footer">
                <p>© {{ new Date().getFullYear() }} HUBIT. All rights reserved.</p>
            </footer>
        </div>
    </div>
</template>

<style scoped>
.phox-client-layout {
    display: flex;
    min-height: 100vh;
    background-color: #f5f7fa;
}

/* Phoxca Theme Sidebar - my.ip.mr Style */
.phox-sidebar {
    width: 260px;
    background: #283194;
    color: white;
    display: flex;
    flex-direction: column;
    position: fixed;
    left: 0;
    top: 0;
    bottom: 0;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
}

.phox-sidebar.collapsed {
    width: 70px;
}

.phox-sidebar.collapsed .nav-text {
    display: none;
}

.sidebar-logo {
    padding: 2rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.logo-link {
    color: white !important;
    text-decoration: none;
    font-size: 1.5rem;
    font-weight: 700;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.nav-item {
    display: flex;
    align-items: center;
    padding: 1rem 1.5rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.nav-item:hover {
    background-color: rgba(255,255,255,0.1);
    color: white;
    border-left-color: white;
}

.nav-item.active {
    background-color: rgba(255,255,255,0.15);
    color: white;
    border-left-color: white;
}

.nav-item i {
    width: 24px;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.nav-divider {
    height: 1px;
    background: rgba(255,255,255,0.1);
    margin: 1rem 0;
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.logout-btn {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.logout-btn:hover {
    background: rgba(255,255,255,0.2);
}

.logout-btn i {
    margin-right: 0.75rem;
}

/* Main Content Area */
.phox-main-content {
    flex: 1;
    margin-left: 260px;
    display: flex;
    flex-direction: column;
    transition: margin-left 0.3s ease;
}

.phox-sidebar.collapsed + .phox-main-content {
    margin-left: 70px;
}

/* Top Bar */
.phox-topbar {
    background: white;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    position: sticky;
    top: 0;
    z-index: 100;
}

.topbar-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.sidebar-toggle {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: #283194;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: all 0.2s;
}

.sidebar-toggle:hover {
    background: #f5f7fa;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-name {
    font-weight: 500;
    color: #2d3748;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #283194;
}

/* Content Area */
.phox-content {
    flex: 1;
    padding: 2rem;
}

/* Footer */
.phox-footer {
    background: white;
    padding: 1.5rem 2rem;
    text-align: center;
    color: #718096;
    border-top: 1px solid #e2e8f0;
}

.phox-footer p {
    margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .phox-sidebar {
        transform: translateX(-100%);
    }

    .phox-sidebar.collapsed {
        transform: translateX(0);
    }

    .phox-main-content {
        margin-left: 0;
    }
}
</style>
