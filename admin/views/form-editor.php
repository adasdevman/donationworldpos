<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1>Créer un nouveau formulaire de don</h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="cpf_save_form">
        <?php wp_nonce_field('cpf_save_form'); ?>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="form_name">Nom du formulaire</label></th>
                <td><input name="form_name" type="text" id="form_name" class="regular-text" required></td>
            </tr>

            <tr>
                <th scope="row">Champs du formulaire</th>
                <td>
                    <?php foreach ($available_fields as $field_id => $field_label): ?>
                        <label>
                            <input type="checkbox" name="fields[]" value="<?php echo esc_attr($field_id); ?>">
                            <?php echo esc_html($field_label); ?>
                        </label><br>
                    <?php endforeach; ?>
                </td>
            </tr>

            <tr>
                <th scope="row"><label for="product_id">Produit associé</label></th>
                <td>
                    <select name="product_id" id="product_id" required>
                        <option value="">Sélectionnez un produit</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo esc_attr($product->get_id()); ?>">
                                <?php echo esc_html($product->get_name()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th scope="row">Méthodes de paiement</th>
                <td>
                    <?php foreach ($payment_gateways as $gateway): ?>
                        <label>
                            <input type="checkbox" name="payment_methods[]" value="<?php echo esc_attr($gateway->id); ?>">
                            <?php echo esc_html($gateway->get_title()); ?>
                        </label><br>
                    <?php endforeach; ?>
                </td>
            </tr>
        </table>

        <?php submit_button('Créer le formulaire'); ?>
    </form>
</div> 