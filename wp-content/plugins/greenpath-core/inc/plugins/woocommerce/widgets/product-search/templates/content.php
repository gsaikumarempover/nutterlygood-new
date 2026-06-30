<?php
$categories = get_terms(array(
	'taxonomy' => 'product_cat',
));

?>
<div class="qodef-product-search-holder">
	<form class="qodef-product-search-form" action="<?php echo esc_url(home_url('/')); ?>" method="GET">
		
		<div class="qodef-product-category-holder">
			<select class="qodef-product-category" name="product_cat">
				<option value=""><?php esc_html_e('All Categories', 'greenpath-core'); ?></option>
				
				<?php if (is_array($categories) && count($categories)) :
					foreach ($categories as $category) : ?>
						
						<option
								value="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?>
						</option>
					
					<?php endforeach;
				endif; ?>
			
			</select>
		</div>
		
		<input type="text" value="" class="qodef-product-search" name="s"
		       placeholder="<?php esc_attr_e('Search for Product...', 'greenpath-core'); ?>">
		
		<button type="submit" class="qodef-product-search-submit">
			<?php echo greenpath_core_get_svg_icon( 'search' ) ?>
		</button>
		
		<input type="hidden" class="qodef-product-post-type" name="post_type" value="product">
	</form>
</div>