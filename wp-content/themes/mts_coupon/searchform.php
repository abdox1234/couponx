<?php
/**
 * The template for displaying the search form.
 */
$mts_options = get_option(MTS_THEME_NAME); ?>

<form method="get" id="searchform" class="search-form" action="<?php echo esc_attr( home_url() ); ?>" _lpchecked="1">
	<fieldset>
		<input type="search" name="s" id="s" value="<?php the_search_query(); ?>" placeholder="<?php _e('Search here...', 'coupon' ); ?>" <?php if (!empty($mts_options['mts_ajax_search'])) echo ' autocomplete="off"'; ?> />
		<button id="search-image" class="sbutton" type="submit" value=""><i class="fa fa-search"></i></button>
	</fieldset>
</form>