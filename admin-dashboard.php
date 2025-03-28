<?php
/**
 * Dashboard administrativo com gerenciador de cores (mantendo estilo original)
 * @package PEC_Color_Chooser
 * @version 2.4
 */

if (!defined('ABSPATH')) {
    exit;
}

function pec_display_dashboard() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'pec_colors';
    
    // Processa adição/remoção de cores
    if (isset($_POST['pec_add_color'])) {
        check_admin_referer('pec_manage_colors');
        
        $new_color = sanitize_text_field($_POST['new_color_name']);
        $new_hex = sanitize_hex_color($_POST['new_color_hex']);
        
        if ($new_color && $new_hex) {
            $custom_colors = get_option('pec_custom_colors', array());
            $custom_colors[$new_color] = $new_hex;
            update_option('pec_custom_colors', $custom_colors);
            
            echo '<div class="notice notice-success"><p>Cor adicionada com sucesso!</p></div>';
        }
    }
    
    if (isset($_GET['delete_color'])) {
        check_admin_referer('pec_delete_color');
        
        $color_to_delete = sanitize_key($_GET['delete_color']);
        $custom_colors = get_option('pec_custom_colors', array());
        
        if (array_key_exists($color_to_delete, $custom_colors)) {
            unset($custom_colors[$color_to_delete]);
            update_option('pec_custom_colors', $custom_colors);
            echo '<div class="notice notice-success"><p>Cor removida com sucesso!</p></div>';
        }
    }

    // Cores disponíveis (padrão + customizadas)
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
    
    // Consulta os dados (mantido original)
    $solicitacoes = $wpdb->get_results("
        SELECT nome, whatsapp, instagram, email, colors, data_pedido 
        FROM $table_name 
        ORDER BY data_pedido DESC
        LIMIT 100
    ");
    
    // Processa as cores para o gráfico (mantido original)
    $color_count = [];
    foreach ($solicitacoes as $solicitacao) {
        $cores = json_decode($solicitacao->colors, true);
        foreach ($cores as $cor => $quantidade) {
            if ($quantidade > 0) {
                $color_count[$cor] = ($color_count[$cor] ?? 0) + $quantidade;
            }
        }
    }
    
    // Carrega Chart.js (mantido original)
    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);
    ?>
    
    <div class="wrap">
        <h1>Relatório de Cores</h1>
        
        <!-- Seção do Gerenciador de Cores (NOVO) -->
        <div class="pec-section" style="margin-bottom: 30px;">
            <h2>Gerenciador de Cores</h2>
            
            <form method="POST" style="margin-bottom: 20px; background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
                <?php wp_nonce_field('pec_manage_colors'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="new_color_name">Nome da Cor</label></th>
                        <td>
                            <input type="text" id="new_color_name" name="new_color_name" required
                                   class="regular-text" placeholder="Ex: turquesa">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="new_color_hex">Cor (HEX)</label></th>
                        <td>
                            <input type="color" id="new_color_hex" name="new_color_hex" 
                                   value="#1E90FF" required style="height: 30px;">
                            <input type="text" class="regular-text" placeholder="#FFFFFF" 
                                   pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$" style="width: 100px; margin-left: 10px;">
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" name="pec_add_color" class="button button-primary">Adicionar Cor</button>
                </p>
            </form>
            
            <h3>Cores Disponíveis</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px;">
                <?php foreach ($color_hex as $cor => $hex): ?>
                    <div style="display: flex; align-items: center; background: #fff; border: 1px solid #ddd; padding: 5px 10px; border-radius: 3px;">
                        <div style="width: 20px; height: 20px; background: <?php echo esc_attr($hex); ?>; margin-right: 8px; border: 1px solid #ccc;"></div>
                        <span style="margin-right: 8px;"><?php echo esc_html(ucfirst($cor)); ?></span>
                        <span style="color: #666; font-family: monospace; margin-right: 8px;"><?php echo esc_html($hex); ?></span>
                        
                        <?php if (array_key_exists($cor, get_option('pec_custom_colors', []))): ?>
                            <a href="<?php echo esc_url(wp_nonce_url(
                                add_query_arg('delete_color', $cor), 
                                'pec_delete_color'
                            )); ?>" style="color: #a00; text-decoration: none;" onclick="return confirm('Tem certeza que deseja remover esta cor?')">
                                ×
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Seção do Gráfico (ORIGINAL) -->
        <div class="pec-section">
            <h2>Distribuição de Cores</h2>
            <div class="chart-container" style="height: 400px; margin-bottom: 40px;">
                <canvas id="color-chart"></canvas>
            </div>
        </div>
        
        <!-- Seção de Resumo por Cor (ORIGINAL) -->
        <div class="pec-section highlight">
            <h2>Resumo por Cor</h2>
            <div class="color-summary-grid">
                <?php foreach ($color_count as $cor => $total): ?>
                    <div class="color-summary-item" style="border-left: 5px solid <?php echo $color_hex[$cor]; ?>">
                        <div class="color-name"><?php echo ucfirst($cor); ?></div>
                        <div class="color-total"><?php echo number_format($total, 0, ',', '.'); ?> unidades</div>
                        <div class="color-percentage">
                            <?php echo round(($total / array_sum($color_count)) * 100, 1); ?>%
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Seção de Últimas Solicitações (ORIGINAL) -->
        <div class="pec-section">
            <h2>Últimas Solicitações</h2>
            <div class="requests-container">
                <?php foreach ($solicitacoes as $solicitacao): ?>
                    <div class="request-card">
                        <div class="request-header">
                            <span class="request-date"><?php echo date('d/m/Y H:i', strtotime($solicitacao->data_pedido)); ?></span>
                            <span class="request-name"><?php echo esc_html($solicitacao->nome); ?></span>
                        </div>
                        
                        <div class="request-contacts">
                            <span><i class="fas fa-phone"></i> <?php echo esc_html($solicitacao->whatsapp); ?></span>
                            <span><i class="fas fa-at"></i> <?php echo esc_html($solicitacao->email); ?></span>
                            <span><i class="fab fa-instagram"></i> <?php echo esc_html($solicitacao->instagram); ?></span>
                        </div>
                        
                        <div class="request-colors">
                            <?php 
                            $cores = json_decode($solicitacao->colors, true);
                            foreach ($cores as $cor => $qtd): 
                                if ($qtd > 0): ?>
                                    <span class="color-badge" style="background-color: <?php echo $color_hex[$cor]; ?>">
                                        <?php echo ucfirst($cor) . ': ' . $qtd; ?>
                                    </span>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <script>
    // Mantido o script original do gráfico
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('color-chart').getContext('2d');
        const colorData = <?php echo json_encode($color_count); ?>;
        const colorHex = <?php echo json_encode($color_hex); ?>;
        
        // Ordena as cores por quantidade
        const sortedColors = Object.keys(colorData).sort((a, b) => colorData[b] - colorData[a]);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sortedColors.map(cor => cor.charAt(0).toUpperCase() + cor.slice(1)),
                datasets: [{
                    label: 'Unidades Solicitadas',
                    data: sortedColors.map(cor => colorData[cor]),
                    backgroundColor: sortedColors.map(cor => colorHex[cor]),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' unidades';
                            }
                        }
                    }
                }
            }
        });

        // Sincroniza os inputs de cor (NOVO)
        const colorPicker = document.getElementById('new_color_hex');
        const hexInput = document.querySelector('input[type="text"][placeholder="#FFFFFF"]');
        
        colorPicker.addEventListener('input', () => {
            hexInput.value = colorPicker.value;
        });
        
        hexInput.addEventListener('input', () => {
            if (/^#[0-9A-F]{6}$/i.test(hexInput.value)) {
                colorPicker.value = hexInput.value;
            }
        });
    });
    </script>
    
    <style>
    /* Mantidos os estilos originais do dashboard */
    .pec-dashboard {
        font-family: 'Segoe UI', Roboto, sans-serif;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .pec-section {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 25px;
        margin-bottom: 30px;
    }
    
    .pec-section.highlight {
        border: 1px solid #e0e0e0;
        background: #f9f9f9;
    }
    
    .color-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .color-summary-item {
        background: white;
        padding: 15px;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .color-name {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .color-total {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .color-percentage {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .requests-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    
    .request-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        padding: 20px;
        transition: transform 0.2s;
    }
    
    .request-card:hover {
        transform: translateY(-3px);
    }
    
    .request-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .request-date {
        color: #7f8c8d;
        font-size: 14px;
    }
    
    .request-name {
        font-weight: 600;
    }
    
    .request-contacts {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        font-size: 14px;
    }
    
    .request-contacts span {
        background: #f5f5f5;
        padding: 5px 10px;
        border-radius: 4px;
    }
    
    .request-contacts i {
        margin-right: 5px;
        color: #555;
    }
    
    .request-colors {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .color-badge {
        color: white;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 500;
        text-shadow: 
            0.5px 0.5px 1px rgba(0,0,0,0.8),
            -0.5px -0.5px 1px rgba(0,0,0,0.8),
            0.5px -0.5px 1px rgba(0,0,0,0.8),
            -0.5px 0.5px 1px rgba(0,0,0,0.8);
        transition: all 0.2s ease;
    }
    
    /* Melhora específica para cores claras */
    .color-badge[style*="background-color: #FFFF00"],
    .color-badge[style*="background-color: #FFC0CB"],
    .color-badge[style*="background-color: #FFFFFF"] {
        text-shadow: 
            1px 1px 2px rgba(0,0,0,0.9),
            -1px -1px 2px rgba(0,0,0,0.9),
            1px -1px 2px rgba(0,0,0,0.9),
            -1px 1px 2px rgba(0,0,0,0.9);
        font-weight: 600;
    }
    
    /* Efeito hover para todos os badges */
    .color-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    </style>
    <?php
}