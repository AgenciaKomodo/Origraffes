/**
 * Script de controle do formulário de seleção de cores
 * Validações e interações do usuário
 * @version 1.1
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('color-order-form');
    const allSelects = document.querySelectorAll('.color-qty');
    const totalDisplay = document.getElementById('total-count');
    const errorMsg = document.querySelector('.error-message');
    const submitBtn = document.querySelector('.submit-btn');
    
    let currentTotal = 0;
    const MAX_TOTAL = 10;
    const MAX_PER_COLOR = 10;
    
    // Atualiza o contador total
    function updateTotal() {
        currentTotal = Array.from(allSelects).reduce((sum, select) => {
            return sum + parseInt(select.value);
        }, 0);
        
        totalDisplay.textContent = currentTotal;
        
        const isOverLimit = currentTotal > MAX_TOTAL;
        errorMsg.style.display = isOverLimit ? 'block' : 'none';
        submitBtn.disabled = isOverLimit;
        totalDisplay.style.color = isOverLimit ? 'red' : '';
    }
    
    // Validação por cor
    allSelects.forEach(select => {
        select.addEventListener('change', function() {
            if(this.value > MAX_PER_COLOR) {
                this.value = MAX_PER_COLOR;
                alert(`Máximo ${MAX_PER_COLOR} unidades por cor`);
            }
            updateTotal();
        });
    });
    
    // Validação no envio
    form.addEventListener('submit', function(e) {
        // Validação do total
        if(currentTotal > MAX_TOTAL) {
            e.preventDefault();
            alert('Reduza sua seleção para no máximo 10 unidades no total');
            return;
        }
        
        // Validação dos campos
        const fields = {
            nome: form.querySelector('input[name="nome"]').value.trim(),
            whatsapp: form.querySelector('input[name="whatsapp"]').value.trim(),
            instagram: form.querySelector('input[name="instagram"]').value.trim(),
            email: form.querySelector('input[name="email"]').value.trim()
        };
        
        // Verifica campos vazios
        for(const [field, value] of Object.entries(fields)) {
            if(!value) {
                e.preventDefault();
                alert(`Por favor, preencha o campo ${field}`);
                return;
            }
        }
        
        // Validações específicas
        if(!/^(\d{10,15})$/.test(fields.whatsapp)) {
            e.preventDefault();
            alert('WhatsApp inválido. Use apenas números com DDD.');
            return;
        }
        
        if(!fields.instagram.startsWith('@')) {
            e.preventDefault();
            alert('Instagram deve começar com @');
            return;
        }
        
        // Feedback visual durante o envio
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
        
        // O formulário será submetido normalmente se todas as validações passarem
    });
});

// Verifica se há parâmetro de sucesso na URL
if (new URLSearchParams(window.location.search).has('pec_status')) {
    alert('Sua solicitação foi enviada com sucesso! Entraremos em contato em breve.');
    
    // Opcional: Rolagem suave para o formulário
    document.getElementById('color-order-form').scrollIntoView({
        behavior: 'smooth'
    });
}