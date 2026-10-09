<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use ToolsAdapter\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Carte Interactive des Zones d'Intervention
 * Cartographie Leaflet / OpenStreetMap personnalisable avec rayons concentriques,
 * marqueurs personnalisés, infobulles, filtres de villes et vérificateur d'éligibilité.
 */
class Interactive_Map extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-interactive-map';
	}

	public function get_title() {
		return esc_html__( 'Carte Interactive des Zones', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'carte', 'map', 'zone intervention', 'leaflet', 'secteurs', 'communes', 'rayon', 'éligibilité', 'zones' ];
	}

	public function get_style_depends() {
		return [ 'leaflet-css', 'tools-adapter-interactive-map' ];
	}

	public function get_script_depends() {
		return [ 'leaflet-js', 'tools-adapter-interactive-map' ];
	}

	protected function register_controls() {

		// ==========================================
		// SECTION CONTENU : CONFIGURATION DE LA CARTE
		// ==========================================
		$this->start_controls_section(
			'section_map_settings',
			[ 'label' => esc_html__( 'Configuration de la carte', 'tools-adapter' ) ]
		);

		$this->add_control(
			'center_lat',
			[
				'label'       => esc_html__( 'Latitude du centre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '48.715',
				'label_block' => true,
			]
		);

		$this->add_control(
			'center_lng',
			[
				'label'       => esc_html__( 'Longitude du centre', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '7.735',
				'label_block' => true,
			]
		);

		$this->add_control(
			'default_zoom',
			[
				'label'   => esc_html__( 'Zoom par défaut', 'tools-adapter' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 4,
				'max'     => 18,
			]
		);

		$this->add_control(
			'tile_theme',
			[
				'label'   => esc_html__( 'Thème graphique de la carte', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'stadia_smooth',
				'options' => [
					'stadia_smooth'   => esc_html__( 'ESRI Light Gray (Clair & Épuré — Recommandé)', 'tools-adapter' ),
					'stadia_bright'   => esc_html__( 'ESRI World Street (Coloré & Détaillé)', 'tools-adapter' ),
					'stadia_dark'     => esc_html__( 'ESRI Satellite (Imagerie Aérienne)', 'tools-adapter' ),
					'stadia_outdoors' => esc_html__( 'ESRI World Topo (Relief & Topographie)', 'tools-adapter' ),
					'osm_standard'    => esc_html__( 'OpenStreetMap Standard', 'tools-adapter' ),
					'osm_hot'         => esc_html__( 'OpenStreetMap HOT (Humanitaire)', 'tools-adapter' ),
				],
			]
		);

		$this->add_responsive_control(
			'map_height',
			[
				'label'      => esc_html__( 'Hauteur de la carte', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'vh' ],
				'range'      => [
					'px' => [ 'min' => 250, 'max' => 900 ],
					'vh' => [ 'min' => 20, 'max' => 90 ],
				],
				'default'    => [
					'size' => 480,
					'unit' => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-canvas' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'scroll_wheel_zoom',
			[
				'label'        => esc_html__( 'Zoom à la molette', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$this->add_control(
			'dragging',
			[
				'label'        => esc_html__( 'Déplacement tactile & souris', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Oui', 'tools-adapter' ),
				'label_off'    => esc_html__( 'Non', 'tools-adapter' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : ZONES D'INTERVENTION (RAYONS)
		// ==========================================
		$this->start_controls_section(
			'section_zones',
			[ 'label' => esc_html__( 'Zones d\'intervention (Rayons)', 'tools-adapter' ) ]
		);

		// Zone 1
		$this->add_control(
			'enable_zone_1',
			[
				'label'        => esc_html__( 'Activer Zone 1 (Prioritaire / Cœur)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'zone_1_name',
			[
				'label'     => esc_html__( 'Nom de la Zone 1', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Zone Cœur (< 22 km - Intervention sous 24h-48h)', 'tools-adapter' ),
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_1_radius',
			[
				'label'     => esc_html__( 'Rayon (en km)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 22,
				'min'       => 1,
				'max'       => 200,
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_1_border_color',
			[
				'label'     => esc_html__( 'Couleur bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_1_fill_color',
			[
				'label'     => esc_html__( 'Couleur de remplissage', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_1_fill_opacity',
			[
				'label'     => esc_html__( 'Opacité du fond', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0.01, 'max' => 0.6, 'step' => 0.01 ] ],
				'default'   => [ 'size' => 0.12 ],
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_1_border_style',
			[
				'label'     => esc_html__( 'Style de bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => [
					'solid'  => esc_html__( 'Continu', 'tools-adapter' ),
					'dashed' => esc_html__( 'Pointillés', 'tools-adapter' ),
				],
				'condition' => [ 'enable_zone_1' => 'yes' ],
			]
		);

		// Zone 2
		$this->add_control(
			'enable_zone_2',
			[
				'label'        => esc_html__( 'Activer Zone 2 (Étendue / Régionale)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'zone_2_name',
			[
				'label'     => esc_html__( 'Nom de la Zone 2', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Zone Étendue (< 42 km - Bas-Rhin & Eurométropole)', 'tools-adapter' ),
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_2_radius',
			[
				'label'     => esc_html__( 'Rayon (en km)', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 42,
				'min'       => 1,
				'max'       => 300,
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_2_border_color',
			[
				'label'     => esc_html__( 'Couleur bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d97706',
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_2_fill_color',
			[
				'label'     => esc_html__( 'Couleur de remplissage', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#d97706',
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_2_fill_opacity',
			[
				'label'     => esc_html__( 'Opacité du fond', 'tools-adapter' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 'px' => [ 'min' => 0.01, 'max' => 0.6, 'step' => 0.01 ] ],
				'default'   => [ 'size' => 0.05 ],
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->add_control(
			'zone_2_border_style',
			[
				'label'     => esc_html__( 'Style de bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'dashed',
				'options'   => [
					'solid'  => esc_html__( 'Continu', 'tools-adapter' ),
					'dashed' => esc_html__( 'Pointillés', 'tools-adapter' ),
				],
				'condition' => [ 'enable_zone_2' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : POINTS D'INTÉRÊT & COMMUNES
		// ==========================================
		$this->start_controls_section(
			'section_locations',
			[ 'label' => esc_html__( 'Points & Communes (Marqueurs)', 'tools-adapter' ) ]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__( 'Nom / Ville', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Brumath (67170)', 'tools-adapter' ),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'slug',
			[
				'label'       => esc_html__( 'Identifiant / Slug (pour le filtre)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => 'brumath',
				'description' => esc_html__( 'Lettres minuscules sans espace, ex: brumath, strasbourg', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'type',
			[
				'label'   => esc_html__( 'Type de marqueur', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'city',
				'options' => [
					'depot' => esc_html__( 'Dépôt / Atelier technique', 'tools-adapter' ),
					'siege' => esc_html__( 'Siège social / Bureau', 'tools-adapter' ),
					'city'  => esc_html__( 'Ville / Secteur couvert', 'tools-adapter' ),
				],
			]
		);

		$repeater->add_control(
			'badge',
			[
				'label'   => esc_html__( 'Badge popup', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Zone Immédiate', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'lat',
			[
				'label'       => esc_html__( 'Latitude', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '48.7303',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'lng',
			[
				'label'       => esc_html__( 'Longitude', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '7.7108',
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'address',
			[
				'label'   => esc_html__( 'Adresse ou communes rattachées', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( '11 Rue des Tuiles, 67170 Brumath', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'meta_text',
			[
				'label'   => esc_html__( 'Information / Délai', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Déplacement sous 24h &bull; Offert', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'icon',
			[
				'label'   => esc_html__( 'Icône du marqueur', 'tools-adapter' ),
				'type'    => Controls_Manager::ICONS,
				'default' => [
					'value'   => 'fas fa-map-marker-alt',
					'library' => 'fa-solid',
				],
			]
		);

		$repeater->add_control(
			'has_pulse',
			[
				'label'        => esc_html__( 'Effet de pulsation (Pulse)', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'no',
			]
		);

		$repeater->add_control(
			'btn_text',
			[
				'label'   => esc_html__( 'Texte du bouton popup', 'tools-adapter' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Demander un devis', 'tools-adapter' ),
			]
		);

		$repeater->add_control(
			'btn_url',
			[
				'label'       => esc_html__( 'Lien du bouton popup', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://votresite.com/contact/',
				'default'     => [ 'url' => '#contact' ],
			]
		);

		$this->add_control(
			'locations_list',
			[
				'label'       => esc_html__( 'Liste des lieux', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'title_field' => '{{{ title }}} ({{{ type }}})',
				'default'     => [
					[
						'title'     => esc_html__( 'Brumath (67170 - Dépôt)', 'tools-adapter' ),
						'slug'      => 'brumath',
						'type'      => 'depot',
						'badge'     => esc_html__( 'Dépôt Technique', 'tools-adapter' ),
						'lat'       => '48.7303',
						'lng'       => '7.7108',
						'address'   => esc_html__( '11 Rue des Tuiles, 67170 Brumath', 'tools-adapter' ),
						'meta_text' => esc_html__( 'Point de départ des équipes', 'tools-adapter' ),
						'has_pulse' => 'yes',
						'icon'      => [ 'value' => 'fas fa-warehouse' ],
						'btn_text'  => esc_html__( 'Devis Chantier Brumath', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
					[
						'title'     => esc_html__( 'Weitbruch (67500 - Siège)', 'tools-adapter' ),
						'slug'      => 'weitbruch',
						'type'      => 'siege',
						'badge'     => esc_html__( 'Siège Social', 'tools-adapter' ),
						'lat'       => '48.7538',
						'lng'       => '7.7788',
						'address'   => esc_html__( '39 rue de la chaux, 67500 Weitbruch', 'tools-adapter' ),
						'meta_text' => esc_html__( 'Administration & Gestion', 'tools-adapter' ),
						'has_pulse' => 'yes',
						'icon'      => [ 'value' => 'fas fa-building' ],
						'btn_text'  => esc_html__( 'Contacter le siège', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
					[
						'title'     => esc_html__( 'Haguenau (67500)', 'tools-adapter' ),
						'slug'      => 'haguenau',
						'type'      => 'city',
						'badge'     => esc_html__( 'Zone Prioritaire', 'tools-adapter' ),
						'lat'       => '48.8156',
						'lng'       => '7.7894',
						'address'   => esc_html__( 'Haguenau et communauté de communes', 'tools-adapter' ),
						'meta_text' => esc_html__( '~15 min &bull; Devis gratuit', 'tools-adapter' ),
						'icon'      => [ 'value' => 'fas fa-map-marker-alt' ],
						'btn_text'  => esc_html__( 'Devis à Haguenau', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
					[
						'title'     => esc_html__( 'Strasbourg (67000)', 'tools-adapter' ),
						'slug'      => 'strasbourg',
						'type'      => 'city',
						'badge'     => esc_html__( 'Zone Fréquente', 'tools-adapter' ),
						'lat'       => '48.5734',
						'lng'       => '7.7521',
						'address'   => esc_html__( 'Strasbourg & Eurométropole', 'tools-adapter' ),
						'meta_text' => esc_html__( '~20 min &bull; ITE & Peinture', 'tools-adapter' ),
						'icon'      => [ 'value' => 'fas fa-map-marker-alt' ],
						'btn_text'  => esc_html__( 'Devis à Strasbourg', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
					[
						'title'     => esc_html__( 'Truchtersheim (67370)', 'tools-adapter' ),
						'slug'      => 'truchtersheim',
						'type'      => 'city',
						'badge'     => esc_html__( 'Kochersberg', 'tools-adapter' ),
						'lat'       => '48.6625',
						'lng'       => '7.6083',
						'address'   => esc_html__( 'Kochersberg, Wiwersheim', 'tools-adapter' ),
						'meta_text' => esc_html__( '~15 min &bull; Rénovation maisons', 'tools-adapter' ),
						'icon'      => [ 'value' => 'fas fa-map-marker-alt' ],
						'btn_text'  => esc_html__( 'Devis Truchtersheim', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
					[
						'title'     => esc_html__( 'Vendenheim (67550)', 'tools-adapter' ),
						'slug'      => 'vendenheim',
						'type'      => 'city',
						'badge'     => esc_html__( 'Zone Immédiate', 'tools-adapter' ),
						'lat'       => '48.6694',
						'lng'       => '7.7125',
						'address'   => esc_html__( 'Vendenheim, Mundolsheim', 'tools-adapter' ),
						'meta_text' => esc_html__( '~10 min &bull; Déplacement offert', 'tools-adapter' ),
						'icon'      => [ 'value' => 'fas fa-map-marker-alt' ],
						'btn_text'  => esc_html__( 'Devis Vendenheim', 'tools-adapter' ),
						'btn_url'   => [ 'url' => '#contact' ],
					],
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : FILTRES / RACCOURCIS DE VILLES
		// ==========================================
		$this->start_controls_section(
			'section_pills',
			[ 'label' => esc_html__( 'Filtres & raccourcis de villes', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_city_pills',
			[
				'label'        => esc_html__( 'Afficher les pilules de villes', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'pill_all_text',
			[
				'label'     => esc_html__( 'Texte bouton "Vue Globale"', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Vue Globale (Bas-Rhin)', 'tools-adapter' ),
				'condition' => [ 'show_city_pills' => 'yes' ],
			]
		);

		$this->add_responsive_control(
			'pills_alignment',
			[
				'label'     => esc_html__( 'Alignement', 'tools-adapter' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [ 'title' => esc_html__( 'Gauche', 'tools-adapter' ), 'icon' => 'eicon-text-align-left' ],
					'center' => [ 'title' => esc_html__( 'Centre', 'tools-adapter' ), 'icon' => 'eicon-text-align-center' ],
					'right'  => [ 'title' => esc_html__( 'Droite', 'tools-adapter' ), 'icon' => 'eicon-text-align-right' ],
				],
				'default'   => 'center',
				'condition' => [ 'show_city_pills' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : VÉRIFICATEUR D'ÉLIGIBILITÉ EXPRESS
		// ==========================================
		$this->start_controls_section(
			'section_checker',
			[ 'label' => esc_html__( 'Vérificateur d\'éligibilité express', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_eligibility_checker',
			[
				'label'        => esc_html__( 'Afficher le testeur d\'éligibilité', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'checker_title',
			[
				'label'       => esc_html__( 'Titre du testeur', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Vérifiez si votre commune est couverte par nos équipes :', 'tools-adapter' ),
				'label_block' => true,
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_badge',
			[
				'label'     => esc_html__( 'Badge de réassurance', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Déplacement offert dans tout le 67', 'tools-adapter' ),
				'condition' => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_placeholder',
			[
				'label'       => esc_html__( 'Placeholder du champ', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Entrez votre ville ou code postal (ex: 67170, Haguenau, Strasbourg...)', 'tools-adapter' ),
				'label_block' => true,
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_btn_text',
			[
				'label'     => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Vérifier mon secteur', 'tools-adapter' ),
				'condition' => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_allowed_zip',
			[
				'label'       => esc_html__( 'Préfixes de codes postaux couverts', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => '67',
				'description' => esc_html__( 'Séparés par virgules. Ex: 67, 68 pour toute l\'Alsace', 'tools-adapter' ),
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_allowed_cities',
			[
				'label'       => esc_html__( 'Communes reconnues (séparées par virgules)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => 'brumath, haguenau, strasbourg, truchtersheim, vendenheim, bischwiller, hoerdt, hœrdt, weyersheim, weitbruch, schiltigheim, mundolsheim, saverne, obernai',
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_success_msg',
			[
				'label'       => esc_html__( 'Message de confirmation (Succès)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Parfait ! Votre secteur est 100% couvert avec déplacement et visite technique offerts.', 'tools-adapter' ),
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_extended_msg',
			[
				'label'       => esc_html__( 'Message pour secteur étendu / hors zone', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Nous intervenons sur l\'ensemble du département. Contactez-nous directement au 06 35 52 43 70 pour valider le délai.', 'tools-adapter' ),
				'condition'   => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : LÉGENDE DE LA CARTE
		// ==========================================
		$this->start_controls_section(
			'section_legend',
			[ 'label' => esc_html__( 'Légende de la carte', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_legend',
			[
				'label'        => esc_html__( 'Afficher la légende sur la carte', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'legend_title',
			[
				'label'     => esc_html__( 'Titre de la légende', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Rayons d\'Intervention', 'tools-adapter' ),
				'condition' => [ 'show_legend' => 'yes' ],
			]
		);

		$this->add_control(
			'legend_position',
			[
				'label'     => esc_html__( 'Position sur la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'pos-bottom-left',
				'options'   => [
					'pos-bottom-left'  => esc_html__( 'En bas à gauche', 'tools-adapter' ),
					'pos-bottom-right' => esc_html__( 'En bas à droite', 'tools-adapter' ),
					'pos-top-left'     => esc_html__( 'En haut à gauche', 'tools-adapter' ),
					'pos-top-right'    => esc_html__( 'En haut à droite', 'tools-adapter' ),
				],
				'condition' => [ 'show_legend' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// SECTION CONTENU : PIED D'INFORMATION
		// ==========================================
		$this->start_controls_section(
			'section_footer_info',
			[ 'label' => esc_html__( 'Pied d\'information sous la carte', 'tools-adapter' ) ]
		);

		$this->add_control(
			'show_footer_info',
			[
				'label'        => esc_html__( 'Afficher le pied d\'information', 'tools-adapter' ),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'footer_text',
			[
				'label'       => esc_html__( 'Texte d\'information', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'default'     => esc_html__( 'Votre commune n\'apparaît pas sur les raccourcis ? Nous intervenons dans tout le Bas-Rhin (Mundolsheim, Reichstett, Saverne, Obernai...).', 'tools-adapter' ),
				'condition'   => [ 'show_footer_info' => 'yes' ],
			]
		);

		$this->add_control(
			'footer_btn_text',
			[
				'label'     => esc_html__( 'Texte du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Voir toutes les 40+ communes desservies', 'tools-adapter' ),
				'condition' => [ 'show_footer_info' => 'yes' ],
			]
		);

		$this->add_control(
			'footer_btn_link',
			[
				'label'       => esc_html__( 'Lien du bouton', 'tools-adapter' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => 'https://votresite.com/zone-intervention/',
				'default'     => [ 'url' => '#' ],
				'condition'   => [ 'show_footer_info' => 'yes' ],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLES : CONTENEUR DE CARTE
		// ==========================================
		$this->start_controls_section(
			'section_style_map',
			[
				'label' => esc_html__( 'Carte & Conteneur', 'tools-adapter' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'map_border_radius',
			[
				'label'      => esc_html__( 'Rayon des angles (Border Radius)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => '20',
					'right'  => '20',
					'bottom' => '20',
					'left'   => '20',
					'unit'   => 'px',
				],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-canvas-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'map_box_shadow',
				'selector' => '{{WRAPPER}} .ta-map-canvas-container',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'map_border',
				'selector' => '{{WRAPPER}} .ta-map-canvas-container',
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLES : VÉRIFICATEUR D'ÉLIGIBILITÉ
		// ==========================================
		$this->start_controls_section(
			'section_style_checker',
			[
				'label'     => esc_html__( 'Vérificateur d\'éligibilité', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_eligibility_checker' => 'yes' ],
			]
		);

		$this->add_control(
			'checker_bg_color',
			[
				'label'     => esc_html__( 'Couleur de fond de la carte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-map-checker-card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'checker_btn_color',
			[
				'label'     => esc_html__( 'Couleur du bouton de recherche', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					'{{WRAPPER}} .ta-map-checker-btn' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'checker_btn_hover_color',
			[
				'label'     => esc_html__( 'Couleur survol du bouton', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#005a8b',
				'selectors' => [
					'{{WRAPPER}} .ta-map-checker-btn:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		// ==========================================
		// STYLES : PILULES DE VILLES
		// ==========================================
		$this->start_controls_section(
			'section_style_pills',
			[
				'label'     => esc_html__( 'Pilules de villes', 'tools-adapter' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 'show_city_pills' => 'yes' ],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pills_typography',
				'selector' => '{{WRAPPER}} .ta-map-pill',
			]
		);

		$this->add_control(
			'pill_normal_heading',
			[
				'label' => esc_html__( '— État Normal', 'tools-adapter' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'pill_color',
			[
				'label'     => esc_html__( 'Couleur du texte', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill:not(.active):not(.pill-all)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pill_bg',
			[
				'label'     => esc_html__( 'Couleur de fond', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill:not(.active):not(.pill-all)' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pill_border_color',
			[
				'label'     => esc_html__( 'Couleur de bordure', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e2e8f0',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill:not(.active):not(.pill-all)' => 'border-color: {{VALUE}};',
				],
			]
		);

		// ---- État ACTIF / SURVOL ----
		$this->add_control(
			'pill_active_heading',
			[
				'label'     => esc_html__( '— État Actif & Survol', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pill_active_bg',
			[
				'label'     => esc_html__( 'Fond actif / survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0077b6',
				'selectors' => [
					// Fond + bordure sur hover/active (hors pill-all qui garde son propre fond)
					'{{WRAPPER}} .ta-map-pill:not(.pill-all):hover, {{WRAPPER}} .ta-map-pill:not(.pill-all).active' =>
						'background-color: {{VALUE}}; border-color: {{VALUE}};',
					// Propagation de la CSS var pour que la box-shadow suive aussi la couleur choisie
					'{{WRAPPER}}' => '--ta-map-accent: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pill_active_text_color',
			[
				'label'     => esc_html__( 'Couleur du texte actif / survol', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill:hover, {{WRAPPER}} .ta-map-pill.active' => 'color: {{VALUE}};',
				],
			]
		);

		// ---- Bouton "Toute la région" ----
		$this->add_control(
			'pill_all_heading',
			[
				'label'     => esc_html__( '— Bouton « Toute la région »', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'pill_all_bg',
			[
				'label'     => esc_html__( 'Fond du bouton « Tout »', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#0b2545',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill.pill-all:not(.active):not(:hover)' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pill_all_color',
			[
				'label'     => esc_html__( 'Texte du bouton « Tout »', 'tools-adapter' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .ta-map-pill.pill-all:not(.active):not(:hover)' => 'color: {{VALUE}};',
				],
			]
		);

		// ---- Forme & Espacement ----
		$this->add_control(
			'pill_layout_heading',
			[
				'label'     => esc_html__( '— Forme & Espacement', 'tools-adapter' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'pill_border_radius',
			[
				'label'      => esc_html__( 'Rayon de bordure', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 50 ] ],
				'default'    => [ 'size' => 9999, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-pill' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pill_padding',
			[
				'label'      => esc_html__( 'Espacement interne (padding)', 'tools-adapter' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [ 'top' => 8, 'right' => 16, 'bottom' => 8, 'left' => 16, 'unit' => 'px', 'isLinked' => false ],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-pill' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pill_gap',
			[
				'label'      => esc_html__( 'Espacement entre les pilules', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 30 ] ],
				'default'    => [ 'size' => 8, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-pills' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pills_margin_bottom',
			[
				'label'      => esc_html__( 'Marge sous les pilules', 'tools-adapter' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [ 'px' => [ 'min' => 0, 'max' => 60 ] ],
				'default'    => [ 'size' => 20, 'unit' => 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .ta-map-pills' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		// Préparation des zones
		$zones = [];
		if ( 'yes' === $settings['enable_zone_1'] ) {
			$zones[] = [
				'name'         => $settings['zone_1_name'],
				'enabled'      => true,
				'radius_km'    => $settings['zone_1_radius'],
				'border_color' => $settings['zone_1_border_color'],
				'fill_color'   => $settings['zone_1_fill_color'],
				'fill_opacity' => $settings['zone_1_fill_opacity']['size'],
				'border_style' => $settings['zone_1_border_style'],
			];
		}
		if ( 'yes' === $settings['enable_zone_2'] ) {
			$zones[] = [
				'name'         => $settings['zone_2_name'],
				'enabled'      => true,
				'radius_km'    => $settings['zone_2_radius'],
				'border_color' => $settings['zone_2_border_color'],
				'fill_color'   => $settings['zone_2_fill_color'],
				'fill_opacity' => $settings['zone_2_fill_opacity']['size'],
				'border_style' => $settings['zone_2_border_style'],
			];
		}

		// Préparation des marqueurs
		$locations = [];
		if ( ! empty( $settings['locations_list'] ) ) {
			foreach ( $settings['locations_list'] as $item ) {
				$icon_val = 'fas fa-map-marker-alt';
				if ( ! empty( $item['icon']['value'] ) ) {
					$icon_val = is_string( $item['icon']['value'] ) ? $item['icon']['value'] : 'fas fa-map-marker-alt';
				}

				$locations[] = [
					'title'      => $item['title'],
					'slug'       => ! empty( $item['slug'] ) ? sanitize_title( $item['slug'] ) : sanitize_title( $item['title'] ),
					'type'       => $item['type'],
					'badge'      => $item['badge'],
					'lat'        => $item['lat'],
					'lng'        => $item['lng'],
					'address'    => $item['address'],
					'meta_text'  => $item['meta_text'],
					'icon'       => $icon_val,
					'has_pulse'  => $item['has_pulse'],
					'btn_text'   => $item['btn_text'],
					'btn_url'    => ! empty( $item['btn_url']['url'] ) ? esc_url( $item['btn_url']['url'] ) : '',
					'color'      => ! empty( $item['marker_color']['value'] ) ? esc_attr( $item['marker_color']['value'] ) : '',
				];
			}
		}

		// Config JSON transmise au JavaScript
		$map_config = [
			'center_lat'             => $settings['center_lat'],
			'center_lng'             => $settings['center_lng'],
			'default_zoom'           => $settings['default_zoom'],
			'tile_theme'             => $settings['tile_theme'],
			'scroll_wheel_zoom'      => $settings['scroll_wheel_zoom'],
			'dragging'               => $settings['dragging'],
			'zones'                  => $zones,
			'locations'              => $locations,
			'checker_allowed_zip'    => $settings['checker_allowed_zip'],
			'checker_allowed_cities' => $settings['checker_allowed_cities'],
			'checker_success_msg'    => $settings['checker_success_msg'],
			'checker_extended_msg'   => $settings['checker_extended_msg'],
		];

		$unique_id   = 'ta-map-' . $this->get_id();
		$pills_align = ! empty( $settings['pills_alignment'] ) ? 'align-' . $settings['pills_alignment'] : 'align-center';

		// Couleur d'accent globale : couleur de la Zone 1 ou fallback
		$accent_color = ! empty( $settings['zone_1_color']['value'] ) ? esc_attr( $settings['zone_1_color']['value'] ) : '#0077b6';
		$css_vars     = '--ta-map-accent:' . $accent_color . ';';
		?>
		<div class="tools-adapter-map-wrapper"
		     data-map-config="<?php echo esc_attr( wp_json_encode( $map_config ) ); ?>"
		     style="<?php echo esc_attr( $css_vars ); ?>">

			<?php if ( 'yes' === $settings['show_eligibility_checker'] ) : ?>
				<!-- Vérificateur d'éligibilité express -->
				<div class="ta-map-checker-card">
					<div class="ta-map-checker-header">
						<span class="ta-map-checker-title">
							<i class="fas fa-crosshairs" style="color: #0077b6;"></i>
							<?php echo esc_html( $settings['checker_title'] ); ?>
						</span>
						<?php if ( ! empty( $settings['checker_badge'] ) ) : ?>
							<span class="ta-map-checker-badge">
								<i class="fas fa-car-side"></i> <?php echo esc_html( $settings['checker_badge'] ); ?>
							</span>
						<?php endif; ?>
					</div>

					<div class="ta-map-checker-form">
						<div class="ta-map-input-wrapper">
							<i class="fas fa-city"></i>
							<input type="text" class="ta-map-checker-input" placeholder="<?php echo esc_attr( $settings['checker_placeholder'] ); ?>">
						</div>
						<button type="button" class="ta-map-checker-btn">
							<i class="fas fa-search"></i> <?php echo esc_html( $settings['checker_btn_text'] ); ?>
						</button>
					</div>

					<div class="ta-map-checker-result"></div>
				</div>
			<?php endif; ?>

			<?php if ( 'yes' === $settings['show_city_pills'] && ! empty( $locations ) ) : ?>
				<!-- Pilules / Raccourcis de Villes -->
				<div class="ta-map-pills <?php echo esc_attr( $pills_align ); ?>">
					<?php if ( ! empty( $settings['pill_all_text'] ) ) : ?>
						<button type="button" class="ta-map-pill pill-all active" data-city="all">
							<i class="fas fa-globe"></i> <?php echo esc_html( $settings['pill_all_text'] ); ?>
						</button>
					<?php endif; ?>

					<?php foreach ( $locations as $loc ) : ?>
						<button type="button" class="ta-map-pill" data-city="<?php echo esc_attr( $loc['slug'] ); ?>">
							<i class="<?php echo esc_attr( $loc['icon'] ); ?>"></i>
							<?php echo esc_html( $loc['title'] ); ?>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<!-- Conteneur Leaflet Canvas -->
			<div class="ta-map-canvas-container">
				<div id="<?php echo esc_attr( $unique_id ); ?>" class="ta-map-canvas"></div>

				<?php if ( 'yes' === $settings['show_legend'] ) : 
					$pos_class = ! empty( $settings['legend_position'] ) ? $settings['legend_position'] : 'pos-bottom-left';
				?>
					<!-- Légende Flottante -->
					<div class="ta-map-legend <?php echo esc_attr( $pos_class ); ?>">
						<div class="ta-map-legend-title">
							<i class="fas fa-layer-group" style="color: #0077b6;"></i>
							<?php echo esc_html( $settings['legend_title'] ); ?>
						</div>
						<div class="ta-map-legend-items">
							<?php if ( 'yes' === $settings['enable_zone_1'] ) :
								$z1_color = ! empty( $settings['zone_1_color']['value'] ) ? esc_attr( $settings['zone_1_color']['value'] ) : '#0077b6';
							?>
								<div class="ta-map-legend-item">
									<span class="ta-legend-bullet" style="background-color:<?php echo $z1_color; ?>;border-color:<?php echo $z1_color; ?>;"></span>
									<span><?php echo esc_html( $settings['zone_1_name'] ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( 'yes' === $settings['enable_zone_2'] ) :
								$z2_color = ! empty( $settings['zone_2_color']['value'] ) ? esc_attr( $settings['zone_2_color']['value'] ) : '#d97706';
							?>
								<div class="ta-map-legend-item">
									<span class="ta-legend-bullet" style="background-color:transparent;border:2px dashed <?php echo $z2_color; ?>;"></span>
									<span><?php echo esc_html( $settings['zone_2_name'] ); ?></span>
								</div>
							<?php endif; ?>
							<div class="ta-map-legend-item">
								<span class="ta-legend-bullet" style="background-color:#0b2545;border:2px solid #ffffff;box-shadow:0 0 0 1.5px #0b2545;"></span>
								<span><?php esc_html_e( 'Bases & Dépôts techniques', 'tools-adapter' ); ?></span>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( 'yes' === $settings['show_footer_info'] ) : ?>
				<!-- Pied d'information sous la carte -->
				<div class="ta-map-footer-help">
					<?php if ( ! empty( $settings['footer_text'] ) ) : ?>
						<div class="ta-map-footer-text">
							<i class="fas fa-info-circle" style="color: #0077b6;"></i>
							<?php echo wp_kses_post( $settings['footer_text'] ); ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $settings['footer_btn_text'] ) && ! empty( $settings['footer_btn_link']['url'] ) ) : ?>
						<a href="<?php echo esc_url( $settings['footer_btn_link']['url'] ); ?>" class="ta-map-footer-btn">
							<i class="fas fa-map"></i> <?php echo esc_html( $settings['footer_btn_text'] ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
		<?php
	}
}
