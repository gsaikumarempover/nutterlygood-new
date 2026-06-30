<div class="qodef-labels-holder">
    <?php if (get_post_meta($product_id, 'qodef_show_new_sign', true) === 'yes') { ?>
        <span
            class="qodef-pl-new-product"><?php esc_html_e('New', 'masterds-core'); ?></span>
    <?php } ?>
</div>