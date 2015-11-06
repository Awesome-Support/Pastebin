<?php
/**
 * @package   Awesome Support Pastebin
 * @author    ThemeAvenue <web@themeavenue.net>
 * @license   GPL-2.0+
 * @link      http://themeavenue.net
 * @copyright 2015 ThemeAvenue
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_filter( 'wpas_plugin_settings', 'as_pastebin_settings', 5, 1 );
/**
 * Add plugin core settings.
 *
 * @param  array $def Array of existing settings
 *
 * @return array      Updated settings
 */
function as_pastebin_settings( $def ) {

	$settings = array(
		'pastebin' => array(
			'name'    => __( 'Pastebin', 'as-pastebin' ),
			'options' => array(
				array(
					'name'    => __( 'Developer API Key', 'as-pastebin' ),
					'id'      => 'pastebin_dev_key',
					'type'    => 'text',
					'default' => '',
					'desc'    => sprintf( __( 'Your developer API key. <a %s>Get it here</a>.', 'as-pastebin' ), 'href="http://pastebin.com/api#1" target="_blank"' )
				),
				array(
					'name'    => __( 'Default Paste Format', 'as-pastebin' ),
					'id'      => 'pastebin_paste_format',
					'type'    => 'select',
					'default' => '',
					'options' => as_pastebin_get_code_formats()
				),
				array(
					'name'    => __( 'Default Paste Visibility', 'as-pastebin' ),
					'id'      => 'pastebin_paste_private',
					'type'    => 'select',
					'default' => '',
					'options' => array(
						'0' => esc_html__( 'Public', 'as-pastebin' ),
						'1' => esc_html__( 'Unlisted', 'as-pastebin' )
					)
				),
				array(
					'name'    => __( 'Paste Lifetime', 'as-pastebin' ),
					'id'      => 'pastebin_paste_expire',
					'type'    => 'select',
					'default' => '',
					'options' => as_pastebin_get_available_lifespan(),
					'desc'    => esc_html__( 'After what delay will the paste expire?', 'as-pastebin' )
				),
			),
		),
	);

	return array_merge( $def, $settings );

}