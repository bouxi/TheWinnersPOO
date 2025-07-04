document.addEventListener('DOMContentLoaded', function () {
    // 👁️ Boutons "afficher/masquer mot de passe"
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            if (!input) return;

            input.type = input.type === 'password' ? 'text' : 'password';
            this.textContent = input.type === 'password' ? '👁️' : '🙈';
        });
    });

    // 🔐 Validation des règles de mot de passe
    document.querySelectorAll('input[type="password"]').forEach(input => {
        input.addEventListener('focus', handleFocus);
        input.addEventListener('input', handleInput);
    });

    function handleFocus(e) {
        const input = e.target;
        const id = extractId(input.id);
        const box = document.getElementById(`password-rules-${id}`);
        if (box) box.style.display = 'block';
    }

    function handleInput(e) {
        const input = e.target;
        const id = extractId(input.id);
        const val = input.value;

        const rules = {
            length: val.length >= 8,
            upper: /[A-Z]/.test(val),
            lower: /[a-z]/.test(val),
            digit: /\d/.test(val),
            special: /[^A-Za-z0-9]/.test(val)
        };

        // ✔️ ou ❌ par règle
        updateRule(`rule-length-${id}`, rules.length);
        updateRule(`rule-uppercase-${id}`, rules.upper);
        updateRule(`rule-lowercase-${id}`, rules.lower);
        updateRule(`rule-digit-${id}`, rules.digit);
        updateRule(`rule-special-${id}`, rules.special);

        // 🔋 jauge
        const strength = Object.values(rules).filter(v => v).length;
        const progress = document.querySelector(`#password-strength-bar-${id} .progress-bar-inner`);
        if (progress) {
            progress.style.width = (strength * 20) + '%';
            progress.style.backgroundColor = strength < 3 ? 'red' : strength < 5 ? 'orange' : 'limegreen';
        }

        // 🛑 message erreur
        const errorBox = document.getElementById(`password-error-${id}`);
        if (errorBox) {
            errorBox.style.display = strength < 5 ? 'block' : 'none';
        }

        // 🔐 Bloque le bouton si pas OK
        const submitBtn = input.closest('form')?.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = strength < 5;
        }
    }

    function updateRule(id, valid) {
        const li = document.getElementById(id);
        if (li) {
            li.textContent = (valid ? '✔️' : '❌') + ' ' + li.textContent.slice(2);
            li.style.color = valid ? 'limegreen' : 'white';
        }
    }

    function extractId(fullId) {
        if (fullId.includes('profile')) return 'profile';
        if (fullId.includes('register')) return 'register';
        if (fullId.includes('reset')) return 'reset';
        if (fullId.includes('login')) return 'login'; // ✅ Ajout pour page login
        return 'default';
    }

});
