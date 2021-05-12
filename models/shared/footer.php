<?php
/**
 * Footer model file.
 */

use DustPress\Model;
use TMS\Theme\Base\Settings;

/**
 * Footer class
 */
class Footer extends Model {

    public function logo() {
        return Settings::get_setting( 'footer_logo' );
    }

    public function contact_info() {
        $info = [
            'title'   => Settings::get_setting( 'contact_title' ),
            'address' => Settings::get_setting( 'address' ),
            'email'   => Settings::get_setting( 'email' ),
            'phone'   => Settings::get_setting( 'phone' ),
        ];

        $has_values = false;

        foreach ( $info as $val ) {
            if ( ! empty( $val ) ) {
                $has_values = true;
            }
        }

        return $has_values
            ? $info
            : null;
    }

    public function link_columns() {
        $columns = Settings::get_setting( 'link_columns' ) ?? null;

        if ( empty( $columns ) ) {
            return null;
        }

        foreach ( $columns as $key => $col ) {
            if ( empty( $col['link_column'] ) ) {
                unset( $columns[ $key ] );
                continue;
            }

            $columns[ $key ]['link_column'] = array_filter( $col['link_column'], function ( $link ) {
                return ! empty( $link['link'] );
            } );
        }

        return $columns;
    }

    public function privacy_links() {
        $links = Settings::get_setting( 'privacy_links' ) ?? null;

        if ( empty( $links ) ) {
            return null;
        }

        foreach ( $links as $key => $link ) {
            if ( empty( $link['privacy_link'] ) ) {
                unset( $links[ $key ] );
            }
        }

        return $links;
    }

    public function hero_credits() {
        return Settings::get_setting( 'hero_credits' ) ?? null;
    }

    public function copyright() {
        return sprintf(
            '&copy; %s %s',
            date( 'Y' ),
            Settings::get_setting( 'copyright' )
        );
    }
}
