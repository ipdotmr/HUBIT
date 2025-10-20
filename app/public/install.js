(function () {
    const container = document.querySelector('[data-installer-container]');
    if (!container) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
    const stepIndicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
    const step1 = document.getElementById('step-1');
    const step2 = document.getElementById('step-2');
    const step3 = document.getElementById('step-3');
    const continueBtn = document.getElementById('btn-precheck-continue');
    const form = document.getElementById('installer-config-form');
    const configErrors = document.getElementById('config-errors');
    const dbButton = document.getElementById('btn-test-database');
    const dbMessage = document.getElementById('db-check-message');
    const startButton = document.getElementById('btn-start-install');
    const installLog = document.getElementById('install-log');
    const installError = document.getElementById('install-error');
    const installSuccess = document.getElementById('install-success');
    const retryContainer = document.getElementById('install-retry');
    const retryButton = retryContainer?.querySelector('[data-retry-button]');
    const credentialsList = document.getElementById('admin-credentials');

    let currentStep = 1;
    let running = false;
    let currentRunIndex = 0;
    const runSteps = Array.from(document.querySelectorAll('[data-run-step]')).map((item) => item.dataset.runStep);

    function updateStepIndicators(step) {
        stepIndicators.forEach((indicator, index) => {
            const stepNumber = index + 1;
            indicator.classList.remove('is-active', 'is-complete', 'opacity-60');
            if (stepNumber < step) {
                indicator.classList.add('is-complete');
            } else if (stepNumber === step) {
                indicator.classList.add('is-active');
            } else {
                indicator.classList.add('opacity-60');
            }
        });
    }

    function showStep(step) {
        currentStep = step;
        if (step1) step1.classList.toggle('hidden', step !== 1);
        if (step1) step1.setAttribute('aria-hidden', String(step !== 1));
        if (step2) step2.classList.toggle('hidden', step !== 2);
        if (step2) step2.setAttribute('aria-hidden', String(step !== 2));
        if (step3) step3.classList.toggle('hidden', step !== 3);
        if (step3) step3.setAttribute('aria-hidden', String(step !== 3));
        updateStepIndicators(step);
        if (step === 2) {
            form?.scrollIntoView({ behavior: 'smooth' });
        }
        if (step === 3) {
            step3?.scrollIntoView({ behavior: 'smooth' });
            startButton?.removeAttribute('disabled');
        }
    }

    function setStatus(element, status, message) {
        if (!element) {
            return;
        }
        element.textContent = message;
        element.classList.remove('text-slate-600', 'text-red-600', 'text-emerald-600');
        const statusClass = status === 'success' ? 'text-emerald-600' : status === 'error' ? 'text-red-600' : 'text-slate-600';
        element.classList.add(statusClass);
    }

    async function postJson(url, payload) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            const message = data?.message || 'An unexpected error occurred.';
            throw new Error(message);
        }

        return data;
    }

    function serializeForm(formElement) {
        const formData = new FormData(formElement);
        const payload = {};

        formData.forEach((value, key) => {
            if (key === 'app_debug') {
                payload[key] = true;
                return;
            }
            payload[key] = value;
        });

        if (!formData.has('app_debug')) {
            payload['app_debug'] = false;
        }

        return payload;
    }

    function appendLog(message) {
        if (!installLog) {
            return;
        }
        const time = new Date().toLocaleTimeString();
        installLog.textContent += `[${time}] ${message}\n`;
        installLog.scrollTop = installLog.scrollHeight;
    }

    function setRunStepState(step, state) {
        const element = document.querySelector(`[data-run-step="${step}"]`);
        if (!element) {
            return;
        }
        element.classList.remove('is-active', 'is-complete', 'has-error');
        if (state) {
            element.classList.add(state);
        }
    }

    function resetRunStates() {
        runSteps.forEach((step) => setRunStepState(step, null));
        installError?.classList.add('hidden');
        retryContainer?.classList.add('hidden');
        if (installSuccess) {
            installSuccess.classList.add('hidden');
        }
        if (credentialsList) {
            credentialsList.innerHTML = '';
        }
        if (installLog) {
            installLog.textContent = '';
        }
    }

    async function runStepAtIndex(index) {
        if (index >= runSteps.length) {
            running = false;
            startButton?.removeAttribute('aria-busy');
            startButton?.setAttribute('disabled', 'disabled');
            return;
        }

        const step = runSteps[index];
        currentRunIndex = index;
        setRunStepState(step, 'is-active');
        startButton?.setAttribute('aria-busy', 'true');
        try {
            const result = await postJson('/install/run', { step });
            const output = result.output ?? 'Step completed.';
            appendLog(`${step}: ${output}`);
            if (result.generatedPassword && credentialsList) {
                const item = document.createElement('div');
                item.className = 'rounded-xl bg-emerald-600/10 px-3 py-2 text-xs text-emerald-700';
                item.textContent = `Generated admin password: ${result.generatedPassword}`;
                credentialsList.appendChild(item);
            }
            if (result.finished && installSuccess) {
                installSuccess.classList.remove('hidden');
            }
            setRunStepState(step, 'is-complete');

            if (result.finished) {
                running = false;
                startButton?.setAttribute('disabled', 'disabled');
                startButton?.removeAttribute('aria-busy');
                return;
            }

            runStepAtIndex(index + 1);
        } catch (error) {
            running = false;
            setRunStepState(step, 'has-error');
            installError?.classList.remove('hidden');
            if (installError) {
                installError.textContent = error.message;
            }
            retryContainer?.classList.remove('hidden');
            if (retryButton) {
                retryButton.dataset.retryStep = step;
            }
            startButton?.removeAttribute('aria-busy');
            startButton?.removeAttribute('disabled');
        }
    }

    function startRun() {
        if (running) {
            return;
        }
        running = true;
        resetRunStates();
        startButton?.setAttribute('disabled', 'disabled');
        runStepAtIndex(0);
    }

    if (continueBtn) {
        continueBtn.addEventListener('click', () => {
            if (continueBtn.hasAttribute('disabled')) {
                return;
            }
            showStep(2);
        });
    }

    if (dbButton) {
        dbButton.addEventListener('click', async () => {
            if (!form) {
                return;
            }
            dbButton.setAttribute('disabled', 'disabled');
            setStatus(dbMessage, 'pending', 'Testing database connection...');
            const payload = serializeForm(form);
            try {
                const response = await postJson('/install/check-db', {
                    driver: payload.db_driver,
                    host: payload.db_host,
                    port: payload.db_port,
                    database: payload.db_database,
                    username: payload.db_username,
                    password: payload.db_password,
                });
                setStatus(dbMessage, 'success', response.message || 'Connection successful.');
            } catch (error) {
                setStatus(dbMessage, 'error', error.message);
            } finally {
                dbButton.removeAttribute('disabled');
            }
        });
    }

    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            configErrors?.classList.add('hidden');
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton?.setAttribute('disabled', 'disabled');
            submitButton?.setAttribute('aria-busy', 'true');
            const payload = serializeForm(form);

            try {
                const response = await postJson('/install/write-env', payload);
                appendLog('Environment configuration saved.');
                if (response?.admin?.email && credentialsList) {
                    const item = document.createElement('div');
                    item.className = 'rounded-xl bg-slate-900/5 px-3 py-2 text-xs text-slate-700';
                    item.textContent = `Admin email: ${response.admin.email}`;
                    credentialsList.appendChild(item);
                }
                showStep(3);
            } catch (error) {
                if (configErrors) {
                    configErrors.textContent = error.message;
                    configErrors.classList.remove('hidden');
                }
            } finally {
                submitButton?.removeAttribute('disabled');
                submitButton?.removeAttribute('aria-busy');
            }
        });
    }

    if (startButton) {
        startButton.addEventListener('click', () => {
            startRun();
        });
    }

    if (retryButton) {
        retryButton.addEventListener('click', () => {
            const step = retryButton.dataset.retryStep;
            if (!step) {
                return;
            }
            retryContainer?.classList.add('hidden');
            installError?.classList.add('hidden');
            installError.textContent = '';
            running = true;
            startButton?.setAttribute('aria-busy', 'true');
            runStepAtIndex(runSteps.indexOf(step));
        });
    }

    updateStepIndicators(1);
})();
