<?php
/**
 * Balises dynamiques Tools Adapter.
 *
 * Elementor gratuit embarque le moteur de balises dynamiques mais aucune
 * balise (elles sont fournies par Elementor Pro). Ce fichier en enregistre un
 * jeu autonome : elles apparaissent dans le bouton « Balises dynamiques »
 * (icône base de données) des champs lien, texte et image des widgets, avec
 * ou sans Elementor Pro.
 *
 * Les balises renvoient du TEXTE BRUT : l'échappement est fait par chaque
 * widget au moment d'afficher la valeur (esc_html / esc_attr / esc_url).
 */
namespace ToolsAdapter\DynamicTags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as Tags_Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const GROUP = 'tools-adapter';

/**
 * Utilitaires partagés par les balises.
 */
final class Helper {

	/**
	 * Identifiant du contenu courant (page, article, produit de la boucle…).
	 *
	 * @return int
	 */
	public static function post_id() {
		$id = get_the_ID();
		if ( ! $id ) {
			$id = get_queried_object_id();
		}
		return (int) $id;
	}

	/**
	 * Texte brut : sans balises HTML, entités décodées (&#8217; → ’).
	 *
	 * @param mixed $value Valeur.
	 * @return string
	 */
	public static function plain( $value ) {
		if ( ! is_scalar( $value ) ) {
			return '';
		}
		return trim( html_entity_decode( wp_strip_all_tags( (string) $value ), ENT_QUOTES, 'UTF-8' ) );
	}

	/**
	 * Liste « identifiant => titre » des pages et articles publiés.
	 *
	 * @return array<int|string,string>
	 */
	public static function post_options() {
		$options = [ '' => esc_html__( '— Choisir une page —', 'tools-adapter' ) ];
		$posts   = get_posts(
			[
				'post_type'        => [ 'page', 'post' ],
				'post_status'      => 'publish',
				'numberposts'      => 300,
				'orderby'          => 'title',
				'order'            => 'ASC',
				'suppress_filters' => false,
			]
		);
		foreach ( $posts as $post ) {
			$options[ $post->ID ] = '' !== $post->post_title ? $post->post_title : '#' . $post->ID;
		}
		return $options;
	}

	/**
	 * Lien « media » (ID + URL) d'une pièce jointe, au format attendu par les
	 * contrôles d'image.
	 *
	 * @param int $attachment_id Identifiant de la pièce jointe.
	 * @return array{id:int,url:string}|array{}
	 */
	public static function image_data( $attachment_id ) {
		$attachment_id = (int) $attachment_id;
		$url           = $attachment_id ? wp_get_attachment_image_url( $attachment_id, 'full' ) : '';
		return $url ? [ 'id' => $attachment_id, 'url' => $url ] : [];
	}
}

/**
 * Groupe et catégories communs à toutes les balises.
 */
trait In_Group {

	public function get_group() {
		return GROUP;
	}
}

/* ======================================================================
 * Balises TEXTE
 * ==================================================================== */

abstract class Text_Tag extends Tag {

	use In_Group;

	public function get_categories() {
		return [ Tags_Module::TEXT_CATEGORY ];
	}
}

class Post_Title extends Text_Tag {
	public function get_name() {
		return 'ta-post-title';
	}
	public function get_title() {
		return esc_html__( 'Titre de la page / de l\'article', 'tools-adapter' );
	}
	public function render() {
		echo esc_html( Helper::plain( get_the_title( Helper::post_id() ) ) );
	}
}

class Post_Excerpt extends Text_Tag {
	public function get_name() {
		return 'ta-post-excerpt';
	}
	public function get_title() {
		return esc_html__( 'Extrait de la page / de l\'article', 'tools-adapter' );
	}
	public function render() {
		echo esc_html( Helper::plain( get_the_excerpt( Helper::post_id() ) ) );
	}
}

