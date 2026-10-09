<?php
namespace ToolsAdapter;

use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Active les « balises dynamiques » Elementor sur les champs de contenu des
 * widgets : liens (URL), images (MEDIA), textes (TEXT / TEXTAREA / WYSIWYG)
 * et galeries.
 *
 * Elementor n'affiche le bouton « Balises dynamiques » que sur les contrôles
 * déclarés avec `'dynamic' => [ 'active' => true ]`. Plutôt que de modifier
 * chaque contrôle à la main, les classes Base_Widget et Repeater ci-dessous
 * ajoutent ce réglage automatiquement ; les widgets n'ont qu'à en hériter.
 *
 * Les valeurs dynamiques sont ensuite résolues par Elementor dans
 * get_settings_for_display(), que tous les widgets utilisent au rendu.
 */
final class Dynamic_Support {

	/**
	 * Identifiants de contrôles qui ne doivent PAS être dynamiques : valeurs
	 * techniques (ancres, mots-clés, libellés comparés exactement…).
	 */
	const EXCLUDED_IDS = [
		'form_anchor_id',
		'prefill_service',
		'default_essence',
		'default_length',
		'currency',
		'detail_text',
		'prefill_message',
		// Sécurité : le destinataire du formulaire de contact ne doit jamais
		// dépendre d'une valeur contrôlable par le visiteur (ex. paramètre d'URL).
		'recipient_email',
	];

	/**
	 * Types de contrôles concernés.
	 *
	 * @return string[]
	 */
	private static function dynamic_types() {
		return [
			Controls_Manager::URL,
			Controls_Manager::MEDIA,
			Controls_Manager::TEXT,
			Controls_Manager::TEXTAREA,
			Controls_Manager::WYSIWYG,
			Controls_Manager::GALLERY,
		];
	}

	/**
	 * Ajoute `dynamic.active` aux arguments d'un contrôle éligible.
	 *
	 * @param string $id   Identifiant du contrôle.
	 * @param array  $args Arguments du contrôle.
	 * @return array
	 */
	public static function prepare( $id, array $args ) {
		if ( isset( $args['dynamic'] ) || empty( $args['type'] ) ) {
			return $args; // Choix explicite du widget : on ne touche à rien.
		}

		if ( ! in_array( $args['type'], self::dynamic_types(), true ) ) {
			return $args;
		}

		// Une valeur injectée dans du CSS ou dans une classe n'est pas résolue
		// par le moteur de balises : on l'exclut.
		if ( ! empty( $args['selectors'] ) || ! empty( $args['prefix_class'] ) ) {
			return $args;
		}

		if ( in_array( $id, self::EXCLUDED_IDS, true ) || preg_match( '/(^|_)(id|anchor|key|class|slug)$/', (string) $id ) ) {
			return $args;
		}

		if ( ! Admin_Settings::is_feature_enabled( 'dynamic_tags' ) ) {
			return $args;
		}

		$args['dynamic'] = [ 'active' => true ];

		return $args;
	}
}

/**
 * Widget de base de Tools Adapter : active les balises dynamiques.
 */
abstract class Base_Widget extends \Elementor\Widget_Base {

	/**
	 * {@inheritdoc}
	 */
	public function add_control( $id, array $args, $options = [] ) {
		return parent::add_control( $id, Dynamic_Support::prepare( $id, $args ), $options );
	}
}

/**
 * Répéteur de Tools Adapter : active les balises dynamiques sur ses champs.
 */
class Repeater extends \Elementor\Repeater {

	/**
	 * {@inheritdoc}
	 */
	public function add_control( $id, array $args, $options = [] ) {
		return parent::add_control( $id, Dynamic_Support::prepare( $id, $args ), $options );
	}
}
