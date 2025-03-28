<?php
/**
 * Formulário de seleção de cores - Versão 3.4
 * @package PEC_Color_Chooser
 */

if (!defined('ABSPATH')) exit;

// Obtém cores disponíveis
$color_hex = array_merge(
    [
        'vermelho' => '#FF0000',
        'verde' => '#00FF00',
        'azul' => '#0000FF',
        'amarelo' => '#FFFF00',
        'roxo' => '#800080',
        'laranja' => '#FFA500',
        'rosa' => '#FFC0CB',
        'marrom' => '#A52A2A',
        'cinza' => '#808080',
        'preto' => '#000000'
    ],
    get_option('pec_custom_colors', [])
);

// Opções de gênero
$gender_options = [
    'Masculino' => 'Masculino / Male',
    'Feminino' => 'Feminino / Female',
    'Não-binário' => 'Não-binário / Non-binary',
    'Agênero' => 'Agênero / Agender',
    'Gênero fluido' => 'Gênero fluido / Genderfluid',
    'Transgênero' => 'Transgênero / Transgender',
    'Cisgênero' => 'Cisgênero / Cisgender',
    'Intersexo' => 'Intersexo / Intersex',
    'Dois-espíritos' => 'Dois-espíritos / Two-spirit',
    'Prefiro não informar' => 'Prefiro não informar / Prefer not to say',
    'Outro' => 'Outro / Other'
];

// Opções de etnia
$ethnicity_options = [
    'Branco(a)' => 'Branco(a) / White',
    'Negro(a)' => 'Negro(a) / Black',
    'Pardo(a)' => 'Pardo(a) / Mixed race',
    'Indígena' => 'Indígena / Indigenous',
    'Asiático(a)' => 'Asiático(a) / Asian',
    'Oriental' => 'Oriental / East Asian',
    'Árabe' => 'Árabe / Arab',
    'Judeu(a)' => 'Judeu(a) / Jewish',
    'Cigano(a)' => 'Cigano(a) / Romani',
    'Prefiro não informar' => 'Prefiro não informar / Prefer not to say',
    'Outro' => 'Outro / Other'
];

// Mensagens de status
if (isset($_GET['pec_status'])) {
    $status_messages = [
        'success' => [
            'class' => 'pec-message--success',
            'icon' => '✓',
            'text' => 'Solicitação enviada com sucesso! / Request submitted successfully!'
        ],
        'partial' => [
            'class' => 'pec-message--warning',
            'icon' => '!',
            'text' => 'Recebemos seu pedido (e-mail não enviado) / Request received (email not sent)'
        ],
        'error' => [
            'class' => 'pec-message--error',
            'icon' => '×',
            'text' => 'Erro no envio. Tente novamente. / Submission error. Please try again.'
        ]
    ];
    
    if (isset($status_messages[$_GET['pec_status']])) {
        $message = $status_messages[$_GET['pec_status']];
        echo '<div class="pec-message ' . esc_attr($message['class']) . '">';
        echo '<span class="pec-message__icon">' . esc_html($message['icon']) . '</span>';
        echo esc_html($message['text']);
        echo '</div>';
    }
}
?>

