<template>
  <div class="settings-index">
    <!-- Header -->
    <div class="page-header">
      <div>
        <h1 class="page-title">Settings Console</h1>
        <p class="page-description">Manage your HUBIT installation configuration</p>
      </div>
    </div>

    <!-- Settings Grid -->
    <div class="settings-grid">
      <!-- Company & Branding -->
      <Link :href="route('managit.settings.company')" class="settings-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Company & Branding</h3>
          <p class="card-description">Company information, logos, and invoice settings</p>
        </div>
        <div class="card-arrow">
          <svg width="20" height="20" viewBox="0 0 20 20">
            <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </Link>

      <!-- Themes & UI -->
      <Link :href="route('managit.settings.themes')" class="settings-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Themes & UI</h3>
          <p class="card-description">Visual themes, RTL support, and custom CSS</p>
        </div>
        <div class="card-arrow">
          <svg width="20" height="20" viewBox="0 0 20 20">
            <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </Link>

      <!-- Stripe Payments -->
      <Link :href="route('managit.settings.stripe')" class="settings-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3v-8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Stripe Payments</h3>
          <p class="card-description">Payment gateway, API keys, and webhooks</p>
        </div>
        <div class="card-arrow">
          <svg width="20" height="20" viewBox="0 0 20 20">
            <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </Link>

      <!-- cPanel Provisioning -->
      <Link :href="route('managit.settings.cpanel')" class="settings-card">
        <div class="card-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">cPanel/WHM</h3>
          <p class="card-description">Provisioning server, packages, and nameservers</p>
        </div>
        <div class="card-arrow">
          <svg width="20" height="20" viewBox="0 0 20 20">
            <path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </Link>

      <!-- Coming Soon Cards -->
      <div class="settings-card coming-soon">
        <div class="card-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Email & SMTP</h3>
          <p class="card-description">Mail server configuration and templates</p>
          <span class="coming-soon-badge">Coming Soon</span>
        </div>
      </div>

      <div class="settings-card coming-soon">
        <div class="card-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Security & Authentication</h3>
          <p class="card-description">2FA, password policies, and rate limiting</p>
          <span class="coming-soon-badge">Coming Soon</span>
        </div>
      </div>

      <div class="settings-card coming-soon">
        <div class="card-icon" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Domain Registrars</h3>
          <p class="card-description">Namecheap, ResellerClub, and more</p>
          <span class="coming-soon-badge">Coming Soon</span>
        </div>
      </div>

      <div class="settings-card coming-soon">
        <div class="card-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="card-content">
          <h3 class="card-title">Webhooks & API</h3>
          <p class="card-description">Configure webhooks and API access</p>
          <span class="coming-soon-badge">Coming Soon</span>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
      <div class="stat-card">
        <div class="stat-value">{{ auditLogs.length }}</div>
        <div class="stat-label">Recent Changes</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">4</div>
        <div class="stat-label">Configured Services</div>
      </div>
      <div class="stat-card">
        <div class="stat-value">12</div>
        <div class="stat-label">More Coming</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  settings: Object,
  auditLogs: Array,
});
</script>

<style scoped>
.settings-index {
  min-height: 100vh;
  background: var(--color-background);
  padding: var(--space-6);
}

.page-header {
  margin-bottom: var(--space-8);
}

.page-title {
  font-size: var(--text-4xl);
  font-weight: var(--font-bold);
  color: var(--color-text);
  margin: 0 0 var(--space-2);
}

.page-description {
  font-size: var(--text-lg);
  color: var(--color-text-muted);
  margin: 0;
}

/* Settings Grid */
.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: var(--space-4);
  margin-bottom: var(--space-8);
}

.settings-card {
  display: flex;
  align-items: center;
  gap: var(--space-4);
  padding: var(--space-5);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  cursor: pointer;
  transition: all var(--transition-fast);
  text-decoration: none;
  position: relative;
}

.settings-card:not(.coming-soon):hover {
  border-color: var(--color-primary-500);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.settings-card.coming-soon {
  opacity: 0.6;
  cursor: not-allowed;
}

.card-icon {
  width: 56px;
  height: 56px;
  border-radius: var(--radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  flex-shrink: 0;
  box-shadow: var(--shadow-md);
}

.card-content {
  flex: 1;
}

.card-title {
  font-size: var(--text-lg);
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin: 0 0 var(--space-1);
}

.card-description {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin: 0;
}

.coming-soon-badge {
  display: inline-block;
  margin-top: var(--space-2);
  padding: 2px 8px;
  background: var(--color-warning-50);
  color: var(--color-warning-700);
  border: 1px solid var(--color-warning-500);
  border-radius: var(--radius-sm);
  font-size: var(--text-xs);
  font-weight: var(--font-medium);
}

.card-arrow {
  color: var(--color-text-muted);
  transition: all var(--transition-fast);
}

.settings-card:not(.coming-soon):hover .card-arrow {
  color: var(--color-primary-600);
  transform: translateX(4px);
}

/* Quick Stats */
.quick-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: var(--space-4);
}

.stat-card {
  padding: var(--space-6);
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  text-align: center;
}

.stat-value {
  font-size: var(--text-4xl);
  font-weight: var(--font-bold);
  color: var(--color-primary-600);
  margin-bottom: var(--space-2);
}

.stat-label {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  font-weight: var(--font-medium);
}

/* RTL Support */
[dir="rtl"] .card-arrow {
  transform: scaleX(-1);
}

[dir="rtl"] .settings-card:not(.coming-soon):hover .card-arrow {
  transform: scaleX(-1) translateX(4px);
}

/* Responsive */
@media (max-width: 768px) {
  .settings-grid {
    grid-template-columns: 1fr;
  }

  .page-title {
    font-size: var(--text-3xl);
  }
}
</style>
