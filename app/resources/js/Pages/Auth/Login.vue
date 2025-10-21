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
    { code: 'en', name: 'English', flag: '🇬🇧' },
    { code: 'fr', name: 'Français', flag: '🇫🇷' },
    { code: 'ar', name: 'العربية', flag: '🇸🇦' }
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
    
    <div class="login-page">
        <!-- 3D Cubes Background -->
        <div class="cubes-background">
            <!-- Isometric cubes pattern -->
            <div class="cube cube-1"></div>
            <div class="cube cube-2"></div>
            <div class="cube cube-3"></div>
            <div class="cube cube-4"></div>
            <div class="cube cube-5"></div>
            <div class="cube cube-6"></div>
            <div class="cube cube-7"></div>
            <div class="cube cube-8"></div>
            <div class="cube cube-9"></div>
            <div class="cube cube-10"></div>
            <div class="cube cube-11"></div>
            <div class="cube cube-12"></div>
        </div>

        <!-- Header with Logo -->
        <header class="login-header">
            <a href="/" class="logo-link">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 5C11.716 5 5 11.716 5 20C5 28.284 11.716 35 20 35C28.284 35 35 28.284 35 20C35 11.716 28.284 5 20 5Z" fill="white" opacity="0.2"/>
                    <path d="M20 10C14.477 10 10 14.477 10 20C10 25.523 14.477 30 20 30C25.523 30 30 25.523 30 20C30 14.477 25.523 10 20 10Z" fill="white"/>
                    <path d="M20 15C17.239 15 15 17.239 15 20C15 22.761 17.239 25 20 25C22.761 25 25 22.761 25 20C25 17.239 22.761 15 20 15Z" fill="#4169E1"/>
                </svg>
                <span class="logo-text">IPROD</span>
            </a>
        </header>

        <!-- Login Card -->
        <main class="login-main">
            <article class="login-card">
                <h1 class="login-title">Connexion à l'espace client</h1>
                <small class="login-subtitle">Accès restreint</small>

                <div v-if="status" class="alert alert-success">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="form-group">
                        <label for="email" class="form-label">Adresse courriel</label>
                        <input
                            id="email"
                            type="email"
                            v-model="form.email"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.email }"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Adresse courriel"
                        />
                        <div v-if="form.errors.email" class="invalid-feedback">{{ form.errors.email }}</div>
                    </div>

                    <div class="form-group">
                        <div class="password-label-row">
                            <label for="password" class="form-label">Mot de passe</label>
                            <a href="/password/reset" class="forgot-password">Mot de passe oublié?</a>
                        </div>
                        <input
                            id="password"
                            type="password"
                            v-model="form.password"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.password }"
                            required
                            autocomplete="current-password"
                            placeholder="Mot de passe"
                        />
                        <div v-if="form.errors.password" class="invalid-feedback">{{ form.errors.password }}</div>
                    </div>

                    <div class="form-check">
                        <input
                            id="remember"
                            type="checkbox"
                            v-model="form.remember"
                            class="form-check-input"
                        />
                        <label for="remember" class="form-check-label">Rester connecté</label>
                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary btn-login"
                        :disabled="form.processing"
                    >
                        <span v-if="!form.processing">Connexion</span>
                        <span v-else>Connexion en cours...</span>
                    </button>
                </form>

                <!-- Language Selector -->
                <div class="language-selector">
                    <button 
                        type="button" 
                        class="language-button"
                        @click="() => {}"
                    >
                        <span class="language-icon">🌐</span>
                        {{ locales.find(l => l.code === locale)?.name || 'Français' }}
                    </button>
                    <select
                        v-model="locale"
                        @change="switchLanguage"
                        class="language-select"
                    >
                        <option v-for="lang in locales" :key="lang.code" :value="lang.code">
                            {{ lang.flag }} {{ lang.name }}
                        </option>
                    </select>
                </div>
            </article>
        </main>

        <!-- Footer -->
        <footer class="login-footer">
            Powered by <a href="https://www.whmcs.com/" target="_blank">WHMCompleteSolution</a>
        </footer>
    </div>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
    background: linear-gradient(135deg, #4169E1 0%, #1E3A8A 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

/* 3D Cubes Background */
.cubes-background {
    position: absolute;
    inset: 0;
    overflow: hidden;
    z-index: 0;
}

.cube {
    position: absolute;
    width: 80px;
    height: 92px;
    transform-style: preserve-3d;
    animation: float 6s ease-in-out infinite;
}

/* Isometric cube using CSS */
.cube::before,
.cube::after {
    content: '';
    position: absolute;
    width: 80px;
    height: 80px;
}

/* Top face */
.cube::before {
    background: rgba(255, 255, 255, 0.9);
    transform: rotateX(60deg) rotateZ(45deg);
    transform-origin: bottom left;
}

/* Side face */
.cube::after {
    background: rgba(30, 58, 138, 0.8);
    transform: rotateY(60deg) rotateZ(45deg);
    transform-origin: bottom left;
    left: 40px;
}

/* Cube positions */
.cube-1 { top: 10%; left: 5%; animation-delay: 0s; }
.cube-2 { top: 15%; left: 15%; animation-delay: 1s; }
.cube-3 { top: 40%; left: 8%; animation-delay: 2s; }
.cube-4 { top: 60%; left: 12%; animation-delay: 0.5s; }
.cube-5 { top: 75%; left: 18%; animation-delay: 1.5s; }
.cube-6 { top: 25%; left: 25%; animation-delay: 2.5s; }
.cube-7 { top: 50%; left: 20%; animation-delay: 3s; }
.cube-8 { top: 35%; right: 45%; animation-delay: 1s; }
.cube-9 { top: 55%; right: 48%; animation-delay: 2s; }
.cube-10 { top: 70%; right: 50%; animation-delay: 0.5s; }
.cube-11 { top: 20%; right: 52%; animation-delay: 1.5s; }
.cube-12 { top: 45%; right: 55%; animation-delay: 2.5s; }

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

/* Header */
.login-header {
    position: relative;
    z-index: 10;
    padding: 2rem 3rem;
}

.logo-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    text-decoration: none;
    color: white;
}

