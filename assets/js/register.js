/* ============================================
   REGISTER PAGE — LIVE FORM VALIDATION
   ============================================ */

(function () {
    'use strict';

    /* ============================================
       0. HELPERS
       ============================================ */

    /**
     * Find the closest .auth-field wrapper for an input.
     */
    function fieldWrap(input) {
        return input.closest('.auth-field');
    }

    /**
     * Set a field's state: valid, invalid, or neutral.
     */
    function setState(input, state, message) {
        const wrap = fieldWrap(input);
        if (!wrap) return;

        wrap.classList.remove('is-valid', 'is-invalid');
        if (state) wrap.classList.add('is-' + state);

        // Remove existing error message
        const existing = wrap.querySelector('.auth-error');
        if (existing) existing.remove();

        // Add new error message if provided
        if (state === 'invalid' && message) {
            const el = document.createElement('div');
            el.className = 'auth-error';
            el.textContent = message;
            wrap.appendChild(el);
        }
    }

    /**
     * Clear a field's state entirely.
     */
    function clearState(input) {
        setState(input, null, null);
    }

    /* ============================================
       1. VALIDATORS
       ============================================ */

    const validators = {

        name: function (value) {
            const v = value.trim();
            if (v === '')                 return 'Please enter your name.';
            if (v.length < 2)             return 'Name must be at least 2 characters.';
            if (v.length > 100)           return 'Name is too long.';
            if (!/^[\p{L}\p{M}\s'.\-]+$/u.test(v))
                                          return 'Name can only contain letters, spaces, and hyphens.';
            return null;
        },

        email: function (value) {
            const v = value.trim();
            if (v === '')                 return 'Please enter your email address.';
            if (v.length > 255)           return 'Email is too long.';
            // RFC-lite pattern — rejects the obvious garbage without being too strict
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v))
                                          return 'Please enter a valid email address.';
            return null;
        },

        password: function (value) {
            if (value === '')             return 'Please choose a password.';
            if (value.length < 8)         return 'Password must be at least 8 characters.';
            if (!/[A-Za-z]/.test(value))  return 'Password must contain at least one letter.';
            if (!/\d/.test(value))        return 'Password must contain at least one number.';
            return null;
        },

        password_confirm: function (value) {
            const pwd = document.getElementById('password');
            if (value === '')             return 'Please confirm your password.';
            if (pwd && value !== pwd.value) return 'Passwords do not match.';
            return null;
        },

        agree: function (_, input) {
            if (!input.checked)           return 'Please agree to the Terms and Privacy Policy.';
            return null;
        }
    };

    /* ============================================
       2. STRENGTH METER
       ============================================ */

    const pwd    = document.getElementById('password');
    const fill   = document.getElementById('strength-fill');
    const text   = document.getElementById('strength-text');
    const meter  = document.getElementById('strength-meter');

    const LEVELS = [
        { label: 'Too weak', color: '#ef4444', width: '20%'  },
        { label: 'Weak',     color: '#f59e0b', width: '40%'  },
        { label: 'Fair',     color: '#eab308', width: '60%'  },
        { label: 'Good',     color: '#22c55e', width: '80%'  },
        { label: 'Strong',   color: '#10b981', width: '100%' }
    ];

    function scorePassword(val) {
        let score = 0;
        if (val.length >= 8)  score++;
        if (val.length >= 12) score++;
        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score++;
        if (/\d/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;
        return Math.min(score, LEVELS.length - 1);
    }

    function updateStrength(val) {
        if (!meter || !fill || !text) return;

        if (!val) {
            meter.classList.remove('is-visible');
            fill.style.width = '0%';
            text.textContent = '';
            return;
        }

        meter.classList.add('is-visible');
        const lvl = LEVELS[scorePassword(val)];
        fill.style.width      = lvl.width;
        fill.style.background = lvl.color;
        text.textContent      = lvl.label;
        text.style.color      = lvl.color;
    }

    /* ============================================
       3. PASSWORD VISIBILITY TOGGLES
       ============================================ */

    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                this.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                this.setAttribute('aria-label', 'Show password');
            }
        });
    });

    /* ============================================
       4. LIVE VALIDATION
       ============================================ */

    const form = document.querySelector('form');

    /**
     * Validate a single field. Shows or clears the error.
     * `showError` — whether to display the message (false while typing, true on blur)
     */
    function validateField(input, showError) {
        const name = input.name;
        if (!validators[name]) return true;

        const value = input.type === 'checkbox' ? null : input.value;
        const error = validators[name](value, input);

        if (error) {
            if (showError) setState(input, 'invalid', error);
            else           clearState(input);
            return false;
        }

        setState(input, 'valid', null);
        return true;
    }

    // Attach listeners to each field
    if (form) {
        const inputs = form.querySelectorAll('input[name]');

        inputs.forEach(function (input) {
            const name = input.name;
            if (!validators[name]) return;

            // While typing: only clear the error once it's fixed
            input.addEventListener('input', function () {
                if (input.value && !validators[name](input.value, input)) {
                    setState(input, 'valid', null);
                } else if (!input.value) {
                    clearState(input);
                }
            });

            // On blur: full validation with message
            input.addEventListener('blur', function () {
                if (!input.value && !input.required) return;
                validateField(input, true);
            });
        });

        // Password: strength + live re-validate confirm
        if (pwd) {
            pwd.addEventListener('input', function () {
                updateStrength(this.value);
                const confirm = document.getElementById('password_confirm');
                if (confirm && confirm.value) {
                    validateField(confirm, true);
                }
            });
        }

        // Confirm password: live match
        const confirmPwd = document.getElementById('password_confirm');
        if (confirmPwd) {
            confirmPwd.addEventListener('input', function () {
                validateField(this, this.value.length > 0);
            });
        }

        // Terms checkbox: live
        const agree = form.querySelector('input[name="agree"]');
        if (agree) {
            agree.addEventListener('change', function () {
                validateField(this, !this.checked);
            });
        }

        // On submit: validate everything, block if any fail
        form.addEventListener('submit', function (e) {
            let ok = true;
            let firstInvalid = null;

            inputs.forEach(function (input) {
                const name = input.name;
                if (!validators[name]) return;

                const valid = validateField(input, true);
                if (!valid) {
                    ok = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            if (!ok) {
                e.preventDefault();
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

})();

