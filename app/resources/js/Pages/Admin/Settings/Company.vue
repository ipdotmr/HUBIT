<template>
  <SettingPageLayout
    title="Company & Branding"
    description="Manage your company information, branding, and invoice settings"
    :hasChanges="hasChanges"
    :isSaving="isSaving"
    :auditLogs="auditLogs"
    @save="handleSave"
    @revert="handleRevert"
  >
    <div class="settings-grid">
      <!-- Company Information Section -->
      <div class="settings-section">
        <h2 class="section-title">Company Information</h2>
        <p class="section-description">Basic information about your company</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.company_name"
            label="Company Name"
            placeholder="Enter company name"
            helpText="Legal name of your company"
            required
            :error="errors.company_name"
          />

          <FormField
            v-model="form.legal_name"
            label="Legal Name"
            placeholder="Enter legal name"
            helpText="Official registered name (if different)"
            :error="errors.legal_name"
          />

          <FormField
            v-model="form.tax_id"
            label="Tax/VAT ID"
            placeholder="Enter tax or VAT ID"
            helpText="Your tax identification number"
            :error="errors.tax_id"
          />

          <FormField
            v-model="form.address"
            type="textarea"
            label="Address"
            placeholder="Enter full address"
            :rows="3"
            required
            :error="errors.address"
          />
        </div>
      </div>

      <!-- Branding Section -->
      <div class="settings-section">
        <h2 class="section-title">Branding</h2>
        <p class="section-description">Upload your company logo and customize branding</p>
        
        <div class="form-grid">
          <div class="logo-upload">
            <label class="field-label">Company Logo (Light)</label>
            <div class="logo-preview" v-if="form.logo_url">
              <img :src="form.logo_url" alt="Company logo" />
              <button @click="removeLogo" class="remove-logo" type="button">
                <svg width="20" height="20" viewBox="0 0 20 20">
                  <path d="M6 6L14 14M6 14L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <input
              type="file"
              @change="handleLogoUpload"
              accept="image/png,image/jpg,image/jpeg,image/svg+xml"
              class="file-input"
              ref="logoInput"
            />
            <p class="help-text">PNG, JPG, SVG up to 2MB. Recommended: 200x60px</p>
          </div>

          <div class="logo-upload">
            <label class="field-label">Company Logo (Dark)</label>
            <div class="logo-preview dark" v-if="form.logo_dark_url">
              <img :src="form.logo_dark_url" alt="Company logo dark" />
              <button @click="removeLogoDark" class="remove-logo" type="button">
                <svg width="20" height="20" viewBox="0 0 20 20">
                  <path d="M6 6L14 14M6 14L14 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
              </button>
            </div>
            <input
              type="file"
              @change="handleLogoDarkUpload"
              accept="image/png,image/jpg,image/jpeg,image/svg+xml"
              class="file-input"
              ref="logoDarkInput"
            />
            <p class="help-text">Optional: Logo variant for dark backgrounds</p>
          </div>
        </div>
      </div>

      <!-- Invoice Settings Section -->
      <div class="settings-section">
        <h2 class="section-title">Invoice Settings</h2>
        <p class="section-description">Customize how invoices are generated</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.invoice_prefix"
            label="Invoice Number Prefix"
            placeholder="INV-"
            helpText="Prefix for invoice numbers (e.g., INV-0001)"
            :error="errors.invoice_prefix"
          />

          <FormField
            v-model="form.invoice_currency"
            type="select"
            label="Default Currency"
            :options="currencyOptions"
            required
            :error="errors.invoice_currency"
          />

          <FormField
            v-model.number="form.tax_rate"
            type="number"
            label="Default Tax Rate (%)"
            placeholder="0"
            helpText="Default tax percentage for invoices"
            :error="errors.tax_rate"
          />

          <FormField
            v-model="form.invoice_footer"
            type="textarea"
            label="Invoice Footer Text"
            placeholder="Enter custom footer text"
            helpText="Appears at the bottom of all invoices"
            :rows="3"
            :error="errors.invoice_footer"
          />
        </div>
      </div>
    </div>
  </SettingPageLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import SettingPageLayout from '../../../Components/Settings/SettingPageLayout.vue';
import FormField from '../../../Components/Settings/FormField.vue';

