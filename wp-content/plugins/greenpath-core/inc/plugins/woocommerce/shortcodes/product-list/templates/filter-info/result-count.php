<div class="qodef-product-list-result-count">
    <?php

    $args = array(
        'total'     => $query_result->found_posts,
        'per_page'  => $query_result->query['posts_per_page'],
        'current'   => max( 1, (int) ( $query_result->query['paged'] ?? 1 ) ),
        'orderedby' => '',
    );

    wc_get_template( 'loop/result-count.php', $args );
    ?>
</div>