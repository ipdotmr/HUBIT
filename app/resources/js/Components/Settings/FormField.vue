<template>
  <div class="form-field">
    <label v-if="label" :for="id" class="field-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>
    
    <input
      v-if="type !== 'textarea' && type !== 'select'"
      :id="id"
      :type="type"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      :class="['field-input', { error: hasError }]"
      :aria-label="label"
      :aria-invalid="hasError"
      :aria-describedby="hasError ? `${id}-error` : helpText ? `${id}-help` : undefined"
    />

    <textarea
      v-if="type === 'textarea'"
      :id="id"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      :placeholder="placeholder"
      :disabled="disabled"
      :required="required"
      :rows="rows"
      :class="['field-textarea', { error: hasError }]"
      :aria-label="label"
      :aria-invalid="hasError"
      :aria-describedby="hasError ? `${id}-error` : helpText ? `${id}-help` : undefined"
    ></textarea>

    <select
      v-if="type === 'select'"
      :id="id"
      :value="modelValue"
      @change="$emit('update:modelValue', $event.target.value)"
      :disabled="disabled"
      :required="required"
      :class="['field-select', { error: hasError }]"
      :aria-label="label"
      :aria-invalid="hasError"
      :aria-describedby="hasError ? `${id}-error` : helpText ? `${id}-help` : undefined"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <p v-if="helpText" :id="`${id}-help`" class="help-text">{{ helpText }}</p>
    <p v-if="hasError" :id="`${id}-error`" class="error-text">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    default: null
  },
  placeholder: {
    type: String,
    default: null
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
  options: {
    type: Array,
    default: () => []
  },
  rows: {
    type: Number,
    default: 4
  }
});

defineEmits(['update:modelValue']);

const id = ref(`form-field-${Math.random().toString(36).substr(2, 9)}`);
const hasError = computed(() => !!props.error);
</script>

<style scoped>
.form-field {
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

.field-input,
.field-textarea,
.field-select {
  width: 100%;
  padding: var(--space-2) var(--space-3);
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  font-size: var(--text-sm);
  color: var(--color-text);
  transition: all var(--transition-fast);
}

.field-input:focus,
.field-textarea:focus,
.field-select:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 0 0 0 3px var(--color-primary-50);
}

.field-input:disabled,
.field-textarea:disabled,
.field-select:disabled {
  background: var(--color-surface);
  color: var(--color-text-disabled);
  cursor: not-allowed;
}

.field-input.error,
.field-textarea.error,
.field-select.error {
  border-color: var(--color-error-500);
}

.field-input.error:focus,
.field-textarea.error:focus,
.field-select.error:focus {
  box-shadow: 0 0 0 3px var(--color-error-50);
}

.field-textarea {
  resize: vertical;
  min-height: 80px;
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
[dir="rtl"] .required {
  margin-left: 0;
  margin-right: 2px;
}
</style>
