<template>
  <div class="secret-field">
    <label v-if="label" :for="id" class="field-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>
    
    <div class="secret-input-wrapper">
      <input
        :id="id"
        :type="isRevealed ? 'text' : 'password'"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="['secret-input', { error: hasError }]"
        :aria-label="label"
        :aria-invalid="hasError"
        :aria-describedby="hasError ? `${id}-error` : helpText ? `${id}-help` : undefined"
      />
      
      <div class="secret-actions">
        <button
          v-if="modelValue && canReveal"
          @click="toggleReveal"
          type="button"
          class="secret-action-btn"
          :aria-label="isRevealed ? 'Hide value' : 'Reveal value'"
          :disabled="disabled"
        >
          <svg v-if="isRevealed" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M3.98 8.223A10.477 10.477 0 001.934 10C3.226 13.338 6.244 15.5 10 15.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0110 4.5c3.756 0 6.773 2.162 8.066 5.5a10.523 10.523 0 01-1.557 2.338m-2.18 2.18a4.5 4.5 0 11-6.364-6.364" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1l18 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          </svg>
          <svg v-else width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M1.934 10C3.226 6.662 6.244 4.5 10 4.5c3.756 0 6.773 2.162 8.066 5.5-1.292 3.338-4.31 5.5-8.066 5.5-3.756 0-6.774-2.162-8.066-5.5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>

        <button
          v-if="modelValue && isRevealed"
          @click="copyToClipboard"
          type="button"
          class="secret-action-btn"
          :aria-label="copied ? 'Copied!' : 'Copy to clipboard'"
          :disabled="disabled"
        >
          <svg v-if="copied" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" fill="currentColor"/>
          </svg>
          <svg v-else width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M8 2a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H8z" stroke="currentColor" stroke-width="1.5"/>
            <path d="M4 6H3a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-1" stroke="currentColor" stroke-width="1.5"/>
          </svg>
        </button>
      </div>
    </div>

    <p v-if="helpText" :id="`${id}-help`" class="help-text">{{ helpText }}</p>
    <p v-if="hasError" :id="`${id}-error`" class="error-text">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: '••••••••••••••••'
  },
  helpText: {
    type: String,
    default: null
  },
  error: {
    type: String,
    default: null
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  canReveal: {
    type: Boolean,
    default: true
  }
});

defineEmits(['update:modelValue']);

const id = ref(`secret-field-${Math.random().toString(36).substr(2, 9)}`);
const isRevealed = ref(false);
const copied = ref(false);

const hasError = computed(() => !!props.error);

const toggleReveal = () => {
  isRevealed.value = !isRevealed.value;
  
  // Auto-hide after 10 seconds for security
  if (isRevealed.value) {
    setTimeout(() => {
      isRevealed.value = false;
    }, 10000);
  }
};

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(props.modelValue);
    copied.value = true;
    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
};
</script>

<style scoped>
.secret-field {
  display: flex;
  flex-direction: column;
  gap: var(--space-2);
}

.field-label {
  font-size: var(--text-sm);
  font-weight: var(--font-medium);
  color: var(--color-text);
}

.required {
  color: var(--color-error-500);
  margin-left: 2px;
}

.secret-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.secret-input {
  width: 100%;
  padding: var(--space-2) var(--space-3);
  padding-right: 90px;
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  font-family: var(--font-mono);
  color: var(--color-text);
  transition: all var(--transition-fast);
}

.secret-input:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 0 0 0 3px var(--color-primary-50);
}

.secret-input:disabled {
  background: var(--color-surface);
  color: var(--color-text-disabled);
  cursor: not-allowed;
}

.secret-input.error {
  border-color: var(--color-error-500);
}

.secret-input.error:focus {
  box-shadow: 0 0 0 3px var(--color-error-50);
}

.secret-actions {
  position: absolute;
  right: var(--space-2);
  display: flex;
  gap: var(--space-1);
}

.secret-action-btn {
  padding: var(--space-1);
  background: transparent;
  border: none;
  color: var(--color-text-muted);
  cursor: pointer;
  border-radius: var(--radius-sm);
  transition: all var(--transition-fast);
  display: flex;
  align-items: center;
  justify-content: center;
}

.secret-action-btn:hover:not(:disabled) {
  background: var(--color-surface-hover);
  color: var(--color-text);
}

.secret-action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.help-text {
  font-size: var(--text-xs);
  color: var(--color-text-muted);
  margin: 0;
}

.error-text {
  font-size: var(--text-xs);
  color: var(--color-error-700);
  margin: 0;
}

/* RTL Support */
[dir="rtl"] .secret-input {
  padding-right: var(--space-3);
  padding-left: 90px;
}

[dir="rtl"] .secret-actions {
  right: auto;
  left: var(--space-2);
}

[dir="rtl"] .required {
  margin-left: 0;
  margin-right: 2px;
}
</style>