class Post_Date extends Text_Tag {
	public function get_name() {
		return 'ta-post-date';
	}
	public function get_title() {
		return esc_html__( 'Date de publication', 'tools-adapter' );
	}
	protected function register_controls() {
		$this->add_control(
			'format',
			[
				'label'   => esc_html__( 'Format', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'j F Y',
				'options' => [
					'j F Y'  => esc_html__( '7 octobre 2026', 'tools-adapter' ),
					'd/m/Y'  => esc_html__( '07/10/2026', 'tools-adapter' ),
					'F Y'    => esc_html__( 'octobre 2026', 'tools-adapter' ),
					'Y'      => esc_html__( '2026', 'tools-adapter' ),
					'custom' => esc_html__( 'Personnalisé', 'tools-adapter' ),
				],
			]
		);
		$this->add_control(
			'custom_format',
			[
				'label'     => esc_html__( 'Format PHP', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'j M Y',
				'condition' => [ 'format' => 'custom' ],
			]
		);
	}
	public function render() {
		$format = $this->get_settings( 'format' );
		if ( 'custom' === $format ) {
			$format = (string) $this->get_settings( 'custom_format' );
		}
		echo esc_html( Helper::plain( get_the_date( $format ? $format : 'j F Y', Helper::post_id() ) ) );
	}
}

class Author_Name extends Text_Tag {
	public function get_name() {
		return 'ta-author-name';
	}
	public function get_title() {
		return esc_html__( 'Auteur', 'tools-adapter' );
	}
	public function render() {
		$author = (int) get_post_field( 'post_author', Helper::post_id() );
		echo esc_html( Helper::plain( $author ? get_the_author_meta( 'display_name', $author ) : '' ) );
	}
}

class Term_Name extends Text_Tag {
	public function get_name() {
		return 'ta-term-name';
	}
	public function get_title() {
		return esc_html__( 'Catégorie / terme', 'tools-adapter' );
	}
	protected function register_controls() {
		$taxonomies = [];
		foreach ( get_taxonomies( [ 'public' => true ], 'objects' ) as $slug => $taxonomy ) {
			$taxonomies[ $slug ] = $taxonomy->labels->singular_name;
		}
		$this->add_control(
			'taxonomy',
			[
				'label'   => esc_html__( 'Taxonomie', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'category',
				'options' => $taxonomies,
			]
		);
	}
	public function render() {
		// Page d'archive : le terme affiché ; sinon : premier terme du contenu.
		$queried = get_queried_object();
		if ( $queried instanceof \WP_Term && ( is_category() || is_tag() || is_tax() ) ) {
			echo esc_html( Helper::plain( $queried->name ) );
			return;
		}
		$terms = get_the_terms( Helper::post_id(), (string) $this->get_settings( 'taxonomy' ) );
		if ( is_array( $terms ) && $terms ) {
			echo esc_html( Helper::plain( reset( $terms )->name ) );
		}
	}
}

class Site_Name extends Text_Tag {
	public function get_name() {
		return 'ta-site-name';
	}
	public function get_title() {
		return esc_html__( 'Nom du site', 'tools-adapter' );
	}
	public function render() {
		echo esc_html( Helper::plain( get_bloginfo( 'name' ) ) );
	}
}

class Site_Tagline extends Text_Tag {
	public function get_name() {
		return 'ta-site-tagline';
	}
	public function get_title() {
		return esc_html__( 'Slogan du site', 'tools-adapter' );
	}
	public function render() {
		echo esc_html( Helper::plain( get_bloginfo( 'description' ) ) );
	}
}

class Current_Year extends Text_Tag {
	public function get_name() {
		return 'ta-current-year';
	}
	public function get_title() {
		return esc_html__( 'Année en cours', 'tools-adapter' );
	}
	public function render() {
		echo esc_html( wp_date( 'Y' ) );
	}
}

class Custom_Field extends Text_Tag {
	public function get_name() {
		return 'ta-custom-field';
	}
	public function get_title() {
		return esc_html__( 'Champ personnalisé', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'key';
	}
	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label'       => esc_html__( 'Clé du champ', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Les clés commençant par « _ » (champs privés) sont ignorées.', 'tools-adapter' ),
			]
		);
	}
	public function render() {
		$key = trim( (string) $this->get_settings( 'key' ) );
		if ( '' === $key || '_' === $key[0] ) {
			return;
		}
		echo esc_html( Helper::plain( get_post_meta( Helper::post_id(), $key, true ) ) );
	}
}

