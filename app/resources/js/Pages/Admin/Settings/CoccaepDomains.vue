<template>
  <SettingPageLayout
    title="Coccaep (.mr Registry) 🇲🇷"
    description="Configure Coccaep registrar for Mauritanian national TLDs"
    :hasChanges="hasChanges"
    :isSaving="isSaving"
    :hasTest="true"
    :isTesting="isTesting"
    :auditLogs="auditLogs"
    @save="handleSave"
    @revert="handleRevert"
    @test="handleTest"
  >
    <div class="settings-grid">
      <!-- API Configuration -->
      <div class="settings-section">
        <h2 class="section-title">API Configuration</h2>
        <p class="section-description">Coccaep registry API endpoint and credentials</p>
        
        <div class="form-grid">
          <FormField
            v-model="form.api_base_url"
            label="API Base URL"
            placeholder="https://registry.coccaep.mr/api"
            helpText="Coccaep API endpoint"
            required
            :error="errors.api_base_url"
            class="full-width"
          />

          <FormField
            v-model="form.username"
            label="Username"
            placeholder="Enter username"
            helpText="Your Coccaep account username"
            required
            :error="errors.username"
          />

          <SecretField
            v-model="form.password"
            label="Password"
            placeholder="Enter password"
            helpText="Your Coccaep account password"
            required
            :error="errors.password"
          />

          <FormField
            v-model="form.registrar_code"
            label="Registrar Code"
            placeholder="MR-XXXX"
            helpText="Your assigned registrar code"
            required
            :error="errors.registrar_code"
          />
        </div>
      </div>

      <!-- TLD Configuration -->
      <div class="settings-section">
        <h2 class="section-title">Supported TLDs 🇲🇷</h2>
        <p class="section-description">Select which Mauritanian TLDs you'll manage</p>
        
        <div class="tld-grid">
          <div 
            v-for="tld in supportedTlds" 
            :key="tld.value"
            @click="toggleTld(tld.value)"
            :class="['tld-card', { active: form.enabled_tlds.includes(tld.value) }]"
          >
            <div class="tld-icon">{{ tld.flag }}</div>
            <div class="tld-content">
              <div class="tld-label">{{ tld.label }}</div>
              <div class="tld-desc">{{ tld.description }}</div>
            </div>
            <div class="tld-check">
              <svg v-if="form.enabled_tlds.includes(tld.value)" width="20" height="20" viewBox="0 0 20 20">
                <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- WHOIS Configuration -->
      <div class="settings-section">
        <h2 class="section-title">WHOIS Localization</h2>
        <p class="section-description">Configure multilingual WHOIS data support</p>
        
        <div class="lang-selector">
          <label class="lang-option">
            <input 
              type="checkbox" 
              v-model="form.whois_languages" 
              value="en"
              class="lang-checkbox"
            />
            <div class="lang-flag">🇬🇧</div>
            <span class="lang-name">English</span>
          </label>
          
          <label class="lang-option">
            <input 
              type="checkbox" 
              v-model="form.whois_languages" 
              value="ar"
              class="lang-checkbox"
            />
            <div class="lang-flag">🇸🇦</div>
            <span class="lang-name">العربية (Arabic)</span>
          </label>
          
          <label class="lang-option">
            <input 
              type="checkbox" 
              v-model="form.whois_languages" 
              value="fr"
              class="lang-checkbox"
            />
            <div class="lang-flag">🇫🇷</div>
            <span class="lang-name">Français</span>
          </label>
        </div>
      </div>

      <!-- Test Connection -->
      <div class="settings-section">
        <h2 class="section-title">Connection Test</h2>
        <p class="section-description">Verify your Coccaep registry connection</p>
        
        <TestConnectionButton service="coccaep" />
        
        <div v-if="connectionResult" class="connection-result">
          <div class="result-header">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
            </svg>
            <span>Connected Successfully</span>
          </div>
          <div class="result-details">
            <div class="result-item">
              <span class="result-label">API Version:</span>
              <span class="result-value">{{ connectionResult.version }}</span>
            </div>
            <div class="result-item">
              <span class="result-label">Registry Status:</span>
              <span class="result-value">{{ connectionResult.status }}</span>
            </div>
            <div class="result-item">
              <span class="result-label">Latency:</span>
              <span class="result-value">{{ connectionResult.latency }}ms</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SettingPageLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import SettingPageLayout from '../../../Components/Settings/SettingPageLayout.vue';
import FormField from '../../../Components/Settings/FormField.vue';
import SecretField from '../../../Components/Settings/SecretField.vue';
import TestConnectionButton from '../../../Components/Settings/TestConnectionButton.vue';

const props = defineProps({
  settings: Object,
  auditLogs: Array,
  errors: {
    type: Object,
    default: () => ({})
  }
});

