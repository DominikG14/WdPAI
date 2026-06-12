const form = document.querySelector("form");
const emailInput = form.querySelector('input[name="email"]');
const passwordInput = form.querySelector('input[name="password"]');
const confirmedPasswordInput = form.querySelector('input[name="password2"]');

function isEmail(email) {
    return /\S+@\S+\.\S+/.test(email) && email.length <= 255;
}

function isPasswordStrong(password) {
    return password.length >= 8
        && password.length <= 128
        && /[a-z]/.test(password)
        && /[A-Z]/.test(password)
        && /\d/.test(password);
}

function markValidation(element, condition) {
    !condition ? element.classList.add('no-valid') : element.classList.remove('no-valid');
}

function validateEmail() {
    markValidation(emailInput, isEmail(emailInput.value));
}

function validatePassword() {
    markValidation(passwordInput, isPasswordStrong(passwordInput.value));
    markValidation(confirmedPasswordInput, passwordInput.value === confirmedPasswordInput.value);
}

emailInput.addEventListener('input', validateEmail);
passwordInput.addEventListener('input', validatePassword);
confirmedPasswordInput.addEventListener('input', validatePassword);