class Url_Parameter extends Text_Tag {
	public function get_name() {
		return 'ta-url-param';
	}
	public function get_title() {
		return esc_html__( 'Paramètre d\'URL', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'param';
	}
	protected function register_controls() {
		$this->add_control(
			'param',
			[
				'label'       => esc_html__( 'Nom du paramètre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'prestation',
				'description' => esc_html__( 'Ex. « prestation » pour une page ouverte avec ?prestation=bois. Avec un cache de pages, excluez cette page du cache.', 'tools-adapter' ),
			]
		);
	}
	public function render() {
		$param = sanitize_key( (string) $this->get_settings( 'param' ) );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- lecture seule, valeur affichée échappée par le widget.
		if ( '' !== $param && isset( $_GET[ $param ] ) && is_scalar( $_GET[ $param ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			echo esc_html( Helper::plain( sanitize_text_field( wp_unslash( $_GET[ $param ] ) ) ) );
		}
	}
}

class Product_Price extends Text_Tag {
	public function get_name() {
		return 'ta-product-price';
	}
	public function get_title() {
		return esc_html__( 'Produit WooCommerce : prix', 'tools-adapter' );
	}
	public function render() {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}
		$product = wc_get_product( Helper::post_id() );
		if ( $product ) {
			echo esc_html( Helper::plain( $product->get_price_html() ) );
		}
	}
}

/* ======================================================================
 * Balises LIEN (URL)
 * ==================================================================== */

abstract class Url_Tag extends Data_Tag {

	use In_Group;

	public function get_categories() {
		return [ Tags_Module::URL_CATEGORY ];
	}
}

class Post_Url extends Url_Tag {
	public function get_name() {
		return 'ta-post-url';
	}
	public function get_title() {
		return esc_html__( 'Lien de la page courante', 'tools-adapter' );
	}
	protected function get_value( array $options = [] ) {
		return (string) get_permalink( Helper::post_id() );
	}
}

class Page_Url extends Url_Tag {
	public function get_name() {
		return 'ta-page-url';
	}
	public function get_title() {
		return esc_html__( 'Lien d\'une page au choix', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'post_id';
	}
	protected function register_controls() {
		$this->add_control(
			'post_id',
			[
				'label'   => esc_html__( 'Page ou article', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'options' => Helper::post_options(),
				'default' => '',
			]
		);
	}
	protected function get_value( array $options = [] ) {
		$id = (int) $this->get_settings( 'post_id' );
		return $id ? (string) get_permalink( $id ) : '';
	}
}

class Home_Url extends Url_Tag {
	public function get_name() {
		return 'ta-home-url';
	}
	public function get_title() {
		return esc_html__( 'Page d\'accueil du site', 'tools-adapter' );
	}
	protected function get_value( array $options = [] ) {
		return home_url( '/' );
	}
}

class Phone_Url extends Url_Tag {
	public function get_name() {
		return 'ta-phone-url';
	}
	public function get_title() {
		return esc_html__( 'Téléphone (appel)', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'phone';
	}
	protected function register_controls() {
		$this->add_control(
			'phone',
			[
				'label'       => esc_html__( 'Numéro', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '03 88 63 70 71',
			]
		);
	}
	protected function get_value( array $options = [] ) {
		$digits = preg_replace( '/[^0-9+]/', '', (string) $this->get_settings( 'phone' ) );
		return $digits ? 'tel:' . $digits : '';
	}
}

class Email_Url extends Url_Tag {
	public function get_name() {
		return 'ta-email-url';
	}
	public function get_title() {
		return esc_html__( 'E-mail', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'email';
	}
	protected function register_controls() {
		$this->add_control( 'email', [ 'label' => esc_html__( 'Adresse e-mail', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => 'contact@exemple.fr' ] );
		$this->add_control( 'subject', [ 'label' => esc_html__( 'Objet (facultatif)', 'tools-adapter' ), 'type' => Controls_Manager::TEXT ] );
	}
	protected function get_value( array $options = [] ) {
		$email = sanitize_email( (string) $this->get_settings( 'email' ) );
		if ( '' === $email ) {
			return '';
		}
		$subject = trim( (string) $this->get_settings( 'subject' ) );
		return 'mailto:' . $email . ( '' !== $subject ? '?subject=' . rawurlencode( $subject ) : '' );
	}
}

class Whatsapp_Url extends Url_Tag {
	public function get_name() {
		return 'ta-whatsapp-url';
	}
	public function get_title() {
		return esc_html__( 'WhatsApp', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'phone';
	}
	protected function register_controls() {
		$this->add_control(
			'phone',
			[
				'label'       => esc_html__( 'Numéro (format international)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => '33388637071',
			]
		);
		$this->add_control( 'message', [ 'label' => esc_html__( 'Message pré-rempli', 'tools-adapter' ), 'type' => Controls_Manager::TEXTAREA, 'rows' => 3 ] );
	}
	protected function get_value( array $options = [] ) {
		$digits = preg_replace( '/\D+/', '', (string) $this->get_settings( 'phone' ) );
		if ( '' === $digits ) {
			return '';
		}
		$message = trim( (string) $this->get_settings( 'message' ) );
		return 'https://wa.me/' . $digits . ( '' !== $message ? '?text=' . rawurlencode( $message ) : '' );
	}
}

class Maps_Url extends Url_Tag {
	public function get_name() {
		return 'ta-maps-url';
	}
	public function get_title() {
		return esc_html__( 'Google Maps (lieu ou itinéraire)', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'address';
	}
	protected function register_controls() {
		$this->add_control( 'address', [ 'label' => esc_html__( 'Adresse', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'placeholder' => '28 rue des Hêtres, 67240 Schirrhein' ] );
		$this->add_control(
			'mode',
			[
				'label'   => esc_html__( 'Action', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'search',
				'options' => [
					'search'     => esc_html__( 'Afficher le lieu', 'tools-adapter' ),
					'directions' => esc_html__( 'Calculer l\'itinéraire', 'tools-adapter' ),
				],
			]
		);
	}
	protected function get_value( array $options = [] ) {
		$address = trim( (string) $this->get_settings( 'address' ) );
		if ( '' === $address ) {
			return '';
		}
		if ( 'directions' === $this->get_settings( 'mode' ) ) {
			return 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address );
		}
		return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $address );
	}
}

class Share_Url extends Url_Tag {
	public function get_name() {
		return 'ta-share-url';
	}
	public function get_title() {
		return esc_html__( 'Partager la page', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'network';
	}
	protected function register_controls() {
		$this->add_control(
			'network',
			[
				'label'   => esc_html__( 'Réseau', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => [
					'facebook' => 'Facebook',
					'x'        => 'X (Twitter)',
					'linkedin' => 'LinkedIn',
					'whatsapp' => 'WhatsApp',
					'email'    => esc_html__( 'E-mail', 'tools-adapter' ),
				],
			]
		);
	}
	protected function get_value( array $options = [] ) {
		$id    = Helper::post_id();
		$url   = rawurlencode( (string) get_permalink( $id ) );
		$title = rawurlencode( Helper::plain( get_the_title( $id ) ) );

		switch ( $this->get_settings( 'network' ) ) {
			case 'x':
				return 'https://twitter.com/intent/tweet?text=' . $title . '&url=' . $url;
			case 'linkedin':
				return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url;
			case 'whatsapp':
				return 'https://wa.me/?text=' . $title . '%20' . $url;
			case 'email':
				return 'mailto:?subject=' . $title . '&body=' . $url;
			default:
				return 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		}
	}
}

class Woo_Page_Url extends Url_Tag {
	public function get_name() {
		return 'ta-woo-page-url';
	}
	public function get_title() {
		return esc_html__( 'Page WooCommerce', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'page';
	}
	protected function register_controls() {
		$this->add_control(
			'page',
			[
				'label'   => esc_html__( 'Page', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'shop',
				'options' => [
					'shop'      => esc_html__( 'Boutique', 'tools-adapter' ),
					'cart'      => esc_html__( 'Panier', 'tools-adapter' ),
					'checkout'  => esc_html__( 'Validation de commande', 'tools-adapter' ),
					'myaccount' => esc_html__( 'Mon compte', 'tools-adapter' ),
				],
			]
		);
	}
	protected function get_value( array $options = [] ) {
		if ( ! function_exists( 'wc_get_page_permalink' ) ) {
			return '';
		}
		$page = (string) $this->get_settings( 'page' );
		return in_array( $page, [ 'shop', 'cart', 'checkout', 'myaccount' ], true ) ? (string) wc_get_page_permalink( $page ) : '';
	}
}

/* ======================================================================
 * Balises IMAGE
 * ==================================================================== */

abstract class Image_Tag extends Data_Tag {

	use In_Group;

	public function get_categories() {
		return [ Tags_Module::IMAGE_CATEGORY ];
	}
}

class Featured_Image extends Image_Tag {
	public function get_name() {
		return 'ta-featured-image';
	}
	public function get_title() {
		return esc_html__( 'Image mise en avant', 'tools-adapter' );
	}
	protected function register_controls() {
		$this->add_control( 'fallback', [ 'label' => esc_html__( 'Image de secours', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA ] );
	}
	protected function get_value( array $options = [] ) {
		$data = Helper::image_data( get_post_thumbnail_id( Helper::post_id() ) );
		if ( ! $data ) {
			$fallback = $this->get_settings( 'fallback' );
			$data     = is_array( $fallback ) ? $fallback : [];
		}
		return $data;
	}
}

class Site_Logo extends Image_Tag {
	public function get_name() {
		return 'ta-site-logo';
	}
	public function get_title() {
		return esc_html__( 'Logo du site', 'tools-adapter' );
	}
	protected function get_value( array $options = [] ) {
		return Helper::image_data( (int) get_theme_mod( 'custom_logo' ) );
	}
}

class Meta_Image extends Image_Tag {
	public function get_name() {
		return 'ta-meta-image';
	}
	public function get_title() {
		return esc_html__( 'Champ personnalisé (image)', 'tools-adapter' );
	}
	public function get_panel_template_setting_key() {
		return 'key';
	}
	protected function register_controls() {
		$this->add_control(
			'key',
			[
				'label'       => esc_html__( 'Clé du champ', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'description' => esc_html__( 'Le champ doit contenir l\'ID d\'une image de la médiathèque ou son URL.', 'tools-adapter' ),
			]
		);
	}
	protected function get_value( array $options = [] ) {
		$key = trim( (string) $this->get_settings( 'key' ) );
		if ( '' === $key || '_' === $key[0] ) {
			return [];
		}
		$value = get_post_meta( Helper::post_id(), $key, true );
		if ( is_numeric( $value ) ) {
			return Helper::image_data( (int) $value );
		}
		if ( is_string( $value ) && wp_http_validate_url( $value ) ) {
			return [ 'id' => 0, 'url' => esc_url_raw( $value ) ];
		}
		return [];
	}
}

/**
 * Enregistre le groupe et toutes les balises auprès d'Elementor.
 *
 * @param \Elementor\Core\DynamicTags\Manager $manager Gestionnaire de balises.
 */
function register( $manager ) {
	$manager->register_group( GROUP, [ 'title' => 'Tools Adapter' ] );

	$tags = [
		// Liens.
		Post_Url::class,
		Page_Url::class,
		Home_Url::class,
		Phone_Url::class,
		Email_Url::class,
		Whatsapp_Url::class,
		Maps_Url::class,
		Share_Url::class,
		Woo_Page_Url::class,
		// Textes.
		Post_Title::class,
		Post_Excerpt::class,
		Post_Date::class,
		Author_Name::class,
		Term_Name::class,
		Site_Name::class,
		Site_Tagline::class,
		Current_Year::class,
		Custom_Field::class,
		Url_Parameter::class,
		Product_Price::class,
		// Images.
		Featured_Image::class,
		Site_Logo::class,
		Meta_Image::class,
	];

	foreach ( $tags as $class ) {
		$manager->register( new $class() );
	}
}
