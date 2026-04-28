document.addEventListener('DOMContentLoaded', function () {

    // ── Token génération ──
    const token = [...Array(32)].map(() => Math.random().toString(36)[2]).join('');
    const displayToken = document.getElementById('displayToken');
    if (displayToken) {
        displayToken.textContent = token.slice(0, 8) + '........' + token.slice(-8);
    }
    const paymentToken = document.getElementById('paymentToken');
    if (paymentToken) paymentToken.value = token;

    // ── Timer countdown ──
    let total = 14 * 60 + 59;
    const countdownEl = document.getElementById('countdown');
    const timerInterval = setInterval(() => {
        if (total <= 0) { clearInterval(timerInterval); return; }
        total--;
        const m = Math.floor(total / 60);
        const s = total % 60;
        if (countdownEl) countdownEl.textContent = `${m} min. ${String(s).padStart(2, '0')} sec.`;
    }, 1000);

    // ── Email toggle ──
    const showEmailCheckbox = document.getElementById('showEmail');
    const emailGroup = document.getElementById('emailGroup');
    if (showEmailCheckbox && emailGroup) {
        showEmailCheckbox.addEventListener('change', function () {
            emailGroup.style.display = this.checked ? '' : 'none';
        });
    }

    // ── Cash toggle ──
    window.toggleCashMode = function () {
        const isCash = document.getElementById('cashPayment').checked;
        const fields = ['cardNumberGroup', 'cardFields', 'cardNameGroup', 'emailGroup'];
        fields.forEach(id => {
            const el = document.getElementById(id);
            if (el) el.style.display = isCash ? 'none' : '';
        });
        const cashAlert = document.getElementById('cashAlert');
        if (cashAlert) cashAlert.style.display = isCash ? 'block' : 'none';

        const submitBtn = document.getElementById('submitBtn');
        const planPrice = submitBtn?.dataset.price || '';
        if (submitBtn) {
            submitBtn.textContent = isCash
                ? '💵 Confirmer paiement en espèces'
                : `Paiement ${planPrice}`;
        }

        document.getElementById('paymentMethod').value = isCash ? 'especes' : 'carte';
    };

    // ── Card number format ──
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = val.match(/.{1,4}/g)?.join(' ') || val;
        });
    }

    // ── Expiry date format (si input texte) ──
    const expiryDateInput = document.getElementById('expiryDate');
    if (expiryDateInput) {
        expiryDateInput.addEventListener('input', function () {
            let val = this.value.replace(/\D/g, '').substring(0, 4);
            if (val.length >= 3) val = val.substring(0, 2) + '/' + val.substring(2);
            this.value = val;
        });
    }

    // ── CVV : chiffres seulement ──
    const cvvInput = document.getElementById('cvvInput');
    if (cvvInput) {
        cvvInput.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').substring(0, 4);
        });
    }

    // ── Submit validation ──
    const form = document.getElementById('paymentForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const isCash = document.getElementById('cashPayment').checked;
            document.getElementById('paymentMethod').value = isCash ? 'especes' : 'carte';

            // ── Mode espèces ──
            if (isCash) {
                if (!confirm('Confirmez-vous le paiement en espèces ?')) {
                    e.preventDefault();
                }
                return;
            }

            // ── Récupérer les valeurs ──
            const cardName   = (document.getElementById('cardName')?.value   || '').trim();
            const cardNumber = (document.getElementById('cardNumber')?.value  || '').trim();
            const cvv        = (document.getElementById('cvvInput')?.value    || '').trim();

            // Expiry : soit select (mois/année), soit input texte
            const expiryMonth = document.getElementById('expiryMonth')?.value || '';
            const expiryYear  = document.getElementById('expiryYear')?.value  || '';
            const expiryDate  = expiryDateInput?.value?.trim() || '';

            // ── Vérification champs vides ──
            if (!cardName) {
                alert('❌ Veuillez saisir le nom du détenteur de la carte.');
                e.preventDefault(); return;
            }

            if (!cardNumber) {
                alert('❌ Veuillez saisir le numéro de carte.');
                e.preventDefault(); return;
            }

            // Expiry vide (select ou input)
            if (expiryMonth !== undefined && !expiryMonth) {
                alert('❌ Veuillez sélectionner le mois d\'expiration.');
                e.preventDefault(); return;
            }
            if (expiryYear !== undefined && !expiryYear) {
                alert('❌ Veuillez sélectionner l\'année d\'expiration.');
                e.preventDefault(); return;
            }
            if (expiryDateInput && !expiryDate) {
                alert('❌ Veuillez saisir la date d\'expiration.');
                e.preventDefault(); return;
            }

            if (!cvv) {
                alert('❌ Veuillez saisir le code de sûreté (CVV).');
                e.preventDefault(); return;
            }

            // ── Validations format ──
            if (!isValidCardNumber(cardNumber)) {
                alert('❌ Numéro de carte invalide !');
                e.preventDefault(); return;
            }

            if (expiryDateInput && !/^\d{2}\/\d{2}$/.test(expiryDate)) {
                alert('❌ Date d\'expiration invalide — format MM/AA');
                e.preventDefault(); return;
            }

            if (cvv.length < 3) {
                alert('❌ Code de sûreté invalide — minimum 3 chiffres.');
                e.preventDefault(); return;
            }
        });
    }

    // ── Luhn algorithm ──
    function isValidCardNumber(cardNumber) {
        const sanitized = cardNumber.replace(/\s+/g, '');
        if (sanitized.length < 13) return false;
        let sum = 0, doubleDigit = false;
        for (let i = sanitized.length - 1; i >= 0; i--) {
            let digit = parseInt(sanitized[i]);
            if (doubleDigit) { digit *= 2; if (digit > 9) digit -= 9; }
            sum += digit;
            doubleDigit = !doubleDigit;
        }
        return sum % 10 === 0;
    }

});