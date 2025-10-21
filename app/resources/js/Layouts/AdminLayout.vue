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
                    <span class="logo-lg"><img src="/osen-images/logo.png" alt="HUBIT Admin"></span>
                    <span class="logo-sm"><img src="/osen-images/logo-sm.png" alt="HUBIT"></span>
                </span>
                <span class="logo-dark">
                    <span class="logo-lg"><img src="/osen-images/logo-dark.png" alt="HUBIT Admin"></span>
                    <span class="logo-sm"><img src="/osen-images/logo-sm.png" alt="HUBIT"></span>
                </span>
            </Link>

            <div data-simplebar>
                <!-- Sidenav Menu -->
                <ul class="side-nav">
                    <li class="side-nav-title">Admin Panel</li>

                    <li class="side-nav-item">
                        <Link href="/managit/dashboard" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-dashboard"></i></span>
                            <span class="menu-text"> Dashboard </span>
                        </Link>
                    </li>

                    <li class="side-nav-title mt-2">Management</li>

                    <!-- Clients -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarClients" aria-expanded="true" aria-controls="sidebarClients" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-users"></i></span>
                            <span class="menu-text"> Clients </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarClients">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('managit.clients.index')" class="side-nav-link">
                                        <span class="menu-text">View All Clients</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('managit.clients.create')" class="side-nav-link">
                                        <span class="menu-text">Add New Client</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Client Groups</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Orders -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarOrders" aria-expanded="true" aria-controls="sidebarOrders" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-shopping-bag"></i></span>
                            <span class="menu-text"> Orders </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarOrders">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Orders</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Pending Orders</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Create New Order</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Services -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminServices" aria-expanded="true" aria-controls="sidebarAdminServices" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-server"></i></span>
                            <span class="menu-text"> Services </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminServices">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Services</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Pending Setup</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Suspended</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Domains -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminDomains" aria-expanded="true" aria-controls="sidebarAdminDomains" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-world"></i></span>
                            <span class="menu-text"> Domains </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminDomains">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Domains</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Domain Pricing</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Registrars</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">TLD Sync</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Billing</li>

                    <!-- Invoices -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminInvoices" aria-expanded="true" aria-controls="sidebarAdminInvoices" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-file-invoice"></i></span>
                            <span class="menu-text"> Invoices </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminInvoices">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Invoices</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Unpaid Invoices</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Create Invoice</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Transactions -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminTransactions" aria-expanded="true" aria-controls="sidebarAdminTransactions" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-credit-card"></i></span>
                            <span class="menu-text"> Transactions </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminTransactions">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('managit.transactions.index')" class="side-nav-link">
                                        <span class="menu-text">All Transactions</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Add Transaction</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Products</li>

                    <!-- Products/Services -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminProducts" aria-expanded="true" aria-controls="sidebarAdminProducts" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-box"></i></span>
                            <span class="menu-text"> Products </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminProducts">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Products</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Product Groups</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Add Product</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('managit.products.mapping')" class="side-nav-link">
                                        <span class="menu-text">Product Mapping</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Support</li>

                    <!-- Support -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminSupport" aria-expanded="true" aria-controls="sidebarAdminSupport" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-headset"></i></span>
                            <span class="menu-text"> Support </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminSupport">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">All Tickets</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Open Tickets</span>
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
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Reports & Utilities</li>

                    <!-- Reports -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminReports" aria-expanded="true" aria-controls="sidebarAdminReports" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-chart-bar"></i></span>
                            <span class="menu-text"> Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminReports">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('managit.reports.services')" class="side-nav-link">
                                        <span class="menu-text">Services Report</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('managit.reports.wallet')" class="side-nav-link">
                                        <span class="menu-text">Wallet Report</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Income Report</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Tax Report</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Utilities -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminUtilities" aria-expanded="true" aria-controls="sidebarAdminUtilities" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-tool"></i></span>
                            <span class="menu-text"> Utilities </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminUtilities">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Automation Status</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Module Queue</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">System Logs</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Database Backup</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-title mt-2">Configuration</li>

                    <!-- Settings -->
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarAdminSettings" aria-expanded="true" aria-controls="sidebarAdminSettings" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-settings"></i></span>
                            <span class="menu-text"> Settings </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse show" id="sidebarAdminSettings">
                            <ul class="sub-menu">
                                <li class="side-nav-item">
                                    <Link :href="route('managit.settings.index')" class="side-nav-link">
                                        <span class="menu-text">General Settings</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('managit.settings.company')" class="side-nav-link">
                                        <span class="menu-text">Company Info</span>
                                    </Link>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Payment Gateways</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Email Templates</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <a href="#" class="side-nav-link">
                                        <span class="menu-text">Admin Users</span>
                                    </a>
                                </li>
                                <li class="side-nav-item">
                                    <Link :href="route('managit.settings.localization')" class="side-nav-link">
                                        <span class="menu-text">Localization</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-item">
                        <Link href="/dashboard" class="side-nav-link">
                            <span class="menu-icon fs-20 me-2"><i class="ti ti-arrow-back"></i></span>
                            <span class="menu-text"> Back to Client Area </span>
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

                            <!-- Quick Actions -->
                            <div class="dropdown d-none d-lg-block">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="ti ti-plus me-1"></i> Quick Create
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-user-plus me-2"></i>Add Client</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-shopping-bag me-2"></i>Create Order</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-file-invoice me-2"></i>Create Invoice</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="ti ti-ticket me-2"></i>Create Ticket</a></li>
                                </ul>
                            </div>
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
                                        <h6 class="my-0 fw-normal text-muted fs-12">Administrator</h6>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                                    <div class="dropdown-header noti-title">
                                        <h6 class="text-overflow m-0">Admin Panel</h6>
                                    </div>

                                    <Link :href="route('profile.edit')" class="dropdown-item">
                                        <i class="ti ti-user me-2 fs-18 align-middle"></i>
                                        <span>My Account</span>
                                    </Link>

                                    <Link href="/dashboard" class="dropdown-item">
                                        <i class="ti ti-arrow-back me-2 fs-18 align-middle"></i>
                                        <span>Client Area</span>
                                    </Link>

                                    <div class="dropdown-divider"></div>

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
                            {{ new Date().getFullYear() }} © HUBIT Admin - Hosting & Billing Platform
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->
        </div>
        <!-- End Page content -->
    </div>
</template>
