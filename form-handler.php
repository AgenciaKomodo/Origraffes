<?php
/**
 * Processador do formulário de seleção de cores
 * @package PEC_Color_Chooser
 * @version 2.1
 */

if (!defined('ABSPATH')) {
    exit;
}

function pec_process_color_form() {
    // Verificação de segurança
    if (!isset($_POST['pec_nonce']) || !wp_verify_nonce($_POST['pec_nonce'], 'pec_form_action')) {
        wp_die('Requisição inválida');
         $cores_permitidas = [
        'vermelho', 'verde', 'azul', 'amarelo', 'roxo',
        'laranja', 'rosa', 'marrom', 'cinza', 'preto',
        'turquesa', 'azul-goiaba','framboesa','magenta' // ADICIONE AS NOVAS CORRES AQUI
    ];
    
    $cores_selecionadas = [];
    foreach ($_POST['colors'] as $cor => $quantidade) {
        if (in_array($cor, $cores_permitidas)) {
            $cores_selecionadas[$cor] = (int)$quantidade;
        }
    }
    
    // Garanta que está salvando como JSON
    $dados['colors'] = json_encode($cores_selecionadas);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'pec_colors';

    // Processa os dados
    $dados = [
        'nome' => sanitize_text_field($_POST['nome']),
        'whatsapp' => sanitize_text_field($_POST['whatsapp']),
        'instagram' => sanitize_text_field($_POST['instagram']),
        'email' => sanitize_email($_POST['email']),
        'colors' => json_encode(array_map('intval', $_POST['colors'])),
        'data_pedido' => current_time('mysql', 1)
    ];

    // Insere no banco de dados
    $insert_result = $wpdb->insert($table_name, $dados);

    // Configuração dos e-mails
    $user_email = $dados['email'];
    $admin_email = 'contato@dnaurbano.com.br'; // Substitua se necessário
    
    // Assunto dos e-mails
    $assunto_user = "Confirmação da sua solicitação - DNA Urbano";
    $assunto_admin = "Nova solicitação de cores - " . $dados['nome'];
    
    // Corpo da mensagem
    $message = "Prezado(a) " . $dados['nome'] . ",\n\n";
    $message .= "Confirmamos o recebimento da sua solicitação com as seguintes cores:\n\n";
    
    foreach (json_decode($dados['colors'], true) as $cor => $quantidade) {
        if ($quantidade > 0) {
            $message .= "- " . ucfirst($cor) . ": " . $quantidade . " unidade(s)\n";
        }
    }
    
    $message .= "\nData da solicitação: " . date('d/m/Y H:i', strtotime($dados['data_pedido']));
    $message .= "\n\nAgradecemos pelo envio!!\n";
    $message .= "Equipe DNA Urbano";
    
    // Cabeçalhos
    $headers = [
        'From: DNA Urbano <contato@dnaurbano.com.br>',
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: contato@dnaurbano.com.br'
    ];

    // Envia e-mails
    $user_email_sent = wp_mail($user_email, $assunto_user, $message, $headers);
    $admin_email_sent = wp_mail($admin_email, $assunto_admin, $message, $headers);

    // Redirecionamento com status
    $status = ($insert_result !== false) ? ($user_email_sent ? 'success' : 'partial') : 'error';
    wp_redirect(add_query_arg('pec_status', $status, wp_get_referer()));
    exit;
}

add_action('admin_post_nopriv_pec_color_form', 'pec_process_color_form');
add_action('admin_post_pec_color_form', 'pec_process_color_form');