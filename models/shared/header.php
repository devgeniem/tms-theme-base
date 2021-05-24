<?php
/**
 * Header model
 */

use DustPress\Model;
use TMS\Theme\Base\Settings;

/**
 * Header class
 */
class Header extends Model {

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
            : $this->home_url();
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
        return Settings::get_setting( 'hide_main_nav' ) ?? false;
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
}
