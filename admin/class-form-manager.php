<?php
class CPF_Form_Manager {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_forms_menu'));
        add_action('admin_post_cpf_save_form', array($this, 'save_form'));
    }

    public function add_forms_menu() {
        add_menu_page(
            'Formulaires de paiement',
            'Formulaires de paiement',
            'manage_options',
            'cpf-forms',
            array($this, 'render_forms_page'),
            'dashicons-feedback',
            30
        );
    }

    public function render_forms_page() {
        if (isset($_GET['action']) && $_GET['action'] === 'new') {
            $this->render_form_editor();
        } else {
            $this->render_forms_list();
        }
    }

    private function render_form_editor() {
        $available_fields = array(
            'montant' => 'Montant',
            'first_name' => 'Prénom',
            'last_name' => 'Nom',
            'email' => 'Email',
            'phone' => 'Téléphone',
            'address' => 'Adresse',
            'city' => 'Ville',
            'postcode' => 'Code postal'
        );

        // Récupérer tous les produits WooCommerce
        $products = wc_get_products(array('status' => 'publish'));
        
        // Récupérer toutes les méthodes de paiement actives
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
        
        include CPF_PLUGIN_DIR . 'admin/views/form-editor.php';
    }
} 