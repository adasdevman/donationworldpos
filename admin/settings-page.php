<?php
if (!defined('ABSPATH')) exit;

class CPF_Settings_Page {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_settings_menu() {
        add_submenu_page(
            'cpf-forms',
            'Paramètres',
            'Paramètres',
            'manage_options',
            'cpf-settings',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings() {
        register_setting('cpf_settings', 'cpf_default_payment_methods');
        register_setting('cpf_settings', 'cpf_success_page');
        register_setting('cpf_settings', 'cpf_error_page');

        add_settings_section(
            'cpf_general_settings',
            'Paramètres généraux',
            array($this, 'render_section_info'),
            'cpf-settings'
        );

        add_settings_field(
            'cpf_default_payment_methods',
            'Méthodes de paiement par défaut',
            array($this, 'render_payment_methods_field'),
            'cpf-settings',
            'cpf_general_settings'
        );

        add_settings_field(
            'cpf_success_page',
            'Page de succès',
            array($this, 'render_page_select_field'),
            'cpf-settings',
            'cpf_general_settings',
            array('field' => 'cpf_success_page')
        );

        add_settings_field(
            'cpf_error_page',
            'Page d\'erreur',
            array($this, 'render_page_select_field'),
            'cpf-settings',
            'cpf_general_settings',
            array('field' => 'cpf_error_page')
        );
    }

    public function render_section_info() {
        echo '<p>Configurez les paramètres généraux du plugin.</p>';
    }

    public function render_payment_methods_field() {
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
        $selected = get_option('cpf_default_payment_methods', array());

        foreach ($payment_gateways as $gateway) {
            printf(
                '<label><input type="checkbox" name="cpf_default_payment_methods[]" value="%s" %s> %s</label><br>',
                esc_attr($gateway->id),
                checked(in_array($gateway->id, $selected), true, false),
                esc_html($gateway->get_title())
            );
        }
    }

    public function render_page_select_field($args) {
        wp_dropdown_pages(array(
            'name' => $args['field'],
            'selected' => get_option($args['field']),
            'show_option_none' => 'Sélectionnez une page',
        ));
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('cpf_settings');
                do_settings_sections('cpf-settings');
                submit_button('Enregistrer les paramètres');
                ?>
            </form>
        </div>
        <?php
    }
} 