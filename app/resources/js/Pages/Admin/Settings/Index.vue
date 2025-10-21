<script setup>
import { Head, Link } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';

defineProps({
  settings: Object,
  auditLogs: Array,
});
</script>

<template>
  <Head title="Settings - Admin" />
  
  <WhmcsAdminLayout>
    <div class="container-fluid">
      <!-- Header -->
      <div class="mb-4">
        <h3 class="mb-0">Settings Console</h3>
        <p class="text-muted">Manage your HUBIT installation configuration</p>
      </div>

      <!-- Settings Grid -->
      <div class="row g-3">
        <!-- Company & Branding -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.company')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                  <i class="fas fa-building text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Company & Branding</h5>
                  <p class="text-muted small mb-0">Company information, logos, and invoice settings</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Themes & UI -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.themes')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                  <i class="fas fa-palette text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Themes & UI</h5>
                  <p class="text-muted small mb-0">Visual themes, RTL support, and custom CSS</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Email & SMTP -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.email')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                  <i class="fas fa-envelope text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Email & SMTP</h5>
                  <p class="text-muted small mb-0">Mail server configuration and test email</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Stripe Payments -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.stripe')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                  <i class="fab fa-stripe text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Stripe Payments</h5>
                  <p class="text-muted small mb-0">Payment gateway, API keys, and webhooks</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- PayPal Payments -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.paypal')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #0070ba 0%, #1546a0 100%);">
                  <i class="fab fa-paypal text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">PayPal Payments</h5>
                  <p class="text-muted small mb-0">PayPal REST API and webhook configuration</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Payment Accounts -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.payment-accounts')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                  <i class="fas fa-wallet text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Payment Accounts</h5>
                  <p class="text-muted small mb-0">Manage payment accounts and currencies</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- cPanel/WHM -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.cpanel')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                  <i class="fas fa-server text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">cPanel/WHM</h5>
                  <p class="text-muted small mb-0">Provisioning server, packages, and nameservers</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Plesk Server -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.plesk')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #52c5fd 0%, #1e88e5 100%);">
                  <i class="fas fa-server text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Plesk Server</h5>
                  <p class="text-muted small mb-0">Plesk provisioning server and service plans</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Exchange Rates -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.exchange-rates')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                  <i class="fas fa-exchange-alt text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Exchange Rates</h5>
                  <p class="text-muted small mb-0">Currency exchange rates configuration</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Namecheap Domains -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.namecheap')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #ff6c2f 0%, #ff4500 100%);">
                  <i class="fas fa-globe text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Namecheap Domains</h5>
                  <p class="text-muted small mb-0">Namecheap registrar API configuration</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- ResellerClub Domains -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.resellerclub')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                  <i class="fas fa-globe text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">ResellerClub Domains</h5>
                  <p class="text-muted small mb-0">ResellerClub registrar API configuration</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Name.com Domains -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.namecom')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                  <i class="fas fa-globe text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Name.com Domains</h5>
                  <p class="text-muted small mb-0">Name.com registrar REST API integration</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Coccaep (.mr Registry) -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.coccaep')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                  <i class="fas fa-globe text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Coccaep (.mr Registry) 🇲🇷</h5>
                  <p class="text-muted small mb-0">Mauritanian national TLDs with IDN support</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>

        <!-- Localization -->
        <div class="col-md-4">
          <Link :href="route('managit.settings.localization')" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 settings-card">
              <div class="card-body d-flex align-items-center">
                <div class="icon-box me-3" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                  <i class="fas fa-language text-white"></i>
                </div>
                <div class="flex-grow-1">
                  <h5 class="mb-1">Localization</h5>
                  <p class="text-muted small mb-0">Languages, timezone, and regional settings</p>
                </div>
                <i class="fas fa-chevron-right text-muted"></i>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="row g-3 mt-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
              <h2 class="text-primary mb-2">{{ auditLogs?.length || 0 }}</h2>
              <p class="text-muted mb-0">Recent Changes</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
              <h2 class="text-primary mb-2">14</h2>
              <p class="text-muted mb-0">Configured Pages</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
              <h2 class="text-primary mb-2">5</h2>
              <p class="text-muted mb-0">Themes Available</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </WhmcsAdminLayout>
</template>

<style scoped>
.settings-card {
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
}

.settings-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.icon-box {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-box i {
  font-size: 20px;
}
</style>
