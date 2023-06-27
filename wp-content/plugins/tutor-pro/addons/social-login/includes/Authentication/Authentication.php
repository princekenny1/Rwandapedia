<?php
/**
 * Handle social authentication
 *
 * @package TutorPro\SocialLogin\Authentication
 * @author Themeum <support@themeum.com>
 * @link https://themeum.com
 * @since 2.1.9
 */

namespace TutorPro\SocialLogin\Authentication;

use TUTOR\Ajax;
use Tutor\Helpers\SessionHelper;
use TUTOR\Input;
use TUTOR\Instructor;
use TutorPro\SocialLogin\Lib\TwitterOauthService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage all social authentication
 */
class Authentication {

	/**
	 * Register hooks
	 *
	 * @since 2.1.9
	 */
	public function __construct() {
		add_action( 'wp_ajax_nopriv_tutor_pro_social_authentication', __CLASS__ . '::authenticate' );
		add_action( 'template_redirect', array( $this, 'twitter_oauth_verify' ) );
		add_action( 'template_redirect', array( $this, 'process_twitter_login' ) );
	}

	/**
	 * Handle authentication
	 *
	 * @return void
	 */
	public static function authenticate() {
		$auth_success_msg = __( 'You are logging in!', 'tutor-pro' );
		$auth_failed_msg  = __( 'Something went wrong, please try again!', 'tutor-pro' );

		tutor_utils()->checking_nonce();

		// Sanitize user data.
		$request = Input::sanitize_array(
			wp_unslash( $_POST ),
			array(
				'email'       => 'sanitize_email',
				'profile_url' => 'sanitize_url',
			)
		);

		// Check if the request is valid.
		if ( 'google' === $request['auth'] ) {
			$client_id = tutor_utils()->get_option( 'google_client_ID' );
			if ( ! $client_id === $request['auth_id_token'] ) {
				wp_send_json_error( 'Invalid Client ID', 'tutor-pro' );
			}
		}

		$email = $request['email'];

		// Validate emails.
		if ( empty( $email ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( __( 'Invalid email', 'tutor-pro' ) );
		}

		// User already exists.
		if ( email_exists( $email ) ) {
			$userdata = get_user_by( 'email', $email );

			if ( is_a( $userdata, 'WP_User' ) ) {
				// Logged-in the user.
				self::logged_in( $userdata );
				wp_send_json_success( $auth_success_msg );
			}
			wp_send_json_error( $auth_failed_msg );
		} else {
			$is_registration_enabled = get_option( 'users_can_register', false );
			if ( ! $is_registration_enabled ) {
				wp_send_json_error( 'Registration is not enabled, please contact with site owner!', 'tutor-pro' );
			}

			// Prepare registration.
			$prepare_user_data = array(
				'user_login' => $request['user_login'] ?? '',
				'user_email' => $request['email'] ?? '',
				'first_name' => $request['first_name'] ?? '',
				'last_name'  => $request['last_name'] ?? '',
				'user_pass'  => '',
			);

			$insert = wp_insert_user( $prepare_user_data );
			if ( is_wp_error( $insert ) ) {
				wp_send_json_error( 'User registration failed', 'tutor-pro' );
			} else {
				$userdata = get_userdata( $insert );
				if ( is_a( $userdata, 'WP_User' ) ) {
					// Check if wanted to be a instructor.
					if ( 'tutor_register_instructor' === $request['attempt'] ) {
						( new Instructor( false ) )->update_instructor_meta( $userdata->ID );
					} else {
						do_action( 'tutor_after_student_signup', $insert );
					}

					// Logged-in the user.
					self::logged_in( $userdata );
					wp_send_json_success( $auth_success_msg );
				}
				wp_send_json_error( $auth_failed_msg );
			}
		}
	}

	/**
	 * Function for Twitter Oauth Service
	 *
	 * @since 2.1.10
	 *
	 * @return void
	 */
	public function twitter_oauth_verify() {
		if ( tutor_utils()->get_option( 'enable_twitter_login' ) && ! get_current_user_id() && Input::has( 'twitter_oauth_verify' ) && Input::get( 'twitter_oauth_verify' ) == 'true' ) {
			$api_key               = tutor_utils()->get_option( 'twitter_app_key' );
			$api_key_secret        = tutor_utils()->get_option( 'twitter_app_key_secret' );
			$oauth_callback        = rtrim( tutor_utils()->tutor_dashboard_url(), '/' ) . '?tutor_twitter_login=true';
			$twitter_oauth_service = new TwitterOauthService( $api_key, $api_key_secret, $oauth_callback );
			$redirect_url          = $twitter_oauth_service->get_oauth_verifier();
			wp_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Process Twitter login after redirect
	 *
	 * @since 2.1.10
	 *
	 * @return void
	 */
	public function process_twitter_login() {
		if ( tutor_utils()->get_option( 'enable_twitter_login' ) && ! get_current_user_id() && Input::has( 'tutor_twitter_login' ) && Input::get( 'tutor_twitter_login' ) === 'true' ) {
			$oauth_token_secret    = SessionHelper::get( 'oauth_token_secret' );
			$is_instructor         = get_transient( 'twitter_login_is_instructor' );
			$api_key               = tutor_utils()->get_option( 'twitter_app_key' );
			$api_key_secret        = tutor_utils()->get_option( 'twitter_app_key_secret' );
			$oauth_callback        = rtrim( tutor_utils()->tutor_dashboard_url(), '/' ) . '?tutor_twitter_login=true';
			$twitter_oauth_service = new TwitterOauthService( $api_key, $api_key_secret, $oauth_callback );

			if ( ! empty( Input::get( 'oauth_verifier' ) ) && ! empty( Input::get( 'oauth_token' ) ) ) {
				$response_user_data = $twitter_oauth_service->get_user_data( Input::get( 'oauth_verifier' ), Input::get( 'oauth_token' ), $oauth_token_secret );
				$response_user_data = json_decode( $response_user_data, true );

				if ( is_array( $response_user_data ) && count( $response_user_data ) && ! array_key_exists( 'errors', $response_user_data ) ) {
					$name_chunks = explode( ' ', trim( $response_user_data['name'] ) );
					$max_index   = count( $name_chunks ) - 1;
					$first_name  = '';
					$last_name   = '';

					if ( $max_index < 1 ) {
						$first_name = $response_user_data['name'];
						$last_name  = '';
					} else {
						foreach ( $name_chunks as $key => $value ) {
							if ( $key < $max_index ) {
								$first_name .= $value . ' ';
							}
						}
						$first_name = trim( $first_name );
						$last_name  = $name_chunks[ $max_index ];
					}

					$email           = $response_user_data['email'];
					$user_login      = $response_user_data['screen_name'];
					$auth_failed_msg = __( 'Something went wrong, please try again!', 'tutor-pro' );

					// Validate emails.
					if ( empty( $email ) || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
						\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( __( 'Invalid email', 'tutor-pro' ) ) );
						return;
					}

					// User already exists.
					if ( email_exists( $email ) ) {
						$userdata = get_user_by( 'email', $email );

						if ( is_a( $userdata, 'WP_User' ) ) {
							// Logged-in the user.
							self::logged_in( $userdata );
						}
					} else {
						$is_registration_enabled = get_option( 'users_can_register', false );
						if ( ! $is_registration_enabled ) {
							\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( __( 'Registration is not enabled, please contact with site owner!', 'tutor-pro' ) ) );
							return;
						}

						// Prepare registration.
						$prepare_user_data = array(
							'user_login' => $user_login,
							'user_email' => $email,
							'first_name' => $first_name,
							'last_name'  => $last_name,
							'user_pass'  => '',
						);

						$insert = wp_insert_user( $prepare_user_data );

						if ( is_wp_error( $insert ) ) {
							\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( __( 'User registration failed', 'tutor-pro' ) ) );
							return;
						} else {
							$userdata = get_userdata( $insert );
							if ( is_a( $userdata, 'WP_User' ) ) {
								// Check if wanted to be a instructor.
								if ( $is_instructor ) {
									( new Instructor( false ) )->update_instructor_meta( $userdata->ID );
								} else {
									do_action( 'tutor_after_student_signup', $insert );
								}

								// Logged-in the user.
								self::logged_in( $userdata );
							} else {
								\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( $auth_failed_msg ) );
								return;
							}
						}
					}
				} else {
					if ( isset( $response_user_data['errors'] ) ) {
						if ( is_array( $response_user_data['errors'] ) ) {
							foreach ( $response_user_data['errors'] as $error ) {
								\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( $error['message'] ) );
							}
							return;
						} else {
							\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( __( 'Something went wrong! Please try again', 'tutor-pro' ) ) );
							return;
						}
					}
				}
			} else {
				\set_transient( Ajax::LOGIN_ERRORS_TRANSIENT_KEY, array( __( 'Something went wrong! Please try again', 'tutor-pro' ) ) );
				return;
			}

			delete_transient( 'twitter_login_is_instructor' );
			wp_safe_redirect( tutor_utils()->tutor_dashboard_url() );
		}
	}

	/**
	 * Logged user in
	 *
	 * @param \WP_User $userdata WP_User object.
	 *
	 * @return void
	 */
	private static function logged_in( \WP_User $userdata ) {
		wp_set_current_user( $userdata->ID, $userdata->user_login );
		wp_set_auth_cookie( $userdata->ID );

		apply_filters( 'authenticate', $userdata, $userdata->user_login, '' );
		do_action( 'wp_login', $userdata->user_login, $userdata );
	}
}
