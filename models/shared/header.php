<?php
/**
 * Header model
 */

use DustPress\Model;
use TMS\Theme\Base\Settings;
use TMS\Theme\Base\Traits;

/**
 * Header class
 */
class Header extends Model {

    use Traits\Breadcrumbs;

    /**
     * Get logo
     *
     * @return mixed|null
     */
    public function logo() {
        return Settings::get_setting( 'logo' ) ?? null;
    }

    /**
     * Get home url
     *
     * @return mixed|string
     */
    public function home_url() {
        return DPT_PLL_ACTIVE
            ? pll_home_url()
            : home_url();
    }

    /**
     * Get tagline
     *
     * @return mixed|null
     */
    public function tagline() {
        return Settings::get_setting( 'tagline' ) ?? null;
    }

    /**
     * Get brand logo
     *
     * @return mixed|null
     */
    public function brand_logo() {
        return Settings::get_setting( 'brand_logo' ) ?? null;
    }

    /**
     * Get language nav
     *
     * @return array|null
     */
    public function language_nav() : ?array {
        if ( ! DPT_PLL_ACTIVE ) {
            return null;
        }

        $lang_nav_display = Settings::get_setting( 'lang_nav_display' );

        if ( 'hide' === $lang_nav_display ) {
            return null;
        }

        $args = [
            'raw'        => 1,
            'hide_empty' => 0,
        ];

        $languages = pll_the_languages( $args );
        $lang_data = [ 'all' => $languages ];

        foreach ( $languages as $lang ) {
            if ( ! empty( $lang['current_lang'] ) ) {
                $lang_data['current'] = $lang;
            }
            else {
                $lang_data['others'][] = $lang;
            }
        }

        return [
            'partial' => 'dropdown' === $lang_nav_display
                ? 'ui/menu/language-nav-dropdown'
                : 'ui/menu/language-nav',
            'links'   => $lang_data,
        ];
    }

    /**
     * Hide main navigation
     *
     * @return false|mixed
     */
    public function hide_main_nav() {
        return apply_filters( 'tms/theme/hide_main_nav', Settings::get_setting( 'hide_main_nav' ) ) ?? false;
    }

    /**
     * Get limit nav depth status
     *
     * @return mixed
     */
    public function limit_nav_depth() {
        return Settings::get_setting( 'limit_nav_depth' );
    }

    /**
     * Is language nav horizontal
     *
     * @return bool
     */
    public function lang_nav_horizontal() : bool {
        $lang_nav_display = Settings::get_setting( 'lang_nav_display' );

        if ( 'hide' === $lang_nav_display ) {
            return false;
        }

        return 'dropdown' !== $lang_nav_display;
    }

    /**
     * Get search action
     *
     * @return string|void
     */
    public function search_action() {
        if ( ! DPT_PLL_ACTIVE ) {
            return '/';
        }

        $default_lang = pll_default_language( 'slug' );
        $current_lang = pll_current_language( 'slug' );

        return sprintf(
            '/%s',
            $current_lang === $default_lang
                ? ''
                : trailingslashit( $current_lang )
        );
    }

    /**
     * Breadcrumbs
     *
     * @return array
     */
    public function breadcrumbs() : array {
        $current_object = get_queried_object();

        if ( $current_object === null || empty( $current_object ) ) {
            return [];
        }

        $breadcrumbs         = [];
        $home_url            = trailingslashit( get_home_url() );
        $current_id          = 0;
        $current_type        = '';
        $breadcrumbs['home'] = $this->get_home_link();

        if ( is_a( $current_object, 'WP_Post' ) ) {
            $current_id   = (int) $current_object->ID;
            $current_type = (string) $current_object->post_type;
            $breadcrumbs  = $this->get_ancestors( $current_id, $current_type, $breadcrumbs );
        }
        elseif ( is_a( $current_object, 'WP_Term' ) ) {
            $current_type = 'tax-archive';
        }
        elseif ( is_post_type_archive() ) {
            $current_type = 'post-type-archive';
        }

        $breadcrumbs = $this->prepare_by_type( $current_type, $current_id, $home_url, $breadcrumbs );

        return (array) apply_filters(
            'tms/theme/breadcrumbs/' . $current_type,
            $this->format_breadcrumbs( $breadcrumbs ),
            $breadcrumbs,
            $current_object
        );
    }

    /**
     * Display helper function.
     *
     * If subpage / view wants to display the breadcrumbs bar somewhere else,
     * this method can be overridden with a filter.
     *
     * @return bool
     */
    public function show_breadcrumbs_in_header() {
        $default = true;
        $status  = apply_filters(
            'tms/theme/breadcrumbs/show_breadcrumbs_in_header',
            $default,
            get_queried_object()
        );

        return is_bool( $status ) ? $status : (bool) $status;
    }

    /**
     * Exception notice.
     *
     * @return array|bool
     */
    public function exception_notice() {
        $text         = Settings::get_setting( 'exception_text' );
        $exception_id = md5( $text );

        if ( empty( $text ) || isset( $_COOKIE[ $exception_id ] ) ) {
            return false;
        }

        return [
            'message' => $text,
            'id'      => $exception_id,
        ];
    }

    /**
     * Hide search
     *
     * @return mixed
     */
    public function hide_search() {
        return Settings::get_setting( 'hide_search' );
    }

    /**
     * Get custom scripts from Site Settings.
     *
     * @return false|mixed
     */
    public function head_custom_scripts() {
        $head_scripts = Settings::get_setting( 'header_scripts' );

        return ( ! empty( $head_scripts ) ) ? $head_scripts : false;
    }

    /**
     * Check if navigation menu exists
     *
     * @return bool
     */
    public function has_nav_menu() : bool {
        return has_nav_menu( 'primary' ) || has_nav_menu( 'secondary' );
    }
}
