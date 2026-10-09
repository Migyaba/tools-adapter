<?php
namespace ToolsAdapter\Widgets;

use Elementor\Controls_Manager;
use ToolsAdapter\Base_Widget;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget: Formulaire de contact stylisé — envoi AJAX par e-mail (wp_mail),
 * anti-spam (honeypot + limite de fréquence côté serveur).
 */
class Contact_Form extends Base_Widget {

	public function get_name() {
		return 'tools-adapter-contact-form';
	}

	public function get_title() {
		return esc_html__( 'Formulaire de contact', 'tools-adapter' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_categories() {
		return [ 'tools-adapter' ];
	}

	public function get_keywords() {
		return [ 'contact', 'formulaire', 'email', 'message' ];
	}

	public function get_style_depends() {
		return [ 'tools-adapter-contact-form' ];
	}

	public function get_script_depends() {
		return [ 'tools-adapter-contact-form' ];
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', [ 'label' => esc_html__( 'Champs', 'tools-adapter' ) ] );

		$this->add_control( 'show_service', [ 'label' => esc_html__( 'Sélecteur de prestation / service', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );
		$this->add_control( 'service_label', [ 'label' => esc_html__( 'Placeholder — Prestation', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( '— Sélectionner une prestation —', 'tools-adapter' ), 'condition' => [ 'show_service' => 'yes' ] ] );
		$this->add_control(
			'service_options',
			[
				'label'       => esc_html__( 'Options de prestation (une par ligne)', 'tools-adapter' ),
				'type'        => Controls_Manager::TEXTAREA,
				'rows'        => 5,
				'default'     => "Assainissement & Vidange de fosse\nTerrassement & Travaux Publics\nBois de chauffage (Stères)\nAutre demande",
				'condition'   => [ 'show_service' => 'yes' ],
			]
		);
		$this->add_control( 'service_required', [ 'label' => esc_html__( 'Prestation obligatoire', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes', 'condition' => [ 'show_service' => 'yes' ] ] );

		$this->add_control( 'show_location', [ 'label' => esc_html__( 'Champ commune / lieu du chantier', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );
		$this->add_control( 'location_placeholder', [ 'label' => esc_html__( 'Placeholder — Commune', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Commune ou code postal du chantier (ex: 67240 Schirrhein)', 'tools-adapter' ), 'condition' => [ 'show_location' => 'yes' ] ] );
		$this->add_control( 'location_required', [ 'label' => esc_html__( 'Commune obligatoire', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'condition' => [ 'show_location' => 'yes' ] ] );

		$this->add_control( 'show_phone', [ 'label' => esc_html__( 'Champ téléphone', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '' ] );
		$this->add_control( 'phone_required', [ 'label' => esc_html__( 'Téléphone obligatoire', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => '', 'condition' => [ 'show_phone' => 'yes' ] ] );
		$this->add_control( 'show_subject', [ 'label' => esc_html__( 'Champ sujet', 'tools-adapter' ), 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ] );

		$this->add_control( 'form_anchor_id', [ 'label' => esc_html__( 'ID d\'ancre HTML du formulaire', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => '', 'description' => esc_html__( 'Ex: "devis" pour pouvoir pointer un bouton vers #devis.', 'tools-adapter' ) ] );

		$this->add_control( 'name_placeholder', [ 'label' => esc_html__( 'Placeholder — Nom', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Votre nom', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'email_placeholder', [ 'label' => esc_html__( 'Placeholder — E-mail', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Votre e-mail', 'tools-adapter' ) ] );
		$this->add_control( 'phone_placeholder', [ 'label' => esc_html__( 'Placeholder — Téléphone', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Votre téléphone', 'tools-adapter' ), 'condition' => [ 'show_phone' => 'yes' ] ] );
		$this->add_control( 'subject_placeholder', [ 'label' => esc_html__( 'Placeholder — Sujet', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Sujet', 'tools-adapter' ), 'condition' => [ 'show_subject' => 'yes' ] ] );
		$this->add_control( 'message_placeholder', [ 'label' => esc_html__( 'Placeholder — Message', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Votre message', 'tools-adapter' ) ] );

		$this->add_control( 'button_text', [ 'label' => esc_html__( 'Texte du bouton', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Envoyer le message', 'tools-adapter' ), 'separator' => 'before' ] );
		$this->add_control( 'recipient_email', [ 'label' => esc_html__( 'E-mail destinataire', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => get_option( 'admin_email' ), 'description' => esc_html__( 'Laissez vide pour utiliser l\'e-mail administrateur du site.', 'tools-adapter' ) ] );
		$this->add_control( 'success_message', [ 'label' => esc_html__( 'Message de succès', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Merci, votre message a bien été envoyé !', 'tools-adapter' ) ] );
		$this->add_control( 'error_message', [ 'label' => esc_html__( 'Message d\'erreur', 'tools-adapter' ), 'type' => Controls_Manager::TEXT, 'default' => esc_html__( 'Une erreur est survenue. Merci de réessayer.', 'tools-adapter' ) ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_fields', [ 'label' => esc_html__( 'Champs & Menus déroulants', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_responsive_control( 'fields_gap', [ 'label' => esc_html__( 'Espacement', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 16, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-contact-form' => '--ta-cf-gap: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_control( 'field_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#f7f7f7', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'field_text_color', [ 'label' => esc_html__( 'Couleur du texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' => 'color: {{VALUE}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'field_border', 'selector' => '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' ] );
		$this->add_control( 'field_border_color_focus', [ 'label' => esc_html__( 'Couleur bordure (focus)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#C9A84C', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__input:focus, {{WRAPPER}} .ta-contact-form__select:focus, {{WRAPPER}} .ta-contact-form__textarea:focus' => 'border-color: {{VALUE}};' ] ] );
		$this->add_control( 'field_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 30 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_responsive_control( 'field_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '14', 'right' => '16', 'bottom' => '14', 'left' => '16', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'field_typography', 'selector' => '{{WRAPPER}} .ta-contact-form__input, {{WRAPPER}} .ta-contact-form__select, {{WRAPPER}} .ta-contact-form__textarea' ] );

		$this->end_controls_section();

		$this->start_controls_section( 'section_style_button', [ 'label' => esc_html__( 'Bouton d\'envoi', 'tools-adapter' ), 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_group_control( Group_Control_Typography::get_type(), [ 'name' => 'button_typography', 'selector' => '{{WRAPPER}} .ta-contact-form__submit' ] );

		$this->add_control( 'button_bg', [ 'label' => esc_html__( 'Fond', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#1c1c1c', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'button_color', [ 'label' => esc_html__( 'Texte', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#ffffff', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit' => 'color: {{VALUE}};' ] ] );

		$this->add_control( 'button_bg_hover', [ 'label' => esc_html__( 'Fond (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit:hover' => 'background-color: {{VALUE}};' ] ] );
		$this->add_control( 'button_color_hover', [ 'label' => esc_html__( 'Texte (survol)', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit:hover' => 'color: {{VALUE}};' ] ] );

		$this->add_responsive_control( 'button_padding', [ 'label' => esc_html__( 'Espacement interne', 'tools-adapter' ), 'type' => Controls_Manager::DIMENSIONS, 'size_units' => [ 'px' ], 'default' => [ 'top' => '14', 'right' => '32', 'bottom' => '14', 'left' => '32', 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ] ] );
		$this->add_control( 'button_radius', [ 'label' => esc_html__( 'Arrondi', 'tools-adapter' ), 'type' => Controls_Manager::SLIDER, 'range' => [ 'px' => [ 'min' => 0, 'max' => 40 ] ], 'default' => [ 'size' => 6, 'unit' => 'px' ], 'selectors' => [ '{{WRAPPER}} .ta-contact-form__submit' => 'border-radius: {{SIZE}}{{UNIT}};' ] ] );
		$this->add_group_control( Group_Control_Border::get_type(), [ 'name' => 'button_border', 'selector' => '{{WRAPPER}} .ta-contact-form__submit' ] );
		$this->add_control(
			'button_width',
			[
				'label'        => esc_html__( 'Largeur', 'tools-adapter' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'auto',
				'options'      => [
					'auto' => esc_html__( 'Automatique', 'tools-adapter' ),
					'full' => esc_html__( 'Pleine largeur', 'tools-adapter' ),
				],
				'prefix_class' => 'ta-cf-btn-width--',
			]
		);
		$this->add_control( 'success_color', [ 'label' => esc_html__( 'Couleur message succès', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#2e7d32', 'separator' => 'before', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__notice--success' => 'color: {{VALUE}};' ] ] );
		$this->add_control( 'error_color', [ 'label' => esc_html__( 'Couleur message erreur', 'tools-adapter' ), 'type' => Controls_Manager::COLOR, 'default' => '#c0392b', 'selectors' => [ '{{WRAPPER}} .ta-contact-form__notice--error' => 'color: {{VALUE}};' ] ] );

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$custom_id = ! empty( $settings['form_anchor_id'] ) ? sanitize_title( $settings['form_anchor_id'] ) : '';
		$form_id   = $custom_id ? $custom_id : 'ta-contact-form-' . $this->get_id();
		?>
		<form
			class="ta-contact-form"
			id="<?php echo esc_attr( $form_id ); ?>"
			data-ta-contact-form
			data-recipient="<?php echo esc_attr( $settings['recipient_email'] ?? '' ); ?>"
			data-recipient-sig="<?php echo esc_attr( \ToolsAdapter\Ajax_Contact::sign_recipient( $settings['recipient_email'] ?? '' ) ); ?>"
			data-success="<?php echo esc_attr( \tools_adapter_translate( $settings['success_message'] ?? '' ) ); ?>"
			data-error="<?php echo esc_attr( \tools_adapter_translate( $settings['error_message'] ?? '' ) ); ?>"
			novalidate
		>
			<?php if ( 'yes' === ( $settings['show_service'] ?? '' ) ) :
				$options_raw = $settings['service_options'] ?? '';
				$options     = array_filter( array_map( 'trim', explode( "\n", $options_raw ) ) );
				?>
				<div class="ta-contact-form__row">
					<select class="ta-contact-form__select" name="service" <?php echo 'yes' === ( $settings['service_required'] ?? '' ) ? 'required' : ''; ?>>
						<option value=""><?php echo esc_html( \tools_adapter_translate( $settings['service_label'] ?? '' ) ); ?></option>
						<?php foreach ( $options as $opt ) : ?>
							<option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="ta-contact-form__row">
				<input class="ta-contact-form__input" type="text" name="name" required placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['name_placeholder'] ?? '' ) ); ?>" autocomplete="name" />
			</div>
			<div class="ta-contact-form__row">
				<input class="ta-contact-form__input" type="email" name="email" required placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['email_placeholder'] ?? '' ) ); ?>" autocomplete="email" />
			</div>
			<?php if ( 'yes' === ( $settings['show_phone'] ?? '' ) ) : ?>
				<div class="ta-contact-form__row">
					<input class="ta-contact-form__input" type="tel" name="phone" <?php echo 'yes' === ( $settings['phone_required'] ?? '' ) ? 'required' : ''; ?> placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['phone_placeholder'] ?? '' ) ); ?>" autocomplete="tel" />
				</div>
			<?php endif; ?>
			<?php if ( 'yes' === ( $settings['show_location'] ?? '' ) ) : ?>
				<div class="ta-contact-form__row">
					<input class="ta-contact-form__input" type="text" name="location" <?php echo 'yes' === ( $settings['location_required'] ?? '' ) ? 'required' : ''; ?> placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['location_placeholder'] ?? '' ) ); ?>" />
				</div>
			<?php endif; ?>
			<?php if ( 'yes' === ( $settings['show_subject'] ?? '' ) ) : ?>
				<div class="ta-contact-form__row">
					<input class="ta-contact-form__input" type="text" name="subject" placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['subject_placeholder'] ?? '' ) ); ?>" />
				</div>
			<?php endif; ?>
			<div class="ta-contact-form__row">
				<textarea class="ta-contact-form__textarea" name="message" rows="5" required placeholder="<?php echo esc_attr( \tools_adapter_translate( $settings['message_placeholder'] ?? '' ) ); ?>"></textarea>
			</div>

			<div class="ta-contact-form__honeypot" aria-hidden="true">
				<label for="<?php echo esc_attr( $form_id . '-hp' ); ?>"><?php echo esc_html__( 'Laissez ce champ vide', 'tools-adapter' ); ?></label>
				<input type="text" id="<?php echo esc_attr( $form_id . '-hp' ); ?>" name="ta_hp" tabindex="-1" autocomplete="off" />
			</div>

			<button type="submit" class="ta-contact-form__submit" data-contact-submit>
				<span class="ta-contact-form__submit-text"><?php echo esc_html( \tools_adapter_translate( $settings['button_text'] ?? '' ) ); ?></span>
				<span class="ta-contact-form__spinner" aria-hidden="true"></span>
			</button>

			<p class="ta-contact-form__notice" data-contact-notice role="alert" hidden></p>
		</form>
		<?php
	}
}
