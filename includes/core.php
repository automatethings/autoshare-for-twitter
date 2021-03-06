<?php
/**
 * Core plugin setup.
 *
 * @package TenUp\AutoshareForTwitter\Core
 */

namespace TenUp\AutoshareForTwitter\Core;

use TenUp\AutoshareForTwitter\Utils;

const POST_TYPE_SUPPORT_FEATURE = 'autoshare-for-twitter';

/**
 * The main setup action.
 */
function setup() {
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/assets.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/settings.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-meta.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/admin/post-transition.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/class-publish-tweet.php';
	require_once plugin_dir_path( AUTOSHARE_FOR_TWITTER ) . 'includes/rest.php';

	\TenUp\AutoshareForTwitter\Admin\Assets\add_hook_callbacks();
	\TenUp\AutoshareForTwitter\REST\add_hook_callbacks();

	/**
	 * Allow others to hook into the core setup action
	 */
	do_action( 'autoshare_for_twitter_setup' );

	add_action( 'init', __NAMESPACE__ . '\set_post_type_supports' );
	add_filter( 'autoshare_for_twitter_enabled_default', __NAMESPACE__ . '\maybe_enable_autoshare_by_default' );
	add_filter( 'autoshare_for_twitter_attached_image', __NAMESPACE__ . '\maybe_disable_upload_image' );
}

/**
 * Fire up the module.
 *
 * @uses autoshare_for_twitter_loaded
 */
add_action( 'autoshare_for_twitter_loaded', __NAMESPACE__ . '\setup' );

/**
 * Adds autoshare support for default post types.
 *
 * @since 1.0.0
 */
function set_post_type_supports() {
	$post_types = Utils\get_enabled_post_types();
	foreach ( (array) $post_types as $post_type ) {
		add_post_type_support( $post_type, POST_TYPE_SUPPORT_FEATURE );
	}
}

/**
 * Enable autoshare by default.
 *
 * @since 1.0.0
 */
function maybe_enable_autoshare_by_default() {
	return (bool) Utils\get_autoshare_for_twitter_settings( 'enable_default' );
}

/**
 * Maybe disable uploading image to Twitter. We upload attached image to Twitter
 * by default, so we disable it if needed here.
 *
 * @since 1.0.0
 *
 * @param null|int $attachment_id ID of attachment being uploaded.
 *
 * @return null|int|bool
 */
function maybe_disable_upload_image( $attachment_id ) {
	if ( ! Utils\get_autoshare_for_twitter_settings( 'enable_upload' ) ) {
		return false;
	}

	return $attachment_id;
}
