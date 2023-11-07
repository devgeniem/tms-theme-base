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
    use Traits\Links;

    /**
     * Hooks.
     */
    public function hooks() {
        add_filter(
            'dustpress/menu/item/classes',
            Closure::fromCallable( [ $this, 'menu_item_classes' ] ),
            10,
            2
        );
    }

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
     * Get site locale
     *
     * @return string
     */
    public function site_locale() : string {
        $locale = DPT_PLL_ACTIVE ? pll_current_language( 'locale' ) : get_locale();
        $locale = $locale === 'fi' ? 'fi-FI' : $locale;

        return $locale;
    }

    /**
     * Hide main navigation
     *
     * @return false|mixed
     */
    public function hide_main_nav() {
        $hide_main_nav = Settings::get_setting( 'hide_main_nav' ) ?? false;

        return apply_filters(
            'tms/theme/hide_main_nav',
            $hide_main_nav
        );
    }

    /**
     * Hide secondary navigation
     *
     * @return false|mixed
     */
    public function hide_secondary_nav() {
        $hide_secondary_nav = Settings::get_setting( 'hide_secondary_nav' ) ?? false;

        return apply_filters(
            'tms/theme/hide_secondary_nav',
            $hide_secondary_nav
        );
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
     * @return string
     */
    public function search_action() {
        return $this->get_search_action();
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

        $cta_link = Settings::get_setting( 'exception_cta_link' );

        if ( empty( $cta_link ) ) {
            return [
                'message' => $text,
                'id'      => $exception_id,
            ];
        }

        return apply_filters(
            'tms/theme/exception_notice/data',
            [
                'message' => $text,
                'id'      => $exception_id,
                'cta_btn' => (object) [
                    'title' => ( new \Strings() )->s()['common']['read_more'],
                    'url'   => $cta_link,
                ],
            ],
        );
    }

    /**
     * Get custom head scripts from Site Settings.
     *
     * @return false|mixed
     */
    public function head_custom_scripts() {
        $head_scripts = Settings::get_setting( 'header_scripts' );

        return ( ! empty( $head_scripts ) ) ? $head_scripts : false;
    }

    /**
     * Get custom body scripts from Site Settings.
     *
     * @return false|mixed
     */
    public function body_custom_scripts() {
        $body_scripts = Settings::get_setting( 'body_scripts' );

        return ( ! empty( $body_scripts ) ) ? $body_scripts : false;
    }

    /**
     * Maybe show chat.
     *
     * @return string|null
     */
    public function chat() {
        $chat_script = Settings::get_setting( 'chat_script' );
        $chat_pages  = Settings::get_setting( 'chat_pages' );

        if ( empty( $chat_script ) && empty( $chat_pages ) ) {
            return null;
        }

        $current_id     = get_the_ID();
        $post_ancestors = get_post_ancestors( $current_id );

        foreach ( $chat_pages as $page ) {
            // return chat script if chat_page equals to current id
            if ( $page['chat_page'] === $current_id ) {
                return $chat_script;
            }

            // return chat if chat is set to be visible on child pages
            // and current chat page is one of the ancestors
            if (
                ! empty( $page['show_on_child'] )
                && in_array( $page['chat_page'] ?? 0, $post_ancestors, true )
                ) {
                return $chat_script;
            }
        }

        return null;

    }

    /**
     * Hide search
     *
     * @return mixed
     */
    public function hide_search() {
        $hide_search = Settings::get_setting( 'hide_search' ) ?? true;

        return apply_filters(
            'tms/theme/hide_header_search',
            $hide_search
        );
    }

    /**
     * Check if navigation menu exists
     *
     * @return bool
     */
    public function has_nav_menu() : bool {
        return has_nav_menu( 'primary' ) || has_nav_menu( 'secondary' );
    }

    /**
     * Hide flyout primary menu.
     *
     * @return bool
     */
    public function hide_flyout_primary() : bool {
        return apply_filters( 'tms/theme/hide_flyout_primary', false );
    }

    /**
     * Hide flyout secondary menu.
     *
     * @return bool
     */
    public function hide_flyout_secondary() : bool {
        return apply_filters( 'tms/theme/hide_flyout_secondary', false );
    }

    /**
     * Setup item classes.
     *
     * @param array  $classes Classes.
     * @param object $item    Menu item object.
     *
     * @return array Classes.
     */
    public function menu_item_classes( $classes, $item ) : array {
        if ( apply_filters( 'tms/theme/nav_parent_link_is_trigger_only', false ) ) {
            $item_is_top_level_parent = '0' === $item->menu_item_parent && ! empty( $item->sub_menu );

            if ( $item_is_top_level_parent ) {
                $classes[] = 'navbar-item--trigger-only';
            }
        }

        $current_page = \get_queried_object();
        if ( ! empty( $current_page->ID ) && (int) $item->object_id === $current_page->ID ) {
            $classes['is_current'] = 'is-current';
        }

        return $classes;
    }

    /**
     * Return header color classes.
     *
     * @return array
     */
    public function colors() : array {
        return apply_filters(
            'tms/theme/header/colors',
            [
                'search_button'          => 'is-primary',
                'search_popup_container' => 'has-background-secondary has-text-secondary-invert',
                'nav'                    => [
                    'container' => 'has-background-primary',
                ],
                'fly_out_nav'            => [
                    'inner'            => 'has-background-primary has-text-secondary-invert',
                    'close_menu'       => 'is-primary-invert',
                    'search_title'     => 'has-text-primary-invert',
                    'search_button'    => 'is-primary is-inverted',
                    'brand_icon_color' => 'is-primary-invert',
                ],
                'lang_nav'               => [
                    'dropdown_toggle' => 'is-primary is-outlined',
                    'link'            => 'has-border-radius-small',
                    'link__default'   => 'has-text-accent',
                    'link__active'    => 'has-background-primary has-text-primary-invert',
                ],
            ]
        );
    }

    /**
     * Header strings
     *
     * @return array
     */
    public function strings() {
        return ( new Strings() )->s()['header'];
    }
}