<div class="pec-form">
    <form id="pec-color-form" method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="pec_color_form">
        <?php wp_nonce_field('pec_form_action', 'pec_nonce'); ?>
        
        <div class="pec-form__section">
            <h2 class="pec-form__title">Seus Dados / Your Information</h2>
            
            <div class="pec-form__grid">
                <div class="pec-input">
                    <label class="pec-input__label">Nome completo / Full name*</label>
                    <input type="text" name="nome" class="pec-input__field" required
                           placeholder="Ex: João Silva / John Smith">
                </div>
                
                <div class="pec-input">
                    <label class="pec-input__label">WhatsApp/Telefone / Phone number*</label>
                    <input type="tel" name="whatsapp" class="pec-input__field" required
                           placeholder="(DDD) 00000-0000 ou +Código País / or +Country Code">
                    <span class="pec-input__hint">Inclua DDD ou código do país / Include area code or country code</span>
                </div>
                
                <div class="pec-input pec-input--instagram">
                    <label class="pec-input__label">Instagram*</label>
                    <div class="pec-input__wrapper">
                        <span class="pec-input__prefix">@</span>
                        <input type="text" name="instagram" class="pec-input__field" required
                               placeholder="seuinstagram / yourinstagram">
                    </div>
                </div>
                
                <div class="pec-input">
                    <label class="pec-input__label">E-mail / Email*</label>
                    <input type="email" name="email" class="pec-input__field" required
                           placeholder="seu@email.com / your@email.com">
                </div>
                
                <div class="pec-input">
                    <label class="pec-input__label">Gênero / Gender</label>
                    <select name="genero" class="pec-input__field">
                        <?php foreach ($gender_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="outro-genero-container" style="display: none; margin-top: 8px;">
                        <input type="text" name="outro_genero" class="pec-input__field" 
                               placeholder="Especifique seu gênero / Specify your gender">
                    </div>
                </div>
                
                <div class="pec-input">
                    <label class="pec-input__label">Etnia / Ethnicity</label>
                    <select name="etnia" class="pec-input__field">
                        <?php foreach ($ethnicity_options as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="outro-etnia-container" style="display: none; margin-top: 8px;">
                        <input type="text" name="outro_etnia" class="pec-input__field" 
                               placeholder="Especifique sua etnia / Specify your ethnicity">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pec-form__section">
            <div class="pec-form__header">
                <h2 class="pec-form__title">Escolha as Cores / Select Colors</h2>
                <div class="pec-form__counter">
                    <span id="pec-total-count">0</span>/10 unidades / units
                </div>
            </div>
            
            <div class="pec-colors">
                <?php foreach ($color_hex as $cor => $hex): ?>
                    <div class="pec-color">
                        <div class="pec-color__box" style="background-color: <?php echo esc_attr($hex); ?>"></div>
                        <div class="pec-color__name"><?php echo esc_html(ucfirst($cor)); ?></div>
                        <select name="colors[<?php echo esc_attr($cor); ?>]" class="pec-color__qty">
                            <?php for ($i = 0; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="pec-form__footer">
                <div class="pec-error" id="pec-error-message">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M11 15h2v2h-2zm0-8h2v6h-2zm.99-5C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"/>
                    </svg>
                    Limite de 10 unidades excedido / 10 units limit exceeded
                </div>
                <button type="submit" class="pec-button">
                    <span class="pec-button__text">Enviar Pedido / Submit Request</span>
                    <span class="pec-button__loader"></span>
                </button>
            </div>
        </div>
    </form>
</div>

<style>
/* Sistema de cores */
:root {
    --pec-primary: #4361ee;
    --pec-primary-hover: #3a56d4;
    --pec-success: #4cc9f0;
    --pec-warning: #f8961e;
    --pec-error: #f94144;
    --pec-light: #f8f9fa;
    --pec-dark: #212529;
    --pec-gray: #6c757d;
    --pec-border: #e9ecef;
    --pec-radius: 8px;
}

/* Reset e base */
.pec-form * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

.pec-form {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    line-height: 1.5;
    color: var(--pec-dark);
}

/* Mensagens */
.pec-message {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    margin-bottom: 24px;
    border-radius: var(--pec-radius);
    font-weight: 500;
    background-color: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.pec-message__icon {
    margin-right: 10px;
    font-weight: bold;
}

.pec-message--success {
    color: var(--pec-success);
    border-left: 4px solid var(--pec-success);
}

.pec-message--warning {
    color: var(--pec-warning);
    border-left: 4px solid var(--pec-warning);
}

.pec-message--error {
    color: var(--pec-error);
    border-left: 4px solid var(--pec-error);
}

/* Seções */
.pec-form__section {
    margin-bottom: 24px;
    padding: 20px;
    background: white;
    border-radius: var(--pec-radius);
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
}

.pec-form__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.pec-form__title {
    font-size: 18px;
    font-weight: 600;
    color: var(--pec-dark);
}

.pec-form__counter {
    font-size: 14px;
    color: var(--pec-gray);
}

.pec-form__counter span {
    font-weight: 600;
    color: var(--pec-primary);
}

.pec-form__footer {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid var(--pec-border);
}

/* Grid de inputs */
.pec-form__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 16px;
}

/* Labels */
.pec-input__label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--pec-dark);
}

.pec-input__label:after {
    content: '*';
    color: var(--pec-error);
    margin-left: 4px;
    display: none;
}

.pec-input__label.required:after {
    display: inline;
}

/* Inputs */
.pec-input {
    margin-bottom: 16px;
}

.pec-input__field {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--pec-border);
    border-radius: var(--pec-radius);
    font-size: 14px;
    transition: all 0.2s;
    background: var(--pec-light);
}

.pec-input__field:focus {
    outline: none;
    border-color: var(--pec-primary);
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
}

.pec-input__field::placeholder {
    color: #adb5bd;
    font-size: 13px;
}

.pec-input__hint {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: var(--pec-gray);
    font-style: italic;
}

/* Instagram input */
.pec-input--instagram .pec-input__wrapper {
    display: flex;
}

.pec-input__prefix {
    padding: 0 12px;
    font-size: 14px;
    color: var(--pec-gray);
    background: var(--pec-light);
    border: 1px solid var(--pec-border);
    border-right: none;
    border-radius: var(--pec-radius) 0 0 var(--pec-radius);
    display: flex;
    align-items: center;
}

.pec-input--instagram .pec-input__field {
    border-radius: 0 var(--pec-radius) var(--pec-radius) 0;
}

/* Select dropdown */
select.pec-input__field {
    appearance: none;
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 16px;
    padding-right: 36px;
}

/* Grid de cores (8 por linha) */
.pec-colors {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 16px;
}

