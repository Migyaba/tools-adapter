<?php
/**
 * i18n bootstrap for Tools Adapter (front only).
 *
 * Editor / wp-admin stay in French (source language).
 * Front + AJAX follow the site locale (pl_PL, en_US, …).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether to load front translations.
 *
 * @return bool
 */
function tools_adapter_should_load_translations() {
	// admin-ajax.php is used by the front filters — always allow.
	if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
		return true;
	}

	// Elementor / wp-admin panel: keep French labels.
	if ( function_exists( 'is_admin' ) && is_admin() ) {
		return false;
	}

	return true;
}

/**
 * Resolve translation map file for current locale.
 *
 * Uses *.translations.php (not *.l10n.php) so WordPress core does not
 * auto-parse these files as native PHP translation files.
 *
 * @param string $locale Locale.
 * @return string|null Absolute path or null.
 */
function tools_adapter_get_l10n_file( $locale ) {
	$dir      = TOOLS_ADAPTER_PATH . 'languages/';
	$locale   = (string) $locale;
	$locale_l = strtolower( str_replace( '-', '_', $locale ) );

	if ( 0 === strpos( $locale_l, 'pl' ) ) {
		$file = $dir . 'tools-adapter-pl_PL.translations.php';
		return is_readable( $file ) ? $file : null;
	}

	if ( 0 === strpos( $locale_l, 'en' ) ) {
		$file = $dir . 'tools-adapter-en_US.translations.php';
		return is_readable( $file ) ? $file : null;
	}

	return null;
}

/**
 * Load PHP translation map (front only).
 */
function tools_adapter_load_textdomain() {
	if ( ! empty( $GLOBALS['tools_adapter_php_translations_loaded'] ) ) {
		return;
	}

	if ( ! tools_adapter_should_load_translations() ) {
		return;
	}

	$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
	$file   = tools_adapter_get_l10n_file( $locale );
	if ( ! $file ) {
		return;
	}

	$data = include $file;
	if ( ! is_array( $data ) || empty( $data['messages'] ) || ! is_array( $data['messages'] ) ) {
		return;
	}

	$GLOBALS['tools_adapter_php_translations']        = $data['messages'];
	$GLOBALS['tools_adapter_php_translations_locale'] = $locale;
	$GLOBALS['tools_adapter_php_translations_loaded'] = true;

	add_filter( 'gettext', 'tools_adapter_gettext_php_fallback', 10, 3 );
	add_filter( 'gettext_with_context', 'tools_adapter_gettext_with_context_php_fallback', 10, 4 );
	add_filter( 'ngettext', 'tools_adapter_ngettext_php_fallback', 10, 5 );
}
add_action( 'init', 'tools_adapter_load_textdomain', 0 );
add_action( 'wp_ajax_tools_adapter_filter_archive', 'tools_adapter_load_textdomain', 0 );
add_action( 'wp_ajax_nopriv_tools_adapter_filter_archive', 'tools_adapter_load_textdomain', 0 );
add_action( 'wp_ajax_tools_adapter_filter_products', 'tools_adapter_load_textdomain', 0 );
add_action( 'wp_ajax_nopriv_tools_adapter_filter_products', 'tools_adapter_load_textdomain', 0 );

/**
 * Translate a front string (also works for Elementor-saved French defaults).
 *
 * @param string $text Text.
 * @return string
 */
function tools_adapter_translate( $text ) {
	if ( ! is_string( $text ) || '' === $text ) {
		return is_string( $text ) ? $text : '';
	}

	// phpcs:ignore WordPress.WP.I18n.NonSingularString
	return __( $text, 'tools-adapter' );
}

/**
 * Plural index for the active locale.
 *
 * @param int $number Number.
 * @return int
 */
function tools_adapter_plural_index( $number ) {
	$n      = abs( (int) $number );
	$locale = strtolower( (string) ( $GLOBALS['tools_adapter_php_translations_locale'] ?? ( function_exists( 'determine_locale' ) ? determine_locale() : '' ) ) );

	// Polish: 3 forms.
	if ( 0 === strpos( $locale, 'pl' ) ) {
		if ( 1 === $n ) {
			return 0;
		}
		if ( $n % 10 >= 2 && $n % 10 <= 4 && ( $n % 100 < 12 || $n % 100 > 14 ) ) {
			return 1;
		}
		return 2;
	}

	// English & most languages: 2 forms.
	return ( 1 === $n ) ? 0 : 1;
}

/**
 * @param string $translation Translation.
 * @param string $text        Original.
 * @param string $domain      Domain.
 * @return string
 */
function tools_adapter_gettext_php_fallback( $translation, $text, $domain ) {
	if ( 'tools-adapter' !== $domain || empty( $GLOBALS['tools_adapter_php_translations'] ) ) {
		return $translation;
	}
	$map = $GLOBALS['tools_adapter_php_translations'];
	if ( isset( $map[ $text ] ) && is_string( $map[ $text ] ) ) {
		return $map[ $text ];
	}
	return $translation;
}

/**
 * @param string $translation Translation.
 * @param string $text        Original.
 * @param string $context     Context.
 * @param string $domain      Domain.
 * @return string
 */
function tools_adapter_gettext_with_context_php_fallback( $translation, $text, $context, $domain ) {
	if ( 'tools-adapter' !== $domain || empty( $GLOBALS['tools_adapter_php_translations'] ) ) {
		return $translation;
	}
	$key = $context . "\x04" . $text;
	$map = $GLOBALS['tools_adapter_php_translations'];
	if ( isset( $map[ $key ] ) && is_string( $map[ $key ] ) ) {
		return $map[ $key ];
	}
	return $translation;
}

/**
 * @param string $translation Translation.
 * @param string $single      Singular.
 * @param string $plural      Plural.
 * @param int    $number      Number.
 * @param string $domain      Domain.
 * @return string
 */
function tools_adapter_ngettext_php_fallback( $translation, $single, $plural, $number, $domain ) {
	if ( 'tools-adapter' !== $domain || empty( $GLOBALS['tools_adapter_php_translations'] ) ) {
		return $translation;
	}
	$map = $GLOBALS['tools_adapter_php_translations'];
	if ( ! isset( $map[ $single ] ) || ! is_array( $map[ $single ] ) ) {
		return $translation;
	}
	$forms = $map[ $single ];
	$index = tools_adapter_plural_index( $number );
	if ( ! isset( $forms[ $index ] ) && isset( $forms[1] ) ) {
		$index = 1;
	}
	return isset( $forms[ $index ] ) && is_string( $forms[ $index ] ) ? $forms[ $index ] : $translation;
}
