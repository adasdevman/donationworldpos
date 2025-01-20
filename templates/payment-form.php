<?php
if (!defined('ABSPATH')) exit;
?>

<form method="post" class="cpf-payment-form" action="">
    <input type="hidden" name="form_id" value="<?php echo esc_attr($atts['id']); ?>">
    
    <?php foreach ($fields as $field_id => $field): ?>
        <div class="form-row">
            <label for="<?php echo esc_attr($field_id); ?>">
                <?php echo esc_html($field['label']); ?>:
            </label>
            <input 
                type="<?php echo esc_attr($field['type']); ?>" 
                id="<?php echo esc_attr($field_id); ?>"
                name="<?php echo esc_attr($field_id); ?>"
                <?php echo ($field['required']) ? 'required' : ''; ?>
            >
        </div>
    <?php endforeach; ?>

    <button type="submit" name="submit_payment" class="button">
        <?php esc_html_e('Procéder au paiement', 'cpf'); ?>
    </button>
</form> 