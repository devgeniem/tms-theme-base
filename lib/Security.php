<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Security.
 */
class Security implements Interfaces\Controller {

    /**
     * Check Password -meta key.
     *
     * @var string
     */
    private string $check_password_meta_key = '_haveibeenpwned';

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_filter( 'check_password', [ $this, 'check_password' ], 10, 4 );
        add_action( 'all_admin_notices', [ $this, 'check_password_notice' ], 10 );
        add_filter( 'rest_endpoints', [ $this, 'disable_user_endpoint' ] );
    }

    /**
     * Check if password can be found from list of compromised passwords.
     *
     * @see https://haveibeenpwned.com/API/v3#PwnedPasswords
     *
     * @param string $password Password to hash and check part of it.
     *
     * @return bool
     */
    public static function check_password_pwnd_status( string $password ) {
        $password_hash = utf8_encode( strtoupper( sha1( $password ) ) );
        $k_anon        = substr( $password_hash, 0, 5 );

        $service_url = 'https://api.pwnedpasswords.com/range/' . $k_anon;
        $response    = wp_remote_get( $service_url );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        return strpos( $response['body'], substr( $password_hash, 5 ) );
    }

    /**
     * Add or remove the compromise notification.
     *
     * @param int    $user_id     WordPress User ID.
     * @param bool   $compromised Was the password compromised? Defaults to true.
     * @param string $meta_key    Meta key to save the data into.
     */
    public static function update_compromise_notification(
        $user_id,
        $compromised = true,
        $meta_key = '_haveibeenpwned'
    ) {
        if ( $compromised ) {
            update_user_meta( $user_id, $meta_key, time() );
        }
        else {
            delete_user_meta( $user_id, $meta_key );
        }
    }

    /**
     * Check password against HaveIBeenPwned.com database.
     *
     * @param bool       $check    Password is users real password.
     * @param string     $password Plaintext password.
     * @param string     $hash     Hash of password.
     * @param int|string $user_id  ID of user to whom password belongs.
     *
     * @return bool
     */
    public function check_password( $check, $password, $hash, $user_id ) {
        // Bail out early on false password
        if ( ! $check ) {
            return $check;
        }

        unset( $hash );

        $pwned = self::check_password_pwnd_status( $password );

        self::update_compromise_notification( $user_id, $pwned, $this->check_password_meta_key );

        return $check;
    }

    /**
     * If User has been flagged as having compromised password,
     * show warning in every /wp-admin/ page.
     */
    public function check_password_notice() {
        $flagged = get_user_meta(
            get_current_user_id(),
            $this->check_password_meta_key,
            true
        );

        if ( $flagged ) {
            $warning = sprintf(
                '<div class="error"><h2><strong>%s:</strong> %s %s</h2></div>',
                __( 'WARNING', 'tms-theme-base' ),
                __( 'Your password has been compromised.', 'tms-theme-base' ),
                __( 'Please change your password <strong>as soon as possible</strong>.', 'tms-theme-base' )
            );

            echo wp_kses( $warning, 'post' );
        }
    }

    /**
     * Disable user endpoint.
     *
     * @param array $endpoints REST API endpoints.
     *
     * @return array
     */
    public function disable_user_endpoint( $endpoints ) {
        if ( isset( $endpoints['/wp/v2/users'] ) ) {
            unset( $endpoints['/wp/v2/users'] );
        }

        if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
            unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
        }

        return $endpoints;
    }
}