.logo-text {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    letter-spacing: 0.05em;
}

/* Main Content */
.login-main {
    position: relative;
    z-index: 10;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 2rem 3rem;
}

.login-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    padding: 3rem 2.5rem;
    width: 100%;
    max-width: 420px;
    margin-right: 5%;
}

.login-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.5rem;
    text-align: center;
}

.login-subtitle {
    display: block;
    font-size: 0.875rem;
    color: #6b7280;
    text-align: center;
    margin-bottom: 2rem;
}

.alert {
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
}

.alert-success {
    background-color: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
}

/* Form */
.login-form {
    margin-top: 2rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}

.password-label-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.forgot-password {
    font-size: 0.875rem;
    color: #4169E1;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.9375rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    transition: all 0.2s;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: #4169E1;
    box-shadow: 0 0 0 3px rgba(65, 105, 225, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #ef4444;
}

.form-check {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
}

.form-check-input {
    width: 1rem;
    height: 1rem;
    margin-right: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    cursor: pointer;
}

.form-check-label {
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
}

.btn-login {
    width: 100%;
    padding: 0.875rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    color: white;
    background: #1E3A8A;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-login:hover:not(:disabled) {
    background: #1e40af;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
}

.btn-login:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Language Selector */
.language-selector {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    position: relative;
}

.language-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
    justify-content: center;
}

.language-button:hover {
    border-color: #4169E1;
    background: #f9fafb;
}

.language-icon {
    font-size: 1.125rem;
}

.language-select {
    position: absolute;
    top: 1.5rem;
    left: 0;
    right: 0;
    opacity: 0;
    pointer-events: none;
    width: 100%;
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    cursor: pointer;
}

.language-selector:hover .language-select {
    opacity: 1;
    pointer-events: all;
}

/* Footer */
.login-footer {
    position: relative;
    z-index: 10;
    background: #000;
    color: #9ca3af;
    text-align: center;
    padding: 1.5rem;
    font-size: 0.875rem;
}

.login-footer a {
    color: #9ca3af;
    text-decoration: none;
}

.login-footer a:hover {
    color: white;
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 768px) {
    .login-main {
        justify-content: center;
        padding: 2rem 1rem;
    }

    .login-card {
        margin-right: 0;
        max-width: 100%;
    }

    .login-header {
        padding: 1.5rem 1rem;
    }

    .cube {
        width: 60px;
        height: 69px;
    }

    .cube::before,
    .cube::after {
        width: 60px;
        height: 60px;
    }
}
</style>
