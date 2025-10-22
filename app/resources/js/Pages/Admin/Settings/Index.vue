<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

defineProps({
  settings: Object,
  auditLogs: Array,
});
</script>

<template>
  <Head title="HUBIT - System Settings" />
  
  <WhmcsAdminLayout>
    <!-- Page Header -->
    <div class="page-header mb-4">
      <h1 class="page-title">System Settings</h1>
      <p class="page-subtitle">Set up and configure your WHMCS installation.</p>
    </div>

    <!-- Category Filter -->
    <div class="settings-filter mb-4">
      <div class="filter-sidebar">
        <button class="filter-btn active">All</button>
        <button class="filter-btn">System</button>
        <button class="filter-btn">Apps & Integrations</button>
        <button class="filter-btn">User Management</button>
        <button class="filter-btn">Products & Services</button>
        <button class="filter-btn">Support</button>
        <button class="filter-btn">API & Security</button>
      </div>
      <div class="filter-sort">
        <select class="form-select">
          <option>Popularity</option>
          <option>Alphabetical</option>
          <option>Recently Updated</option>
        </select>
      </div>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
        <!-- General Settings -->
        <Link :href="route('managit.settings.company')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-cog"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">General Settings</h5>
              <p class="settings-card-desc">General settings and configuration</p>
            </div>
          </div>
        </Link>

        <!-- Apps & Integrations -->
        <Link :href="route('managit.settings.stripe')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-puzzle-piece"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Apps & Integrations</h5>
              <span class="settings-badge">UPDATED</span>
              <p class="settings-card-desc">Browse available modules and integrations</p>
            </div>
          </div>
        </Link>

        <!-- Automation Settings -->
        <Link :href="route('managit.settings.email')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-clock"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Automation Settings</h5>
              <span class="settings-badge">UPDATED</span>
              <p class="settings-card-desc">Timing and automation setup</p>
            </div>
          </div>
        </Link>

        <!-- MarketConnect -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-bullseye"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">MarketConnect</h5>
              <span class="settings-badge settings-badge-green">NEW SERVICE</span>
              <p class="settings-card-desc">Re-sell Market Leading Services</p>
            </div>
          </div>
        </Link>

        <!-- Products/Services -->
        <Link :href="route('managit.products.index')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-box"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Products/Services</h5>
              <p class="settings-card-desc">Setup and manage products</p>
            </div>
          </div>
        </Link>

        <!-- Configurable Options -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-sliders-h"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Configurable Options</h5>
              <p class="settings-card-desc">Manage extras and options for products</p>
            </div>
          </div>
        </Link>

        <!-- Product Addons -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-plus-square"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Product Addons</h5>
              <span class="settings-badge">UPDATED</span>
              <p class="settings-card-desc">Setup and manage product add-ons</p>
            </div>
          </div>
        </Link>

        <!-- Product Bundles -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-layer-group"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Product Bundles</h5>
              <p class="settings-card-desc">Create and manage product bundles</p>
            </div>
          </div>
        </Link>

        <!-- Email Templates -->
        <Link :href="route('managit.settings.email')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-envelope"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Email Templates</h5>
              <span class="settings-badge">UPDATED</span>
              <p class="settings-card-desc">Customise email templates</p>
            </div>
          </div>
        </Link>

        <!-- Payment Gateways -->
        <Link :href="route('managit.settings.stripe')" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-university"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Payment Gateways</h5>
              <p class="settings-card-desc">Setup and manage payment gateways</p>
            </div>
          </div>
        </Link>

        <!-- Tax Configuration -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-percent"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Tax Configuration</h5>
              <span class="settings-badge">UPDATED</span>
              <p class="settings-card-desc">Configure Taxes and VAT</p>
            </div>
          </div>
        </Link>

        <!-- Administrator Users -->
        <Link href="#" class="settings-card-link">
          <div class="settings-card">
            <div class="settings-card-icon">
              <i class="fas fa-user-shield"></i>
            </div>
            <div class="settings-card-content">
              <h5 class="settings-card-title">Administrator Users</h5>
              <p class="settings-card-desc">Setup and manage staff members</p>
            </div>
          </div>
        </Link>
      </div>
  </WhmcsAdminLayout>
</template>

<style scoped>
/* Page Header */
.page-header {
  margin-bottom: 2rem;
}

.page-title {
  font-size: 1.75rem;
  font-weight: 600;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.page-subtitle {
  color: #6c757d;
  font-size: 0.95rem;
  margin: 0;
}

/* Settings Filter */
.settings-filter {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2rem;
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.filter-sidebar {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.filter-btn {
  background: none;
  border: none;
  padding: 0.5rem 1rem;
  text-align: left;
  color: #495057;
  font-size: 0.9rem;
  border-radius: 4px;
  transition: all 0.2s;
  cursor: pointer;
}

.filter-btn:hover {
  background: #f8f9fa;
  color: #FF6B35;
}

.filter-btn.active {
  background: #FF6B35;
  color: white;
  font-weight: 600;
}

.filter-sort {
  min-width: 200px;
}

.filter-sort .form-select {
  border: 1px solid #dee2e6;
  border-radius: 4px;
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
}

/* Settings Grid */
.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}

.settings-card-link {
  text-decoration: none;
  color: inherit;
}

.settings-card {
  background: #fef5f1;
  border-radius: 8px;
  padding: 2rem 1.5rem;
  text-align: center;
  transition: all 0.2s;
  cursor: pointer;
  min-height: 180px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.settings-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.settings-card-icon {
  width: 64px;
  height: 64px;
  background: #FF6B35;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 1rem;
}

.settings-card-icon i {
  font-size: 2rem;
  color: white;
}

.settings-card-content {
  position: relative;
}

.settings-card-title {
  font-size: 1rem;
  font-weight: 600;
  color: #333;
  margin: 0 0 0.5rem 0;
}

.settings-card-desc {
  font-size: 0.85rem;
  color: #6c757d;
  margin: 0;
  line-height: 1.4;
}

.settings-badge {
  display: inline-block;
  background: #FF6B35;
  color: white;
  font-size: 0.65rem;
  font-weight: 700;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  margin-bottom: 0.5rem;
  letter-spacing: 0.5px;
}

.settings-badge-green {
  background: #28a745;
}

/* Responsive */
@media (max-width: 768px) {
  .settings-filter {
    flex-direction: column;
  }
  
  .filter-sidebar {
    flex-direction: row;
    flex-wrap: wrap;
  }
  
  .filter-sort {
    width: 100%;
  }
  
  .settings-grid {
    grid-template-columns: 1fr;
  }
}
</style>
