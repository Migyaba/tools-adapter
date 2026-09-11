<?php
namespace ToolsAdapter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AJAX endpoint for the "Formulaire de contact" widget.
 */
final class Ajax_Contact {

	const ACTION = 'tools_adapter_contact_submit';
	const NONCE  = 'tools_adapter_contact';

	public function __construct() {
		add_action( 'wp_ajax_' . self::ACTION, [ $this, 'handle' ] );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, [ $this, 'handle' ] );
	}

	/**
	 * Very small throttle: max 5 submissions per minute per IP.
	 *
	 * @return bool
	 */
	private function is_rate_limited() {
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$key = 'ta_contact_' . md5( $ip );
		$count = (int) get_transient( $key );
		if ( $count >= 5 ) {
			return true;
		}
		set_transient( $key, $count + 1, MINUTE_IN_SECONDS );
		return false;
	}

	public function handle() {
		check_ajax_referer( self::NONCE, 'nonce' );

		// Honeypot: bots fill hidden fields, real users never do.
		if ( ! empty( $_POST['ta_hp'] ) ) {
			wp_send_json_success( [ 'message' => __( 'Merci, votre message a bien été envoyé.', 'tools-adapter' ) ] );
		}

		if ( $this->is_rate_limited() ) {
			wp_send_json_error( [ 'message' => __( 'Trop de tentatives, veuillez réessayer dans une minute.', 'tools-adapter' ) ], 429 );
		}

		$name    = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone   = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$to      = isset( $_POST['recipient'] ) ? sanitize_email( wp_unslash( $_POST['recipient'] ) ) : '';

		if ( '' === $name || '' === $message || ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => __( 'Merci de renseigner votre nom, un e-mail valide et un message.', 'tools-adapter' ) ], 400 );
		}

		if ( '' === $to || ! is_email( $to ) ) {
			$to = get_option( 'admin_email' );
		}

		$site_name = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		/* translators: %s: site name. */
		$email_subject = $subject ? $subject : sprintf( __( 'Nouveau message de contact — %s', 'tools-adapter' ), $site_name );

		$body_lines = [
			sprintf( __( 'Nom : %s', 'tools-adapter' ), $name ),
			sprintf( __( 'E-mail : %s', 'tools-adapter' ), $email ),
		];
		if ( $phone ) {
			$body_lines[] = sprintf( __( 'Téléphone : %s', 'tools-adapter' ), $phone );
		}
		if ( $subject ) {
			$body_lines[] = sprintf( __( 'Sujet : %s', 'tools-adapter' ), $subject );
		}
		$body_lines[] = '';
		$body_lines[] = $message;

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
		if ( is_email( $email ) ) {
			$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
		}

		$sent = wp_mail( $to, $email_subject, implode( "\n", $body_lines ), $headers );

		if ( ! $sent ) {
			wp_send_json_error( [ 'message' => __( 'L\'envoi du message a échoué. Merci de réessayer plus tard.', 'tools-adapter' ) ], 500 );
		}

		wp_send_json_success( [ 'message' => __( 'Merci, votre message a bien été envoyé.', 'tools-adapter' ) ] );
	}
}
