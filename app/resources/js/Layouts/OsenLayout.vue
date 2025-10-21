<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const showingNavigationDropdown = ref(false);
const page = usePage();
</script>

<template>
    <div class="wrapper" data-sidenav-size="compact">
        <!-- Sidenav Menu Start -->
        <div class="sidenav-menu bg-dark text-white" style="min-height:100vh;">
            <!-- Brand Logo -->
            <Link href="/" class="logo">
                <span class="logo-light">
                    <span class="logo-lg"><img src="/osen-images/logo.png" alt="HUBIT"></span>
                    <span class="logo-sm"><img src="/osen-images/logo-sm.png" alt="HUBIT"></span>
                </span>
                <span class="logo-dark">
                    <span class="logo-lg"><img src="/osen-images/logo-dark.png" alt="HUBIT"></span>
                    <span class="logo-sm"><img src="/osen-images/logo-sm.png" alt="HUBIT"></span>
                </span>
            </Link>

            <div data-simplebar>
                <!-- Sidenav Menu -->
                <ul class="side-nav">
                    <li class="side-nav-title">Main</li>

                    <li class="side-nav-item">
                        <Link :href="route('dashboard')" class="side-nav-link" :class="{ 'active': route().current('dashboard') }">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text"> Dashboard </span>
                        </Link>
                    </li>

                    <li class="side-nav-title mt-2">Services & Products</li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarServices" aria-expanded="true" aria-controls="sidebarServices" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-server"></i></span>
                            <span class="menu-text"> Services </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarServices">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('client.services.index')" class="side-nav-link">
                                        <span class="menu-text">My Services</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('products.index')" class="side-nav-link">
                                        <span class="menu-text">Order New Services</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarDomains" aria-expanded="true" aria-controls="sidebarDomains" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-world"></i></span>
                            <span class="menu-text"> Domains </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarDomains">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('client.domains.index')" class="side-nav-link">
                                        <span class="menu-text">My Domains</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('domains.search')" class="side-nav-link">
                                        <span class="menu-text">Register Domain</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('domains.pricing')" class="side-nav-link">
                                        <span class="menu-text">Domain Pricing</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Billing</li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarInvoice" aria-expanded="true" aria-controls="sidebarInvoice" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-file-invoice"></i></span>
                            <span class="menu-text"> Invoices</span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarInvoice">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('client.invoices.index')" class="side-nav-link">
                                        <span class="menu-text">My Invoices</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarWallet" aria-expanded="true" aria-controls="sidebarWallet" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-wallet"></i></span>
                            <span class="menu-text"> Wallet </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarWallet">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('client.wallet.index')" class="side-nav-link">
                                        <span class="menu-text">My Wallet</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('client.wallet.transactions')" class="side-nav-link">
                                        <span class="menu-text">Transactions</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-item">
                        <Link :href="route('cart.index')" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-shopping-cart"></i></span>
                            <span class="menu-text"> Shopping Cart </span>
                        </Link>
                    </li>

                    <li class="side-nav-title mt-2">Support</li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarSupport" aria-expanded="true" aria-controls="sidebarSupport" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-headset"></i></span>
                            <span class="menu-text"> Support </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarSupport">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">My Tickets</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Open New Ticket</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Knowledgebase</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Announcements</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Network Status</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Account</li>

                    <li class="side-nav-item">
                        <Link :href="route('profile.edit')" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-user"></i></span>
                            <span class="menu-text"> Profile </span>
                        </Link>
                    </li>

                    <li class="side-nav-item">
                        <Link :href="route('logout')" method="post" as="button" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-logout"></i></span>
                            <span class="menu-text"> Logout </span>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Sidenav Menu End -->

        <!-- Start Page Content here -->
        <div class="page-content">
            <!-- Topbar Start -->
            <header class="topbar">
                <div class="container-fluid">
                    <div class="navbar-custom">
                        <div class="topbar-menu d-flex align-items-center gap-lg-2 gap-1">
                            <!-- Sidebar Menu Toggle Button -->
                            <button class="button-toggle-menu">
                                <i class="ti ti-menu-2"></i>
                            </button>

                            <!-- Horizontal Menu Toggle Button -->
                            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                                <div class="lines">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </button>
                        </div>

                        <ul class="topbar-menu d-flex align-items-center gap-3">
                            <!-- Language Switcher -->
                            <li>
                                <LanguageSwitcher />
                            </li>
                            
                            <!-- User Dropdown -->
                            <li class="dropdown">
                                <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="account-user-avatar">
                                        <img src="/osen-images/users/avatar-1.jpg" alt="user-image" width="40" class="rounded-circle">
                                    </span>
                                    <span class="d-lg-flex flex-column gap-0 d-none ms-2">
                                        <h5 class="my-0 fw-semibold fs-14">{{ page.props.auth.user.name }}</h5>
                                        <h6 class="my-0 fw-normal text-muted fs-12">{{ page.props.auth.user.email }}</h6>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                                    <!-- item-->
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">Welcome!</h6>
                                    </div>

                                    <!-- item-->
                                    <Link :href="route('profile.edit')" class="dropdown-item">
                                        <i class="ti ti-user me-2 fs-18 align-middle"></i>
                                        <span>My Account</span>
                                    </Link>

                                    <!-- item-->
                                    <Link :href="route('logout')" method="post" as="button" class="dropdown-item">
                                        <i class="ti ti-logout me-2 fs-18 align-middle"></i>
                                        <span>Logout</span>
                                    </Link>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>
            <!-- end Topbar -->

            <div class="page-container">
                <!-- Page Title -->
                <div class="page-title-box" v-if="$slots.header">
                    <slot name="header" />
                </div>

                <!-- Page Content -->
                <slot />
            </div>

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 text-center">
                            {{ new Date().getFullYear() }} © HUBIT - Hosting & Billing Platform
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- End Page content -->
    </div>
</template>
