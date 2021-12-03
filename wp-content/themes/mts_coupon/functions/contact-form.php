<?php

/**
 * Class mtscontact
 *
 * AJAX Contact Form - mts_contact_form()
 */
if ( !class_exists('mtscontact') ) {
	class mtscontact {
		public $errors = array();
		public $userinput = array('name' => '', 'email' => '', 'message' => '');
		public $success = false;

		/**
		 * Set up action hooks.
		 */
		public function __construct() {
			add_action('wp_ajax_mtscontact', array($this, 'ajax_mtscontact'));
			add_action('wp_ajax_nopriv_mtscontact', array($this, 'ajax_mtscontact'));
			add_action('init', array($this, 'init'));
			add_action('wp_enqueue_scripts', array($this, 'register_scripts'));
		}

		/**
		 * Send the form via AJAX request and send the `$errors` object.
		 */
		public function ajax_mtscontact() {
			if ($this->validate()) {
				if ($this->send_mail()) {
					echo json_encode('success');
					wp_create_nonce( "mtscontact" ); // purge used nonce
				} else {
					// wp_mail() unable to send
					$this->errors['sendmail'] = __('An error occurred. Please contact site administrator.', 'coupon' );
					echo json_encode($this->errors);
				}
			} else {
				echo json_encode($this->errors);
			}
			die();
		}

		/**
		 * Send the form and set the success flag or populate the `$errors` object when it fails.
		 */
		public function init() {
			// No-js fallback
			if ( !defined( 'DOING_AJAX' ) || !DOING_AJAX ) {
				if (!empty($_POST['action']) && $_POST['action'] == 'mtscontact') {
					if ($this->validate()) {
						if (!$this->send_mail()) {
							$this->errors['sendmail'] = __('An error occurred. Please contact site administrator.', 'coupon' );
						} else {
							$this->success = true;
						}
					}
				}
			}
		}
		public function register_scripts() {
			wp_register_script('mtscontact', get_template_directory_uri() . '/js/contact.js', true);
			wp_localize_script('mtscontact', 'mtscontact', array('ajaxurl' => admin_url('admin-ajax.php')));
		}

		/**
		 * Validate the submitted form.
		 *
		 * @return bool
		 */
		private function validate() {
			// check nonce
			if (!check_ajax_referer( 'mtscontact', 'mtscontact_nonce', false )) {
				$this->errors['nonce'] = __('Please try again.', 'coupon' );
			}
			
			// check honeypot // must be empty
			if (!empty($_POST['mtscontact_captcha'])) {
				$this->errors['captcha'] = __('Please try again.', 'coupon' );
			}
			
			// name field
			$name = trim(str_replace(array("\n", "\r", "<", ">"), '', strip_tags($_POST['mtscontact_name'])));
			if (empty($name)) {
				$this->errors['name'] = __('Please enter your name.', 'coupon' );
			}
			
			// email field
			$useremail = trim($_POST['mtscontact_email']);
			if (!is_email($useremail)) {
				$this->errors['email'] = __('Please enter a valid email address.', 'coupon' );
			}
			
			// message field
			$message = strip_tags($_POST['mtscontact_message']);
			if (empty($message)) {
				$this->errors['message'] = __('Please enter a message.', 'coupon' );
			}
			
			// store fields for no-js
			$this->userinput = array('name' => $name, 'email' => $useremail, 'message' => $message);
			
			return empty($this->errors);
		}

		/**
		 * Send the mail.
		 *
		 * @return bool
		 */
		private function send_mail() {
			$email_to = get_option('admin_email');
			$email_subject = __('Contact Form Message from', 'coupon' ).' '.get_bloginfo('name');
			$email_message = __('Name:', 'coupon' ).' '.$this->userinput['name']."\n\n".
							 __('Email:', 'coupon' ).' '.$this->userinput['email']."\n\n".
							 __('Message:', 'coupon' ).' '.$this->userinput['message'];
			return wp_mail($email_to, $email_subject, $email_message);
		}

		/**
		 * Get the HTML form.
		 *
		 * @return string
		 */
		public function get_form() {
			wp_enqueue_script('mtscontact');
			
			$return = '';
			if (!$this->success) {
				$return .= '<form method="post" action="" id="mtscontact_form" class="contact-form">
				<input type="text" name="mtscontact_captcha" value="" style="display: none;" />
				<input type="hidden" name="mtscontact_nonce" value="'.wp_create_nonce( "mtscontact" ).'" />
				<input type="hidden" name="action" value="mtscontact" />
				
				<label for="mtscontact_name">'.__('Name', 'coupon' ).'</label>
				<input type="text" name="mtscontact_name" value="'.esc_attr($this->userinput['name']).'" id="mtscontact_name" />
				
				<label for="mtscontact_email">'.__('Email', 'coupon' ).'</label>
				<input type="text" name="mtscontact_email" value="'.esc_attr($this->userinput['email']).'" id="mtscontact_email" />
				
				<label for="mtscontact_message">'.__('Message', 'coupon' ).'</label>
				<textarea name="mtscontact_message" id="mtscontact_message">'.esc_textarea($this->userinput['message']).'</textarea>

				<div class="mtscontact_consent">
					<input type="checkbox" name="mtscontact_consent" value="yes" id="mtscontact_consent" />
					<label for="mtscontact_consent">'.__('Please tick to agree to be contacted.', 'coupon').'</label>
				</div>
				
				<input type="submit" value="'.__('Send', 'coupon' ).'" id="mtscontact_submit" />
			</form>';
			}
			$return .= '<div id="mtscontact_success"'.($this->success ? '' : ' style="display: none;"').'>'.__('Your message has been sent.', 'coupon' ).'</div>';
			return $return;
		}

		/**
		 * Get the errors.
		 *
		 * @return string
		 */
		public function get_errors() {
			$html = '';
			foreach ($this->errors as $error) {
				$html .= '<div class="mtscontact_error">'.$error.'</div>';
			}
			return $html;
		}
	}
}
$mtscontact = new mtscontact;

/**
 * Display the contact form.
 */
function mts_contact_form() {
	echo mts_get_contact_form();
}

/**
 * Get the contact form.
 * This could be used for shortcode support.
 *
 * @return string
 */
if ( !function_exists('mts_get_contact_form') ) {
	function mts_get_contact_form() {
		global $mtscontact;
		return $mtscontact->get_errors() . $mtscontact->get_form();
	}
}
