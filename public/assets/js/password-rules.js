function setupPasswordRules(id, inputId = 'password', submitButtonSelector = 'form button[type="submit"]') {
    const passwordInput = document.getElementById(inputId);
    const rulesBox = document.getElementById(`password-rules-${id}`);
    const strengthBar = document.getElementById(`password-strength-bar-${id}`)?.querySelector('.progress-bar-inner');
    const errorBox = document.getElementById(`password-error-${id}`);
    const submitButton = document.querySelector(submitButtonSelector);

    const ruleLength = document.getElementById(`rule-length-${id}`);
    const ruleUppercase = document.getElementById(`rule-uppercase-${id}`);
    const ruleLowercase = document.getElementById(`rule-lowercase-${id}`);
    const ruleDigit = document.getElementById(`rule-digit-${id}`);
    const ruleSpecial = document.getElementById(`rule-special-${id}`);

    if (!passwordInput || !rulesBox) return;

    passwordInput.addEventListener('focus', () => {
        rulesBox.style.display = 'block';
    });

    passwordInput.addEventListener('blur', () => {
        setTimeout(() => { rulesBox.style.display = 'none'; }, 200);
    });

    passwordInput.addEventListener('input', () => {
        const value = passwordInput.value;

        const validations = [
            value.length >= 8,
            /[A-Z]/.test(value),
            /[a-z]/.test(value),
            /[0-9]/.test(value),
            /[^A-Za-z0-9]/.test(value)
        ];

        const score = validations.filter(v => v).length;

        updateRule(ruleLength, validations[0]);
        updateRule(ruleUppercase, validations[1]);
        updateRule(ruleLowercase, validations[2]);
        updateRule(ruleDigit, validations[3]);
        updateRule(ruleSpecial, validations[4]);

        // Mise à jour barre
        if (strengthBar) {
            const width = (score / 5) * 100;
            strengthBar.style.width = width + '%';

            if (score <= 2) {
                strengthBar.style.backgroundColor = 'red';
            } else if (score === 3 || score === 4) {
                strengthBar.style.backgroundColor = 'orange';
            } else {
                strengthBar.style.backgroundColor = 'green';
            }
        }

        // Gestion de l’erreur + du bouton
        const allValid = score === 5;

        if (errorBox) {
            errorBox.style.display = allValid ? 'none' : 'block';
        }

        if (submitButton) {
            submitButton.disabled = !allValid;
        }
    });

    function updateRule(element, isValid) {
        element.classList.toggle('valid', isValid);
        element.classList.toggle('invalid', !isValid);

        const icon = element.querySelector('.rule-icon');
        if (icon) {
            icon.textContent = isValid ? '✔️' : '❌';
        }
    }
}
