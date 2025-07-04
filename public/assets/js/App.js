document.addEventListener('DOMContentLoaded', function () {
    // 👁️ 1. Affichage / Masquage du mot de passe quand on clique sur l'œil
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.dataset.target; // Récupère l'ID du champ à cibler
            const input = document.getElementById(targetId);
            if (!input) return;

            // Alterne entre type "password" et "text"
            input.type = input.type === 'password' ? 'text' : 'password';
            this.textContent = input.type === 'password' ? '👁️' : '🙈'; // Change l'icône
        });
    });

    // 🔐 2. Sélectionne tous les champs de type password
    document.querySelectorAll('input[type="password"]').forEach(input => {
        const id = extractId(input.id);
        if (!id) return; // Si le champ vient de la page login => skip

        input.addEventListener('focus', handleFocus);   // Affiche les règles au focus
        input.addEventListener('input', handleInput);   // Valide dynamiquement au changement
    });

    /**
     * ✏️ handleFocus : Affiche le bloc des règles de mot de passe quand le champ reçoit le focus
     */
    function handleFocus(e) {
        const input = e.target;
        const id = extractId(input.id);
        const box = document.getElementById(`password-rules-${id}`);
        if (box) box.style.display = 'block';
    }

    /**
     * 🔎 handleInput : Vérifie chaque critère de sécurité à chaque frappe
     */
    function handleInput(e) {
        const input = e.target;
        const id = extractId(input.id);
        if (!id) return;

        const val = input.value;

        // Définition des règles à vérifier
        const rules = {
            length: val.length >= 8,
            upper: /[A-Z]/.test(val),
            lower: /[a-z]/.test(val),
            digit: /\d/.test(val),
            special: /[^A-Za-z0-9]/.test(val)
        };

        // ✅ Met à jour chaque règle (✔️ ou ❌)
        updateRule(`rule-length-${id}`, rules.length);
        updateRule(`rule-uppercase-${id}`, rules.upper);
        updateRule(`rule-lowercase-${id}`, rules.lower);
        updateRule(`rule-digit-${id}`, rules.digit);
        updateRule(`rule-special-${id}`, rules.special);

        // 🔋 Met à jour la jauge de progression visuelle
        const strength = Object.values(rules).filter(v => v).length; // Compte les règles validées
        const progress = document.querySelector(`#password-strength-bar-${id} .progress-bar-inner`);
        if (progress) {
            progress.style.width = (strength * 20) + '%'; // 20% par règle validée
            progress.style.backgroundColor = strength < 3 ? 'red' : strength < 5 ? 'orange' : 'limegreen';
        }

        // 🛑 Affiche un message d'erreur si toutes les règles ne sont pas respectées
        const errorBox = document.getElementById(`password-error-${id}`);
        if (errorBox) {
            errorBox.style.display = strength < 5 ? 'block' : 'none';
        }

        // 🔐 Active ou désactive le bouton "valider"
        const submitBtn = input.closest('form')?.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = strength < 5;
        }
    }

    /**
     * ✔️ updateRule : Met à jour le style et le symbole d'une règle selon sa validité
     */
    function updateRule(id, valid) {
        const li = document.getElementById(id);
        if (li) {
            li.textContent = (valid ? '✔️' : '❌') + ' ' + li.textContent.slice(2);
            li.style.color = valid ? 'limegreen' : 'white';
        }
    }

    /**
     * 🧠 extractId : Identifie le contexte de la page selon l'ID du champ
     * Sert à activer ou non la validation selon la page : register, profile, reset
     * Retourne `null` sur la page login (aucune règle de sécurité à appliquer)
     */
    function extractId(fullId) {
        if (fullId.includes('login')) return null;
        return fullId.includes('profile') ? 'profile' :
            fullId.includes('register') ? 'register' :
                fullId.includes('reset') ? 'reset' : null;
    }

});
