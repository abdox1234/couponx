<?php
/**
 * The template for displaying the header.
 *
 * Displays everything from the doctype declaration down to the navigation.
 */
?>
<!DOCTYPE html>
<?php $mts_options = get_option(MTS_THEME_NAME); ?>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame -->
	<!--[if IE ]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<?php mts_meta(); ?>
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<?php wp_head(); ?>
</head>
<body id="blog" <?php body_class('main'); ?>>
	<div class="main-container">
		<header id="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">
			<div id="header">
				<div class="container clearfix">
					<div class="logo-wrap">
						<?php if ( isset( $mts_options['mts_logo'] ) && $mts_options['mts_logo'] != '') {

							$logo_id = mts_get_image_id_from_url( $mts_options['mts_logo'] );
							$logo_w_h = '';
							if ( $logo_id ) {
								$logo	 = wp_get_attachment_image_src( $logo_id, 'full' );
								$logo_w_h = ' width="'.$logo[1].'" height="'.$logo[2].'"';
							} ?>

							<?php if( is_front_page() || is_home() || is_404() ) { ?>
								<h1 id="logo" class="image-logo" itemprop="headline">
									<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $mts_options['mts_logo'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"<?php echo $logo_w_h; ?>></a>
								</h1><!-- END #logo -->
							<?php } else { ?>
								<h2 id="logo" class="image-logo" itemprop="headline">
									<a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_url( $mts_options['mts_logo'] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"<?php echo $logo_w_h; ?>></a>
								</h2><!-- END #logo -->
							<?php } ?>

						<?php } else { ?>

							<?php if( is_front_page() || is_home() || is_404() ) { ?>
								<h1 id="logo" class="text-logo" itemprop="headline">
									<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
								</h1><!-- END #logo -->
							<?php } else { ?>
								<h2 id="logo" class="text-logo" itemprop="headline">
									<a href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
								</h2><!-- END #logo -->
							<?php } ?>

						<?php }
						if ( get_bloginfo( 'description' ) ) { ?>
							<div class="site-description" itemprop="description">
								<?php bloginfo( 'description' ); ?>
							</div>
						<?php } ?>
					</div>
					<?php if( $mts_options['mts_header_login'] == '1' ) { ?>
					<div class="header-login">
						<ul>
							<li><a href="<?php echo wp_login_url(); ?>" title="Login">Log in</a></li>
							<li><a href="<?php echo wp_registration_url(); ?>">Sign up</a></li>
						</ul>
					</div>
					<?php } ?>
					<?php if( $mts_options['mts_header_search'] == '1' ) { ?>
						<div class="header-search">
							<form method="get" id="searchform" class="search-form" action="<?php echo esc_attr( home_url() ); ?>" _lpchecked="1">
								<fieldset>
									<input type="text" name="s" id="s" value="<?php the_search_query(); ?>" placeholder="<?php echo esc_attr( $mts_options['mts_header_search_placeholder'] ); ?>" <?php if (!empty($mts_options['mts_ajax_search'])) echo ' autocomplete="off"'; ?> />
									<input type="hidden" class="mts_post_type" name="post_type" value="coupons" />
									<button id="search-image" class="sbutton" type="submit" value="">
										<i class="fa fa-search"></i>
									</button>
								</fieldset>
							</form>
						</div>
					<?php } ?>
				</div><!--.container-->
			</div><!--#header-->

			<?php if ( $mts_options['mts_show_primary_nav'] == '1' ) { ?>
			<?php if( $mts_options['mts_sticky_nav'] == '1' ) { ?>
				<div id="catcher" class="clear" ></div>
				<div id="primary-navigation" class="sticky-navigation clearfix" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php } else { ?>
				<div id="primary-navigation" class="clearfix" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">
			<?php } ?>
				<div class="container clearfix">
					<a href="#" id="pull" class="toggle-mobile-menu"><?php _e('Menu', 'coupon' ); ?></a>
					<?php if ( has_nav_menu( 'mobile' ) ) { ?>
						<nav class="navigation clearfix">
							<?php if ( has_nav_menu( 'primary' ) ) { ?>
								<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
							<?php } else { ?>
								<ul class="menu clearfix">
									<?php wp_list_categories('title_li='); ?>
								</ul>
							<?php } ?>
						</nav>
						<nav class="navigation mobile-only clearfix mobile-menu-wrapper">
							<?php wp_nav_menu( array( 'theme_location' => 'mobile', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
						</nav>
					<?php } else { ?>
						<nav class="navigation clearfix mobile-menu-wrapper">
							<?php if ( has_nav_menu( 'primary' ) ) { ?>
								<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_class' => 'menu clearfix', 'container' => '', 'walker' => new mts_menu_walker ) ); ?>
							<?php } else { ?>
								<ul class="menu clearfix">
									<?php wp_list_categories('title_li='); ?>
								</ul>
							<?php } ?>
						</nav>
				</div><!--.container-->
				<?php } ?>
			</div>
			<?php } ?>
		</header>

		<?php dynamic_sidebar('widget-header'); ?>
