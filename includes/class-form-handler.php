<?php
if (!defined('ABSPATH')) exit;

class CPF_Form_Handler {
    public function __construct() {
        add_action('init', array($this, 'handle_form_submission'));
    }

    public function handle_form_submission() {
        if (!isset($_POST['submit_payment']) || !isset($_POST['form_id'])) {
            return;
        }

        $form_id = intval($_POST['form_id']);
        
        // Récupérer les informations du formulaire
        global $wpdb;
        $form = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cpf_forms WHERE id = %d",
            $form_id
        ));

        if (!$form) {
            wc_add_notice('Formulaire invalide', 'error');
            return;
        }

        // Valider les champs requis
        $fields = json_decode($form->fields, true);
        foreach ($fields as $field_id => $field) {
            if ($field['required'] && empty($_POST[$field_id])) {
                wc_add_notice(sprintf('Le champ %s est requis', $field['label']), 'error');
                return;
            }
        }

        // Créer la commande WooCommerce
        $order = wc_create_order();

        // Ajouter le produit
        $product = wc_get_product($form->product_id);
        if (!$product) {
            wc_add_notice('Produit invalide', 'error');
            return;
        }

        $order->add_product($product, 1);

        // Définir les informations client
        $billing_address = array();
        foreach ($fields as $field_id => $field) {
            if (isset($_POST[$field_id])) {
                $billing_address[$field_id] = sanitize_text_field($_POST[$field_id]);
            }
        }

        $order->set_address($billing_address, 'billing');
        
        // Calculer les totaux et sauvegarder
        $order->calculate_totals();
        $order->save();

        // Rediriger vers le paiement
        wp_redirect($order->get_checkout_payment_url());
        exit;
    }
} 