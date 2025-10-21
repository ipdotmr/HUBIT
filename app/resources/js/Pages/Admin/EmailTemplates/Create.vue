<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import WhmcsAdminLayout from '@/Layouts/WhmcsAdminLayout.vue';
import { ref } from 'vue';

const activeTab = ref('en');

const form = useForm({
    name: '',
    type: 'outgoing',
    category: '',
    description: '',
    subject_en: '',
    body_en: '',
    subject_ar: '',
    body_ar: '',
    subject_fr: '',
    body_fr: '',
    variables: [],
    is_active: true,
});

const submit = () => {
    form.post(route('managit.email-templates.store'));
};

const addVariable = () => {
    const varName = prompt('Enter variable name (e.g., client_name, order_id):');
    if (varName && !form.variables.includes(varName)) {
        form.variables.push(varName);
    }
};

const removeVariable = (index) => {
    form.variables.splice(index, 1);
};
</script>

<template>
    <Head title="Create Email Template" />

    <WhmcsAdminLayout>
        <template #header>
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <div class="page-title-right">
                            <Link :href="route('managit.email-templates.index')" class="btn btn-secondary">
                                <i class="ti ti-arrow-left me-1"></i> Back to Templates
                            </Link>
                        </div>
                        <h4 class="page-title">Create Email Template</h4>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Template Name *</label>
                                    <input 
                                        v-model="form.name" 
                                        type="text" 
                                        class="form-control" 
                                        :class="{ 'is-invalid': form.errors.name }"
                                        id="name"
                                        required
                                    >
                                    <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="type" class="form-label">Type *</label>
                                    <select 
                                        v-model="form.type" 
                                        class="form-select" 
                                        :class="{ 'is-invalid': form.errors.type }"
                                        id="type"
                                        required
                                    >
                                        <option value="incoming">Incoming</option>
                                        <option value="outgoing">Outgoing</option>
                                    </select>
                                    <div v-if="form.errors.type" class="invalid-feedback">{{ form.errors.type }}</div>
                                </div>
                                <div class="col-md-3">
                                    <label for="category" class="form-label">Category</label>
                                    <input 
                                        v-model="form.category" 
                                        type="text" 
                                        class="form-control" 
                                        id="category"
                                        placeholder="e.g., Orders, Invoices"
                                    >
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-10">
                                    <label for="description" class="form-label">Description</label>
                                    <input 
                                        v-model="form.description" 
                                        type="text" 
                                        class="form-control" 
                                        id="description"
                                        placeholder="Brief description of this template"
                                    >
                                </div>
                                <div class="col-md-2">
                                    <label for="is_active" class="form-label">Status</label>
                                    <div class="form-check form-switch mt-2">
                                        <input 
                                            v-model="form.is_active" 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="is_active"
                                        >
                                        <label class="form-check-label" for="is_active">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Variables Section -->
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="form-label">Available Variables</label>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span 
                                            v-for="(variable, index) in form.variables" 
                                            :key="index"
                                            class="badge bg-info"
                                        >
                                            &#123;&#123;{{ variable }}&#125;&#125;
                                            <i 
                                                @click="removeVariable(index)" 
                                                class="ti ti-x ms-1" 
                                                style="cursor: pointer;"
                                            ></i>
                                        </span>
                                        <button 
                                            @click.prevent="addVariable" 
                                            type="button" 
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            <i class="ti ti-plus"></i> Add Variable
                                        </button>
                                    </div>
                                    <small class="text-muted">
                                        Use these variables in your email subject and body. They will be replaced with actual values when sending.
                                    </small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Language Tabs -->
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'en'"
                                        :class="['nav-link', { active: activeTab === 'en' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> English
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'ar'"
                                        :class="['nav-link', { active: activeTab === 'ar' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> Arabic
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button 
                                        @click="activeTab = 'fr'"
                                        :class="['nav-link', { active: activeTab === 'fr' }]"
                                        type="button"
                                    >
                                        <i class="ti ti-language me-1"></i> French
                                    </button>
                                </li>
                            </ul>

                            <!-- English Tab -->
                            <div v-show="activeTab === 'en'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="subject_en" class="form-label">Subject (English) *</label>
                                        <input 
                                            v-model="form.subject_en" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.subject_en }"
                                            id="subject_en"
                                            required
                                        >
                                        <div v-if="form.errors.subject_en" class="invalid-feedback">{{ form.errors.subject_en }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="body_en" class="form-label">Body (English) *</label>
                                        <textarea 
                                            v-model="form.body_en" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.body_en }"
                                            id="body_en"
                                            rows="15"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.body_en" class="invalid-feedback">{{ form.errors.body_en }}</div>
                                        <small class="text-muted">HTML is supported. Use variables like &#123;&#123;client_name&#125;&#125; for dynamic content.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Arabic Tab -->
                            <div v-show="activeTab === 'ar'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="subject_ar" class="form-label">Subject (Arabic)</label>
                                        <input 
                                            v-model="form.subject_ar" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.subject_ar }"
                                            id="subject_ar"
                                            dir="rtl"
                                        >
                                        <div v-if="form.errors.subject_ar" class="invalid-feedback">{{ form.errors.subject_ar }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="body_ar" class="form-label">Body (Arabic)</label>
                                        <textarea 
                                            v-model="form.body_ar" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.body_ar }"
                                            id="body_ar"
                                            rows="15"
                                            dir="rtl"
                                        ></textarea>
                                        <div v-if="form.errors.body_ar" class="invalid-feedback">{{ form.errors.body_ar }}</div>
                                        <small class="text-muted">HTML is supported. Use variables like &#123;&#123;client_name&#125;&#125; for dynamic content.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- French Tab -->
                            <div v-show="activeTab === 'fr'" class="tab-content">
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="subject_fr" class="form-label">Subject (French)</label>
                                        <input 
                                            v-model="form.subject_fr" 
                                            type="text" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.subject_fr }"
                                            id="subject_fr"
                                        >
                                        <div v-if="form.errors.subject_fr" class="invalid-feedback">{{ form.errors.subject_fr }}</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label for="body_fr" class="form-label">Body (French)</label>
                                        <textarea 
                                            v-model="form.body_fr" 
                                            class="form-control" 
                                            :class="{ 'is-invalid': form.errors.body_fr }"
                                            id="body_fr"
                                            rows="15"
                                        ></textarea>
                                        <div v-if="form.errors.body_fr" class="invalid-feedback">{{ form.errors.body_fr }}</div>
                                        <small class="text-muted">HTML is supported. Use variables like &#123;&#123;client_name&#125;&#125; for dynamic content.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <button 
                                        type="submit" 
                                        class="btn btn-primary"
                                        :disabled="form.processing"
                                    >
                                        <i class="ti ti-device-floppy me-1"></i>
                                        {{ form.processing ? 'Creating...' : 'Create Template' }}
                                    </button>
                                    <Link :href="route('managit.email-templates.index')" class="btn btn-secondary ms-2">
                                        Cancel
                                    </Link>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </WhmcsAdminLayout>
</template>
