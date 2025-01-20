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

        // Créer la commande en utilisant l'API WooCommerce
        $order_data = array(
            'status' => 'pending',
            'customer_id' => get_current_user_id(),
            'created_via' => 'donationwc',
            'customer_note' => '',
            'billing' => array(),
        );

        // Préparer les données de facturation
        foreach ($fields as $field_id => $field) {
            if (isset($_POST[$field_id])) {
                $order_data['billing'][$field_id] = sanitize_text_field($_POST[$field_id]);
            }
        }

        // Créer la commande via l'API WooCommerce
        $order = wc_create_order($order_data);

        if (is_wp_error($order)) {
            wc_add_notice($order->get_error_message(), 'error');
            return;
        }

        // Ajouter le produit
        $product = wc_get_product($form->product_id);
        if (!$product) {
            $order->delete(true);
            wc_add_notice('Produit invalide', 'error');
            return;
        }

        try {
            $order->add_product($product, 1);
            $order->calculate_totals();
            $order->save();

            // Rediriger vers le paiement en utilisant l'URL sécurisée
            $checkout_url = $order->get_checkout_payment_url();
            wp_redirect($checkout_url);
            exit;

        } catch (Exception $e) {
            $order->delete(true);
            wc_add_notice($e->getMessage(), 'error');
            return;
        }
    }
} 