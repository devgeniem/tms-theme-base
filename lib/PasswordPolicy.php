<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use function add_action;
use function add_filter;
use function is_wp_error;

/**
 * Password Policy
 */
class PasswordPolicy implements Interfaces\Controller {

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'user_profile_update_errors', [ $this, 'validate_profile_update' ], 0, 3 );
        add_action( 'validate_password_reset', [ $this, 'validate_strong_password' ], 0, 2 );
        add_action( 'resetpass_form', [ $this, 'validate_resetpass_form' ], 10 );
        add_filter( 'random_password', [ $this, 'generate_random_password' ], 10, 4 );
    }

    /**
     * Get $_POST data with aggressive filtering.
     *
     * @param string $key INPUT_POST Key.
     *
     * @return string|null
     */
    private function get_post_var( string $key ) {
        $input = trim( sanitize_text_field( filter_input( INPUT_POST, $key ) ) );

        return empty( $input ) ? null : $input;
    }

    /**
     * Check user profile update and throw an error if the password isn't strong.
     *
     * @param \WP_Error          $errors    WP_Error object.
     * @param bool               $update    Whether this is a user update.
     * @param \WP_Error|\WP_User $user_data User object.
     *
     * @return \WP_Error
     */
    public function validate_profile_update( $errors, $update, $user_data ) {
        unset( $update );

        return $this->validate_strong_password( $errors, $user_data );
    }

    /**
     * Check password reset form and throw an error if the password isn't strong.
     *
     * @param \WP_User $user_data User object of the user whose password is being reset.
     *
     * @return \WP_Error
     */
    public function validate_resetpass_form( $user_data ) {
        return $this->validate_strong_password( false, $user_data );
    }

    /**
     * Functionality used by both user profile and reset password validation.
     *
     * @param \WP_Error|bool     $errors    WP_Error object, or false.
     * @param \WP_User|\WP_Error $user_data WP_User object or WP_Error object.
     *
     * @return \WP_Error
     */
    public function validate_strong_password( $errors, $user_data ) {
        $password = $this->get_post_var( 'pass1' ) ?? '';
        $username = $this->get_post_var( 'user_login' ) ?? $user_data->user_login;

        // No password set / already got a password error
        if ( empty( $password ) || ( is_wp_error( $errors ) && $errors->get_error_data( 'pass' ) ) ) {
            return $errors;
        }

        $password_ok = $this->check_password_strength( $password, $username );

        // Error
        if ( ! $password_ok && is_wp_error( $errors ) ) {
            $errors->add(
                'pass',
                apply_filters(
                    'error_message',
                    __( 'Salasanan tulee olla vähintään 12 merkkiä pitkä, maksimissaan 128 merkkiä.', 'tms-theme-base' )
                )
            );
        }

        // Password can't be one of breached ones.
        if ( Security::check_password_pwnd_status( $password ) ) {
            $errors->add(
                'pass',
                apply_filters(
                    'error_message',
                    __( 'Valittu salasana on vaarantunut. Valitse toinen salasana.', 'tms-theme-base' )
                )
            );
        }
        else {
            Security::update_compromise_notification( $user_data->ID, false );
        }

        // The errors array will be empty, which makes the form
        return $errors;
    }

    /**
     * Check password requirements:
     * - Length min. 12 characters
     * - Length max. 128 characters
     * - Can't be identical with the username
     *
     * @param string $password User password.
     * @param string $username User username.
     *
     * @return bool
     */
    public function check_password_strength( $password, $username ) {
        // Password length at least 12 characters
        $password_length = mb_strlen( $password );
        if ( $password_length < 12 || $password_length > 128 ) {
            return false;
        }

        // Password can't be identical to the username
        if ( strtolower( $password ) === strtolower( $username ) ) {
            return false;
        }

        return true;
    }

    /**
     * Overwrite the default WordPress password generation making sure
     * it adheres to the requirements set in this class.
     *
     * @param string $old_password        The generated password.
     * @param int    $length              The length of password to generate.
     * @param bool   $special_chars       Whether to include standard special characters.
     * @param bool   $extra_special_chars Whether to include other special characters.
     *
     * @return string
     */
    public function generate_random_password( $old_password, $length, $special_chars, $extra_special_chars = false ) {
        unset( $old_password );

        // Define character libraries
        $sets   = [];
        $sets[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sets[] = 'abcdefghijklmnopqrstuvwxyz';
        $sets[] = '0123456789';

        // Passwords are generated for password reset URLs also, which don't support special characters
        if ( $special_chars ) {
            $sets[] = '!@#$%^&*()';
        }

        // Extra special umlauts.
        if ( $extra_special_chars ) {
            $sets[] = 'ÄäÖöÅå';
        }

        // Reset the OG password
        $password = '';

        // Append a character from each set, making sure to meet the initial requirements
        foreach ( $sets as $set ) {
            $password .= $set[ array_rand( str_split( $set ) ) ];
        }

        // Use all characters to fill up to the required length
        $password_length = 0;

        while ( $password_length < $length ) {
            $random_set = $sets[ array_rand( $sets ) ];

            $password .= $random_set[ array_rand( str_split( $random_set ) ) ];

            $password_length = mb_strlen( $password );
        }

        // Shuffle the characters and return a shiny new password
        return str_shuffle( $password );
    }
}
