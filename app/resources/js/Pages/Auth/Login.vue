<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const locale = ref('en');
const locales = [
    { code: 'en', name: 'English' },
    { code: 'ar', name: 'العربية' },
    { code: 'fr', name: 'Français' }
];

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const switchLanguage = () => {
    fetch(route('language.switch'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({ locale: locale.value })
    }).then(() => {
        window.location.reload();
    });
};
</script>

<template>
    <Head title="Client Login - HUBIT" />
    
    <div class="min-h-screen flex items-center justify-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); position: relative; overflow: hidden;">
        <!-- 3D Cube Pattern Background -->
        <div style="position: absolute; inset: 0; opacity: 0.1; background-image: repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px), repeating-linear-gradient(-45deg, transparent, transparent 35px, rgba(255,255,255,.1) 35px, rgba(255,255,255,.1) 70px);"></div>
        
        <div class="container mx-auto px-4">
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <!-- Logo -->
                    <div class="text-center mb-8">
                        <h1 class="text-5xl font-bold text-white mb-2">HUBIT</h1>
                        <p class="text-white text-opacity-90">Hosting & Billing Platform</p>
                    </div>

                    <!-- Login Card -->
                    <div class="bg-white rounded-lg shadow-2xl p-8">
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Client Area Login</h2>
                            <p class="text-sm text-gray-600">Restricted Access</p>
                        </div>

                        <div v-if="status" class="mb-4 p-3 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                            {{ status }}
                        </div>

                        <form @submit.prevent="submit">
                            <div class="mb-4">
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    :class="{ 'border-red-500': form.errors.email }"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="your@email.com"
                                />
                                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    :class="{ 'border-red-500': form.errors.password }"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">{{ form.errors.password }}</p>
                            </div>

                            <div class="mb-6 flex items-center">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label for="remember" class="ml-2 text-sm text-gray-700">Stay logged in</label>
                            </div>

                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-lg"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                                :disabled="form.processing"
                            >
                                <span v-if="!form.processing">Login</span>
                                <span v-else>Logging in...</span>
                            </button>
                        </form>

                        <!-- SSO Options -->
                        <div class="mt-6">
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a
                                    :href="route('auth.microsoft')"
                                    class="w-full flex items-center justify-center gap-3 px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                                >
                                    <svg class="w-5 h-5" viewBox="0 0 23 23" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M0 0h10.931v10.931H0V0z" fill="#F25022"/>
                                        <path d="M12.069 0H23v10.931H12.069V0z" fill="#7FBA00"/>
                                        <path d="M0 12.069h10.931V23H0V12.069z" fill="#00A4EF"/>
                                        <path d="M12.069 12.069H23V23H12.069V12.069z" fill="#FFB900"/>
                                    </svg>
                                    Sign in with Microsoft 365
                                </a>
                            </div>
                        </div>

                        <!-- Language Selector -->
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <label for="language" class="text-sm text-gray-600">Language:</label>
                                <select
                                    id="language"
                                    v-model="locale"
                                    @change="switchLanguage"
                                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option v-for="lang in locales" :key="lang.code" :value="lang.code">
                                        {{ lang.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 text-center">
                            <p class="text-xs text-gray-500">Powered by HUBIT</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Additional custom styles if needed */
</style>
