<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Popup preuve sociale — notifications flottantes affichant des
 * messages personnalisés ou les commandes WooCommerce récentes.
 */
class Social_Proof extends Widget_Base {

	public function get_name() {
		return 'tools-adapter-social-proof';
	}

	public function get_title() {
		return esc_html__( 'Popup preuve sociale', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-notification';
	}

	public function get_categories() {
		return [ 'tools-adapter', 'woocommerce-elements' ];
	}

	public function get_keywords() {
		return [ 'preuve sociale', 'notification', 'popup', 'ventes récentes' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-social-proof' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-social-proof' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Contenu', 'tools-adapter' ) ] );

		$this->add_control(
			'source',
			[
				'label'   => esc_html__( 'Source des messages', 'tools-adapter' ),
				'type'    => Controls_Manager::SELECT,
				'default' => class_exists( 'WooCommerce' ) ? 'orders' : 'manual',
				'options' => array_filter(
					[
						'orders' => class_exists( 'WooCommerce' ) ? esc_html__( 'Commandes WooCommerce récentes', 'tools-adapter' ) : null,
						'manual' => esc_html__( 'Messages personnalisés', 'tools-adapter' ),
					]
				),
			]
		);

		$this->add_control(
			'orders_template',
			[
				'label'       => esc_html__( 'Modèle de texte', 'tools-adapter' ),
				'description' => esc_html__( 'Variables : {product}, {city}, {time}', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Un client de {city} a acheté {product}', 'tools-adapter' ),
				'condition'   => [ 'source' => 'orders' ],
			]
		);

		$this->add_control(
			'orders_count',
			[
				'label'     => esc_html__( 'Nombre de commandes à afficher', 'tools-adapter' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 8,
				'min'       => 1,
				'max'       => 30,
				'condition' => [ 'source' => 'orders' ],
			]
		);

		$this->add_control(
			'fallback_city',
			[
				'label'       => esc_html__( 'Ville par défaut', 'tools-adapter' ),
				'description' => esc_html__( 'Utilisée si la commande n\'a pas de ville renseignée.', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'France', 'tools-adapter' ),
				'condition'   => [ 'source' => 'orders' ],
			]
		);

		$repeater = new Repeater();
		$repeater->add_control( 'image', [ 'label' => esc_html__( 'Image / avatar', 'tools-adapter' ), 'type' => Controls_Manager::MEDIA ] );
		$repeater->add_control( 'title', [ 'label' => esc_html__( 'Titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Marie vient d\'acheter le produit X', 'tools-adapter' ) ] );
		$repeater->add_control( 'subtitle', [ 'label' => esc_html__( 'Sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Il y a 3 minutes', 'tools-adapter' ) ] );
		$repeater->add_control( 'link', [ 'label' => esc_html__( 'Lien (optionnel)', 'tools-adapter' ), 'type' => Controls_Manager::URL ] );

		$this->add_control(
			'messages',
			[
				'label'       => esc_html__( 'Messages', 'tools-adapter' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => [
					[ 'title' => esc_html__( 'Sophie vient de s\'inscrire', 'tools-adapter' ), 'subtitle' => esc_html__( 'Il y a 2 minutes', 'tools-adapter' ) ],
					[ 'title' => esc_html__( '128 clients ont commandé cette semaine', 'tools-adapter' ), 'subtitle' => esc_html__( 'Rejoignez-les !', 'tools-adapter' ) ],
				],
				'title_field' => '{{{ title }}}',
				'condition'   => [ 'source' => 'manual' ],
			]
		);

		$this->add_control( 'show_icon', [ 'label' => esc_html__( 'Icône par défaut (si pas d\'image)', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'separator' => 'before' ] );
		$this->add_control( 'icon', [ 'label' => esc_html__( 'Icône', 'tools-adapter' ), 'type' => Controls_Manager::ICONS, 'default' => [ 'value' => 'fas fa-shopping-bag', 'library' => 'fa-solid' ], 'condition' => [ 'show_icon' => 'yes' ] ] );
		$this->add_control( 'show_close', [ 'label' => esc_html__( 'Bouton fermer', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_behavior', [ 'label' => esc_html__( 'Comportement', 'tools-adapter' ) ] );

		$this->add_control(
			'position',
			[
				'label'        => esc_html__( 'Position', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'bottom-left',
				'options'      => [
					'bottom-left'  => esc_html__( 'Bas gauche', 'tools-adapter' ),
					'bottom-right' => esc_html__( 'Bas droite', 'tools-adapter' ),
					'top-left'     => esc_html__( 'Haut gauche', 'tools-adapter' ),
					'top-right'    => esc_html__( 'Haut droite', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-social-proof-pos--',
			]
		);

		$this->add_control( 'initial_delay', [ 'label' => esc_html__( 'Délai avant la 1ère notification (ms)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 3000, 'min' => 0 ] );
		$this->add_control( 'display_duration', [ 'label' => esc_html__( 'Durée d\'affichage (ms)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 5000, 'min' => 1000 ] );
		$this->add_control( 'interval', [ 'label' => esc_html__( 'Intervalle entre notifications (ms)', 'tools-adapter' ), 'type' => Controls_Manager::NUMBER, 'default' => 9000, 'min' => 2000 ] );
		$this->add_control( 'loop', [ 'label' => esc_html__( 'Boucler indéfiniment', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style', [ 'label' => esc_html__( 'Style', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'bg_color', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-social-proof__toast' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'title_color', [ 'label' => esc_html__( 'Couleur du titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-social-proof__title' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'title_typography', 'selector' => '{{WRAPPER}} .ta-social-proof__title' ] );
		$this->add_control( 'subtitle_color', [ 'label' => esc_html__( 'Couleur du sous-titre', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#888888', 'selectors' => [ '{{WRAPPER}} .ta-social-proof__subtitle' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'icon_color', [ 'label' => esc_html__( 'Couleur icône', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-social-proof__icon' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 10, 'unit' => 'px' ], 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-social-proof__toast' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Box_Shadow::get_type(), [ 'name' => 'shadow', 'selector' => '{{WRAPPER}} .ta-social-proof__toast' ] );

		$this->end_controls_section();
	}

	/**
	 * @return array<int,array{title:string,subtitle:string,image:string,link:string}>
	 */
	private function get_order_items( array $settings ) {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_orders' ) ) {
			return [];
		}

		$count = absint( $settings['orders_count'] ?? 8 );
		$orders = wc_get_orders(
			[
				'limit'   => $count,
				'status'  => [ 'wc-processing', 'wc-completed' ],
				'orderby' => 'date',
				'order'   => 'DESC',
			]
		);

		$template      = $settings['orders_template'] ?? '{product} — {city}';
		$fallback_city = $settings['fallback_city'] ?? '';
		$items         = [];

		foreach ( $orders as $order ) {
			if ( ! $order instanceof \WC_Order ) {
				continue;
			}
			$order_items = $order->get_items();
			if ( empty( $order_items ) ) {
				continue;
			}
			$first_item = reset( $order_items );
			$product    = $first_item->get_product();
			$product_name = $product ? $product->get_name() : $first_item->get_name();
			$city          = $order->get_billing_city();
			$city          = $city ? $city : $fallback_city;
			$time_diff     = human_time_diff( $order->get_date_created()->getTimestamp(), time() );

			$text = str_replace(
				[ '{product}', '{city}', '{time}' ],
				[ $product_name, $city, $time_diff ],
				$template
			);

			$items[] = [
				'title'    => $text,
				/* translators: %s: human-readable time difference, e.g. "3 minutes". */
				'subtitle' => sprintf( esc_html__( 'Il y a %s', 'tools-adapter' ), $time_diff ),
				'image'    => $product ? wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ) : '',
				'link'     => '',
			];
		}

		return $items;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$source   = $settings['source'] ?? 'manual';

		if ( 'orders' === $source ) {
			$items = $this->get_order_items( $settings );
		} else {
			$items = [];
			foreach ( (array) ( $settings['messages'] ?? [] ) as $message ) {
				if ( empty( $message['title'] ) ) {
					continue;
				}
				$items[] = [
					'title'    => \tools_adapter_translate( $message['title'] ),
					'subtitle' => \tools_adapter_translate( $message['subtitle'] ?? '' ),
					'image'    => $message['image']['url'] ?? '',
					'link'     => $message['link']['url'] ?? '',
				];
			}
		}

		if ( empty( $items ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<p>' . esc_html__( 'Aucun message à afficher — ajoutez des messages personnalisés ou vérifiez qu\'il existe des commandes récentes.', 'tools-adapter' ) . '</p>';
			}
			return;
		}

		$show_icon = 'yes' === ( $settings['show_icon'] ?? '' );
		?>
		<div
			class="ta-social-proof"
			data-ta-social-proof
			data-initial-delay="<?php echo esc_attr( (string) absint( $settings['initial_delay'] ?? 3000 ) ); ?>"
			data-duration="<?php echo esc_attr( (string) absint( $settings['display_duration'] ?? 5000 ) ); ?>"
			data-interval="<?php echo esc_attr( (string) absint( $settings['interval'] ?? 9000 ) ); ?>"
			data-loop="<?php echo 'yes' === ( $settings['loop'] ?? '' ) ? '1' : '0'; ?>"
			data-items="<?php echo esc_attr( wp_json_encode( $items ) ); ?>"
		>
			<div class="ta-social-proof__toast" data-social-proof-toast hidden>
				<div class="ta-social-proof__media" data-social-proof-media>
					<?php if ( $show_icon ) : ?>
						<span class="ta-social-proof__icon" data-social-proof-icon><?php \Elementor\Icons_Manager::render_icon( $settings['icon'], [ 'aria-hidden' => 'true' ] ); ?></span>
					<?php endif; ?>
				</div>
				<div class="ta-social-proof__body">
					<p class="ta-social-proof__title" data-social-proof-title></p>
					<p class="ta-social-proof__subtitle" data-social-proof-subtitle></p>
				</div>
				<?php if ( 'yes' === ( $settings['show_close'] ?? '' ) ) : ?>
					<button type="button" class="ta-social-proof__close" data-social-proof-close aria-label="<?php echo esc_attr__( 'Fermer', 'tools-adapter' ); ?>">&times;</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
