<?php
/**
 * Content wrappers
 *
 * This template can be overridden by copying it to yourtheme/bookingfor/global/wrapper-start.php.
 *
 * HOWEVER, on occasion BookingFor will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		BookingFor
 * @package 	BookingFor/Templates
 * @version     2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$template = get_option( 'template' );

switch( $template ) {
	case 'BookYourTravel':
	global $post, $bookyourtravel_theme_globals;

	$page_id = $post->ID;

	$page_custom_fields = get_post_custom( $page_id);
	$page_sidebar_positioning = null;
	if (isset($page_custom_fields['page_sidebar_positioning'])) {
		$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
		$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
	}
	$section_class = 'full-width';
	if ($page_sidebar_positioning == 'both')
		$section_class = 'one-half';
	else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right') 
		$section_class = 'three-fourth';
?>
<div class="row">
	<?php
	if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
		get_sidebar('left');
		
	$allowed_tags = array();
	$allowed_tags['span'] = array('class' => array());
	?>
	<!--full-width content-->
	<section class="<?php echo esc_attr($section_class); ?>">
<?php    
	    break;
	case 'twentyeleven' :
		echo '<div id="primary"><div id="content" role="main" class="twentyeleven">';
		break;
	case 'twentytwelve' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="twentytwelve">';
		break;
	case 'twentythirteen' :
		echo '<div id="primary" class="site-content"><div id="content" role="main" class="entry-content twentythirteen">';
		break;
	case 'twentyfourteen' :
		echo '<div id="primary" class="content-area"><div id="content" role="main" class="site-content twentyfourteen"><div class="tfwc">';
		break;
	case 'twentyfifteen' :
		echo '<div id="primary" role="main" class="content-area twentyfifteen"><div id="main" class="site-main t15wc">';
		break;
	case 'twentysixteen' :
		echo '<div id="primary" class="content-area twentysixteen"><main id="main" class="site-main" role="main">';
		break;
	case 'twentyseventeen' :
	// Add class if sidebar is used.
	$correctiveClass= "";
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$correctiveClass = 'has-sidebar';
	}

	?>
		<div class="wrap <?php echo $correctiveClass ?>">
			<div id="primary" class="content-area">
				<main id="main" class="site-main" role="main">
<?php
		break;
	case 'royal' :
		$l = et_page_config();
	?>
		<div class="container content-page">
		<div class="page-content sidebar-position-<?php esc_attr_e( $l['sidebar'] ); ?> sidebar-mobile-<?php esc_attr_e( $l['sidebar-mobile'] ); ?>">
			<div class="row">

				<div class="content <?php esc_attr_e( $l['content-class'] ); ?>">
<?php
		break;
	case 'saladmag' :
	?>
		<section id="content_main" class="bfi-clearfix">
		<div class="row main_content">

		   <!-- Start content -->
			<div class="eight columns" id="content">
		 <div <?php post_class('widget_container content_page'); ?>> 
<?php
		break;
	case 'realhomes' :
?>
    <!-- Page Head -->
    <?php get_template_part("banners/default_page_banner"); ?>

    <!-- Content -->
    <div class="container contents single">
        <div class="row">
            <div class="span9 main-wrap">
                <!-- Main Content -->
                <div class="main">

                    <div class="inner-wrapper">
<?php 
		break;
	case 'x' :
	case 'x-child' :
?>
  <div class="x-container max width offset">
    <div class="<?php x_main_content_class(); ?>" role="main">
<?php 
		break;

	case 'genesis' :
    do_action( 'genesis_before_content_sidebar_wrap' );
    genesis_markup( array(
        'html5' => '<div %s>',
        'xhtml' => '<div id="content-sidebar-wrap">',
        'context' => 'content-sidebar-wrap',
    ) );
    
    do_action( 'genesis_before_content' );
    genesis_markup( array(
        'html5' => '<main %s>',
        'xhtml' => '<div id="content" class="hfeed">',
        'context' => 'content',
    ) );
    do_action( 'genesis_before_loop' );
?>
<?php
		break;
	default :
		echo '<div id="container"><div id="content" role="main">';
		break;
}