.pec-color {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.pec-color__box {
    width: 100%;
    height: 60px;
    border-radius: var(--pec-radius);
    position: relative;
    overflow: hidden;
    box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
}

.pec-color:hover .pec-color__box {
    transform: scale(1.05);
}

/* Nome da cor */
.pec-color__name {
    font-size: 12px;
    font-weight: 600;
    color: var(--pec-dark);
    text-align: center;
    width: 100%;
    padding: 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.pec-color__qty {
    width: 100%;
    padding: 8px;
    border: 1px solid var(--pec-border);
    border-radius: var(--pec-radius);
    font-size: 13px;
    text-align: center;
    cursor: pointer;
    background: white;
    transition: all 0.2s;
}

.pec-color__qty:focus {
    outline: none;
    border-color: var(--pec-primary);
    box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
}

/* Mensagem de erro */
.pec-error {
    display: none;
    align-items: center;
    gap: 6px;
    color: var(--pec-error);
    font-size: 13px;
    margin-bottom: 12px;
}

.pec-error svg {
    width: 14px;
    height: 14px;
}

/* Botão */
.pec-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 12px 24px;
    background-color: var(--pec-primary);
    color: white;
    border: none;
    border-radius: var(--pec-radius);
    font-size: 15px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    overflow: hidden;
    width: 100%;
    max-width: 300px;
}

.pec-button:hover {
    background-color: var(--pec-primary-hover);
    transform: translateY(-1px);
}

.pec-button:active {
    transform: translateY(0);
}

.pec-button__loader {
    display: none;
    position: absolute;
    width: 20px;
    height: 20px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Estados */
.pec-button.is-loading .pec-button__text {
    visibility: hidden;
}

.pec-button.is-loading .pec-button__loader {
    display: block;
}

/* Responsividade */
@media (max-width: 700px) {
    .pec-colors {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
    
    .pec-form__grid {
        grid-template-columns: 1fr;
    }
    
    .pec-color__name {
        font-size: 11px;
    }
}

@media (max-width: 480px) {
    .pec-form {
        padding: 15px;
    }
    
    .pec-form__section {
        padding: 16px;
    }
    
    .pec-color__box {
        height: 50px;
    }
    
    .pec-color__name {
        font-size: 10px;
    }
    
    .pec-input__label {
        font-size: 13px;
    }
    
    .pec-input__field {
        padding: 10px 12px;
        font-size: 13px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pec-color-form');
    const colorSelects = form.querySelectorAll('.pec-color__qty');
    const totalDisplay = document.getElementById('pec-total-count');
    const errorMessage = document.getElementById('pec-error-message');
    const submitButton = form.querySelector('.pec-button');
    const genderSelect = form.querySelector('select[name="genero"]');
    const outroGeneroContainer = document.getElementById('outro-genero-container');
    const ethnicitySelect = form.querySelector('select[name="etnia"]');
    const outroEtniaContainer = document.getElementById('outro-etnia-container');
    const MAX_TOTAL = 10;

    // Mostrar campos "Outro" quando selecionados
    genderSelect.addEventListener('change', function() {
        outroGeneroContainer.style.display = this.value === 'Outro' ? 'block' : 'none';
    });

    ethnicitySelect.addEventListener('change', function() {
        outroEtniaContainer.style.display = this.value === 'Outro' ? 'block' : 'none';
    });

    // Marcar campos obrigatórios
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        const label = form.querySelector(`label[for="${field.id}"]`);
        if (label) {
            label.classList.add('required');
        }
    });

    // Atualiza o total de unidades
    function updateTotal() {
        let total = 0;
        colorSelects.forEach(select => {
            total += parseInt(select.value);
        });
        
        totalDisplay.textContent = total;
        
        if (total > MAX_TOTAL) {
            errorMessage.style.display = 'flex';
            submitButton.disabled = true;
            totalDisplay.style.color = 'var(--pec-error)';
        } else {
            errorMessage.style.display = 'none';
            submitButton.disabled = false;
            totalDisplay.style.color = '';
        }
    }

    // Validação do formulário
    form.addEventListener('submit', function(e) {
        const total = parseInt(totalDisplay.textContent);
        
        if (total > MAX_TOTAL) {
            e.preventDefault();
            return;
        }
        
        if (total === 0) {
            e.preventDefault();
            alert('Por favor, selecione pelo menos uma cor / Please select at least one color');
            return;
        }
        
        // Validação básica dos campos obrigatórios
        let isValid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.style.borderColor = 'var(--pec-error)';
                field.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios / Please fill all required fields');
            return;
        }
        
        // Mostra loading
        submitButton.classList.add('is-loading');
    });

    // Remove o estilo de erro quando o usuário começa a digitar
    form.querySelectorAll('input, select').forEach(field => {
        field.addEventListener('input', function() {
            this.style.borderColor = '';
        });
    });

    // Atualiza quando muda qualquer quantidade
    colorSelects.forEach(select => {
        select.addEventListener('change', updateTotal);
    });

    // Atualiza inicialmente
    updateTotal();
});
</script>