const props = defineProps({
  settings: Object,
  auditLogs: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const form = ref({
  company_name: props.settings?.general?.['company.name'] || '',
  legal_name: props.settings?.general?.['company.legal_name'] || '',
  tax_id: props.settings?.general?.['company.tax_id'] || '',
  address: props.settings?.general?.['company.address'] || '',
  logo_url: props.settings?.general?.['company.logo'] || '',
  logo_dark_url: props.settings?.general?.['company.logo_dark'] || '',
  invoice_prefix: props.settings?.general?.['invoice.prefix'] || 'INV-',
  invoice_currency: props.settings?.general?.['invoice.currency'] || 'USD',
  tax_rate: props.settings?.general?.['invoice.tax_rate'] || 0,
  invoice_footer: props.settings?.general?.['invoice.footer'] || '',
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const logoInput = ref(null);
const logoDarkInput = ref(null);

const currencyOptions = [
  { value: 'USD', label: 'USD - US Dollar' },
  { value: 'EUR', label: 'EUR - Euro' },
  { value: 'GBP', label: 'GBP - British Pound' },
  { value: 'MRU', label: 'MRU - Mauritanian Ouguiya' },
];

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'company.name', value: form.value.company_name, type: 'string' },
    { key: 'company.legal_name', value: form.value.legal_name, type: 'string' },
    { key: 'company.tax_id', value: form.value.tax_id, type: 'string' },
    { key: 'company.address', value: form.value.address, type: 'string' },
    { key: 'company.logo', value: form.value.logo_url, type: 'string' },
    { key: 'company.logo_dark', value: form.value.logo_dark_url, type: 'string' },
    { key: 'invoice.prefix', value: form.value.invoice_prefix, type: 'string' },
    { key: 'invoice.currency', value: form.value.invoice_currency, type: 'string' },
    { key: 'invoice.tax_rate', value: form.value.tax_rate, type: 'int' },
    { key: 'invoice.footer', value: form.value.invoice_footer, type: 'string' },
  ];
  
  router.post('/managit/settings', { settings }, {
    onSuccess: () => {
      originalForm.value = JSON.parse(JSON.stringify(form.value));
      isSaving.value = false;
    },
    onError: () => {
      isSaving.value = false;
    }
  });
};

const handleRevert = () => {
  form.value = JSON.parse(JSON.stringify(originalForm.value));
};

const handleLogoUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // In production, upload to server and get URL
    const reader = new FileReader();
    reader.onload = (e) => {
      form.value.logo_url = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const handleLogoDarkUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      form.value.logo_dark_url = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeLogo = () => {
  form.value.logo_url = '';
  if (logoInput.value) logoInput.value.value = '';
};

const removeLogoDark = () => {
  form.value.logo_dark_url = '';
  if (logoDarkInput.value) logoDarkInput.value.value = '';
};
</script>

<style scoped>
.settings-grid {
  display: flex;
  flex-direction: column;
  gap: var(--space-8);
}

.settings-section {
  background: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
}

.section-title {
  font-size: var(--text-xl);
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin: 0 0 var(--space-2);
}

.section-description {
  font-size: var(--text-sm);
  color: var(--color-text-muted);
  margin: 0 0 var(--space-6);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: var(--space-4);
}

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}

.logo-upload {
  grid-column: 1 / -1;
}

.field-label {
  display: block;
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--color-text);
  margin-bottom: var(--space-2);
}

.logo-preview {
  position: relative;
  width: 200px;
  height: 80px;
  background: var(--color-background);
  border: 2px dashed var(--color-border);
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--space-2);
  padding: var(--space-2);
}

.logo-preview.dark {
  background: #0a0a0a;
}

.logo-preview img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
}

.remove-logo {
  position: absolute;
  top: var(--space-1);
  right: var(--space-1);
  background: var(--color-error-500);
  color: white;
  border: none;
  border-radius: var(--radius-sm);
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: opacity var(--transition-fast);
}

.logo-preview:hover .remove-logo {
  opacity: 1;
}

.file-input {
  display: block;
  width: 100%;
  padding: var(--space-2);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  cursor: pointer;
}

.help-text {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  margin-top: var(--space-1);
}
</style>