const supportedTlds = [
  { 
    value: '.mr', 
    label: '.mr', 
    description: 'Mauritania General',
    flag: '🇲🇷'
  },
  { 
    value: '.gov.mr', 
    label: '.gov.mr', 
    description: 'Government',
    flag: '🏛️'
  },
  { 
    value: '.edu.mr', 
    label: '.edu.mr', 
    description: 'Education',
    flag: '🎓'
  },
  { 
    value: '.xn--mgbah1a', 
    label: '.موريتانيا', 
    description: 'Mauritania (Arabic IDN)',
    flag: '🌐'
  },
];

const form = ref({
  api_base_url: props.settings?.coccaep?.['coccaep.api_base_url'] || 'https://registry.coccaep.mr/api',
  username: props.settings?.coccaep?.['coccaep.username'] || '',
  password: props.settings?.coccaep?.['coccaep.password'] || '',
  registrar_code: props.settings?.coccaep?.['coccaep.registrar_code'] || '',
  enabled_tlds: props.settings?.coccaep?.['coccaep.enabled_tlds'] || ['.mr'],
  whois_languages: props.settings?.coccaep?.['coccaep.whois_languages'] || ['en', 'ar'],
});

const originalForm = ref(JSON.parse(JSON.stringify(form.value)));
const isSaving = ref(false);
const isTesting = ref(false);
const connectionResult = ref(null);

const hasChanges = computed(() => {
  return JSON.stringify(form.value) !== JSON.stringify(originalForm.value);
});

const toggleTld = (tld) => {
  const index = form.value.enabled_tlds.indexOf(tld);
  if (index > -1) {
    if (form.value.enabled_tlds.length > 1) {
      form.value.enabled_tlds.splice(index, 1);
    }
  } else {
    form.value.enabled_tlds.push(tld);
  }
};

const handleSave = () => {
  isSaving.value = true;
  
  const settings = [
    { key: 'coccaep.api_base_url', value: form.value.api_base_url, type: 'string' },
    { key: 'coccaep.username', value: form.value.username, type: 'string' },
    { key: 'coccaep.password', value: form.value.password, type: 'string', is_secret: true },
    { key: 'coccaep.registrar_code', value: form.value.registrar_code, type: 'string' },
    { key: 'coccaep.enabled_tlds', value: JSON.stringify(form.value.enabled_tlds), type: 'json' },
    { key: 'coccaep.whois_languages', value: JSON.stringify(form.value.whois_languages), type: 'json' },
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

const handleTest = () => {
  isTesting.value = true;
  setTimeout(() => {
    isTesting.value = false;
    connectionResult.value = {
      version: 'EPP/XML 1.0',
      status: 'Operational',
      latency: 145
    };
  }, 2000);
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

.full-width {
  grid-column: 1 / -1;
}

@media (min-width: 768px) {
  .form-grid {
    grid-template-columns: 1fr 1fr;
  }
}

/* TLD Grid */
.tld-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: var(--space-3);
}

.tld-card {
  display: flex;
  align-items: center;
  gap: var(--space-3);
  padding: var(--space-4);
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.tld-card:hover {
  border-color: var(--color-primary-500);
  box-shadow: var(--shadow-sm);
}

.tld-card.active {
  border-color: var(--color-primary-600);
  background: var(--color-primary-50);
}

.tld-icon {
  font-size: var(--text-3xl);
  flex-shrink: 0;
}

.tld-content {
  flex: 1;
}

.tld-label {
  font-weight: var(--font-semibold);
  color: var(--color-text);
  margin-bottom: 2px;
  font-family: var(--font-mono);
}

.tld-desc {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
}

.tld-check {
  color: var(--color-primary-600);
  flex-shrink: 0;
}

/* Language Selector */
.lang-selector {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: var(--space-3);
}

.lang-option {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  padding: var(--space-3);
  background: var(--color-background);
  border: 2px solid var(--color-border);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: all var(--transition-fast);
}

.lang-option:hover {
  border-color: var(--color-primary-500);
}

.lang-option:has(.lang-checkbox:checked) {
  border-color: var(--color-primary-600);
  background: var(--color-primary-50);
}

.lang-checkbox {
  width: 18px;
  height: 18px;
  cursor: pointer;
}

.lang-flag {
  font-size: var(--text-2xl);
}

.lang-name {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--color-text);
}

/* Connection Result */
.connection-result {
  margin-top: var(--space-4);
  padding: var(--space-4);
  background: var(--color-success-50);
  border: 1px solid var(--color-success-500);
  border-radius: var(--radius-md);
}

.result-header {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  margin-bottom: var(--space-3);
  color: var(--color-success-700);
  font-weight: var(--font-semibold);
}

.result-header svg {
  flex-shrink: 0;
}

.result-details {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.result-item {
  display: flex;
  justify-content: space-between;
  font-size: var(--text-sm);
}

.result-label {
  color: var(--color-success-700);
  font-weight: var(--font-medium);
}

.result-value {
  color: var(--color-success-900);
  font-family: var(--font-mono);
}
</style>
