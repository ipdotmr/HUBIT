<template>
  <div class="mb-3">
    <label v-if="label" :for="id" class="form-label fw-semibold">
      {{ label }}
      <span v-if="required" class="text-danger">*</span>
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
      :class="['form-control', { 'is-invalid': hasError }]"
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
      :class="['form-control', { 'is-invalid': hasError }]"
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
      :class="['form-select', { 'is-invalid': hasError }]"
      :aria-label="label"
      :aria-invalid="hasError"
      :aria-describedby="hasError ? `${id}-error` : helpText ? `${id}-help` : undefined"
    >
      <option v-if="placeholder" value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <div v-if="helpText" :id="`${id}-help`" class="form-text">{{ helpText }}</div>
    <div v-if="hasError" :id="`${id}-error`" class="invalid-feedback d-block">{{ error }}</div>
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
/* Bootstrap classes are used, minimal custom styling needed */
</style>
