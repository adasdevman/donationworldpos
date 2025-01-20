<?php
class CPF_Shortcode_Generator {
    public function __construct() {
        add_shortcode('custom_payment_form', array($this, 'render_payment_form'));
    }

    public function render_payment_form($atts) {
        $atts = shortcode_atts(array(
            'id' => 0
        ), $atts);

        if (empty($atts['id'])) {
            return 'ID du formulaire non spécifié';
        }

        // Récupérer le formulaire depuis la base de données
        global $wpdb;
        $form = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cpf_forms WHERE id = %d",
            $atts['id']
        ));

        if (!$form) {
            return 'Formulaire non trouvé';
        }

        $fields = json_decode($form->fields, true);
        $product_id = $form->product_id;
        $payment_methods = json_decode($form->payment_methods, true);

        ob_start();
        include CPF_PLUGIN_DIR . 'templates/payment-form.php';
        return ob_get_clean();
    }
} 