<?php
/*
Plugin Name: Plugin de Escolha de Cores
Description: Permite que os usuários escolham até 10 cores e registra essas escolhas, exibindo um dashboard com cores e dados dos usuários.
Version: 1.0
Author: Seu Nome
*/

if (!defined('ABSPATH')) {
    exit; // Protege contra acesso direto
}

// ==== ADICIONE APENAS ESTE BLOCO DE CÓDIGO ==== //
// 1. Cria a tabela no banco de dados ao ativar o plugin
function pec_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pec_colors';
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        nome varchar(100) NOT NULL,
        whatsapp varchar(20) NOT NULL,
        instagram varchar(50) NOT NULL,
        email varchar(100) NOT NULL,
        colors text NOT NULL,
        data_pedido datetime NOT NULL,
        PRIMARY KEY (id)
    ) ".$wpdb->get_charset_collate().";";

    require_once(ABSPATH.'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'pec_create_table');

// 2. Processa o envio do formulário e salva no banco de dados
function pec_save_form_data() {
    if (isset($_POST['nome']) && isset($_POST['colors'])) {
        global $wpdb;
        
        $wpdb->insert($wpdb->prefix.'pec_colors', [
            'nome'       => sanitize_text_field($_POST['nome']),
            'whatsapp'   => sanitize_text_field($_POST['whatsapp']),
            'instagram'  => sanitize_text_field($_POST['instagram']),
            'email'      => sanitize_email($_POST['email']),
            'colors'     => json_encode($_POST['colors']), // Armazena como JSON
            'data_pedido'=> current_time('mysql')
        ]);

        // Redireciona de volta ao formulário sem erro 404
        wp_redirect($_SERVER['HTTP_REFERER'] . '?pec_success=1');
        exit;
    }
}
add_action('admin_post_nopriv_pec_save_colors', 'pec_save_form_data'); // Usuários não logados
add_action('admin_post_pec_save_colors', 'pec_save_form_data');        // Usuários logados

// 3. Adiciona o shortcode (se já não existir)
add_shortcode('pec_form', function() {
    ob_start();
    include plugin_dir_path(__FILE__).'includes/form.php';
    return ob_get_clean();
});
// ==== FIM DO BLOCO ADICIONAL ==== //
// Inclui os arquivos principais do plugin
include_once plugin_dir_path(__FILE__) . 'includes/form-handler.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-dashboard.php';

// Registra os scripts e estilos
function pec_register_scripts() {
    wp_enqueue_style('pec-style', plugin_dir_url(__FILE__) . 'assets/style.css');
    wp_enqueue_script('pec-script', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);
}

add_action('wp_enqueue_scripts', 'pec_register_scripts');

// Registra o shortcode para exibir o formulário
function pec_form_shortcode() {
    ob_start();
    include plugin_dir_path(__FILE__) . 'includes/form.php'; // Aqui estamos incluindo o arquivo do formulário
    return ob_get_clean();
}

add_shortcode('pec_form', 'pec_form_shortcode');

// Registra o menu de administração
add_action('admin_menu', 'pec_admin_menu');

function pec_admin_menu() {
    add_menu_page(
        'Dashboard de Cores',   // Título da página
        'Dashboard de Cores',   // Nome no menu
        'manage_options',       // Capacidade necessária para visualizar
        'pec-dashboard',        // Slug da página
        'pec_display_dashboard',// Função que exibe o conteúdo da página
        'dashicons-palmtree',   // Ícone do menu
        6                       // Posição no menu
    );
}
?>
