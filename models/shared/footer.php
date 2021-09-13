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

    /**
     * Logo
     *
     * @return mixed
     */
    public function logo() {
        return Settings::get_setting( 'footer_logo' );
    }

    /**
     * Brand logo url
     *
     * @return string
     */
    public function brand_logo_url() : string {
        if ( DPT_PLL_ACTIVE && 'fi' !== pll_current_language() ) {
            return 'https://www.tampere.fi/en/';
        }

        return 'https://www.tampere.fi/';
    }

    /**
     * Get class for footer columns
     *
     * @return string
     */
    public function column_class() : string {
        $contact_info = $this->contact_info();
        $columns      = $this->link_columns();
        $count        = empty( $columns ) ? 0 : count( $columns );
        $count        = empty( $contact_info ) ? $count : ++ $count;

        return $count <= 3
            ? 'is-6 is-4-widescreen'
            : 'is-6 is-3-widescreen';
    }

    /**
     * Contact info
     *
     * @return array|null
     */
    public function contact_info() {
        $info = [
            'title'   => Settings::get_setting( 'contact_title' ),
            'address' => Settings::get_setting( 'address' ),
            'email'   => Settings::get_setting( 'email' ),
            'phone'   => Settings::get_setting( 'phone' ),
        ];

        return 0 !== count( array_filter( $info, fn( $val ) => ! empty( $val ) ) )
            ? $info
            : null;
    }

    /**
     * Get link columns
     *
     * @return mixed|null
     */
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

    /**
     * Get privacy links
     *
     * @return mixed|null
     */
    public function privacy_links() {
        $links = Settings::get_setting( 'privacy_links' ) ?? null;

        if ( empty( $links ) ) {
            return null;
        }

        return array_filter( $links, fn( $link ) => ! empty( $link['privacy_link'] ) );
    }

    /**
     * Get hero credits
     *
     * @return mixed|null
     */
    public function hero_credits() {
        return Settings::get_setting( 'hero_credits' ) ?? null;
    }

    /**
     * Get copyright
     *
     * @return string
     */
    public function copyright() : string {
        return sprintf(
            '&copy; %s %s',
            date( 'Y' ),
            Settings::get_setting( 'copyright' )
        );
    }

    /**
     * Return footer color classes.
     *
     * @return array
     */
    public function colors() : array {
        return apply_filters(
            'tms/theme/footer/colors',
            [
                'container'   => 'has-background-primary has-text-primary-invert',
                'back_to_top' => 'is-primary is-inverted',
                'link'        => 'has-text-primary-invert',
                'link_icon'   => 'is-primary-invert',
            ],
        );
    }
}
