<div class="qodef-m-action">
    <?php

    $button_params = array(
        'link'          => '#',
        'button_layout' => 'filled',
        'text'          => esc_html__( 'Add all to cart', 'greenpath-core' ),
        'custom_class'  => 'qodef-add-all-to-cart-button',
    );

    echo GreenPathCore_Button_Shortcode::call_shortcode( $button_params );
    ?>
</div>
