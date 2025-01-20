<?php
/*
Plugin Name: DonationWC
Description: Solution flexible de formulaires de dons pour WooCommerce. Créez des formulaires de dons personnalisables avec des champs configurables, sélectionnez vos méthodes de paiement préférées et gérez facilement vos campagnes de dons. Intégration simple par shortcode et paiement direct via WooCommerce.
Version: 1.0
Author: Benjamin Franck Adagharagba
Author URI: https://github.com/votre-github
Text Domain: donationwc
Domain Path: /languages
Requires at least: 5.0
Requires PHP: 7.2
WC requires at least: 3.0
WC tested up to: 8.0
*/

if (!defined('ABSPATH')) {
    exit;
}

define('CPF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CPF_PLUGIN_URL', plugin_dir_url(__FILE__));

// Vérifier si WooCommerce est activé
function cpf_check_woocommerce() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>Custom Payment Forms nécessite WooCommerce pour fonctionner.</p></div>';
        });
        return false;
    }
    return true;
}

// Initialisation du plugin
function cpf_init() {
    if (!cpf_check_woocommerce()) return;
    
    require_once CPF_PLUGIN_DIR . 'admin/class-form-manager.php';
    require_once CPF_PLUGIN_DIR . 'admin/settings-page.php';
    require_once CPF_PLUGIN_DIR . 'includes/class-form-handler.php';
    require_once CPF_PLUGIN_DIR . 'includes/class-shortcode-generator.php';
    
    // Initialiser les classes
    new CPF_Form_Manager();
    new CPF_Settings_Page();
    new CPF_Form_Handler();
    new CPF_Shortcode_Generator();
}
add_action('plugins_loaded', 'cpf_init');

// Activation du plugin
function cpf_activate() {
    // Créer la table pour stocker les modèles de formulaire
    global $wpdb;
    $table_name = $wpdb->prefix . 'cpf_forms';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        fields text NOT NULL,
        product_id int(11) NOT NULL,
        payment_methods text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'cpf_activate'); 