<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Assets
 *
 * @package TMS\Theme\Base
 */
class Assets implements Interfaces\Controller {

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'wp_enqueue_scripts',
            \Closure::fromCallable( [ $this, 'enqueue_assets' ] ),
            100
        );

        \add_action(
            'admin_enqueue_scripts',
            \Closure::fromCallable( [ $this, 'admin_assets' ] ),
            100
        );

        \add_action(
            'wp_default_scripts',
            \Closure::fromCallable( [ $this, 'disable_jquery_migrate' ] )
        );

        \add_action(
            'wp_footer',
            \Closure::fromCallable( [ $this, 'include_svg_icons' ] )
        );

        \add_action(
            'enqueue_block_editor_assets',
            \Closure::fromCallable( [ $this, 'editor' ] )
        );

        \add_action(
            'admin_init',
            \Closure::fromCallable( [ $this, 'add_editor_styles' ] )
        );

        \add_filter(
            'tms/theme/icons',
            \Closure::fromCallable( [ $this, 'get_theme_icons' ] ),
            10,
            0
        );

        \add_filter(
            'tms/theme/acc_icons',
            [ __CLASS__, 'get_accessibility_icons' ],
            10,
            0
        );

        add_filter(
            'script_loader_tag',
            \Closure::fromCallable( [ $this, 'add_script_attributes' ] ),
            10,
            2
        );
    }

    /**
     * This adds custom styling to ACF Wysiwygs. Remove if nor needed.
     *
     * @return void
     */
    private function add_editor_styles() : void {
        \add_editor_style( 'custom-editor-styles.css' );
    }

    /**
     * Enqueue Theme Files.
     *
     * @param string $theme Theme file name without prefix 'theme_' or suffix '.js/.css'.
     */
    protected function enqueue_theme( $theme = 'tunnelma' ) : void {
        $css = apply_filters( 'tms/theme/theme_css_file', sprintf( 'theme_%s.css', $theme ) );
        $js  = apply_filters( 'tms/theme/theme_js_file', sprintf( 'theme_%s.js', $theme ) );

        \wp_enqueue_style(
            'theme-css',
            apply_filters(
                'tms/theme/theme_css_path',
                DPT_ASSET_URI . '/' . $css,
                $css
            ),
            [],
            apply_filters(
                'tms/theme/asset_mod_time',
                static::get_theme_asset_mod_time( $css ),
                $css
            ),
            'all'
        );

        \wp_enqueue_script(
            'theme-js',
            apply_filters(
                'tms/theme/theme_js_path',
                DPT_ASSET_URI . '/' . $js,
                $js
            ),
            [ 'jquery', 'vendor-js' ],
            apply_filters(
                'tms/theme/asset_mod_time',
                static::get_theme_asset_mod_time( $js ),
                $js
            ),
            true
        );
    }

    /**
     * Theme assets. These have automatic cache busting.
     */
    private function enqueue_assets() : void {
        \wp_enqueue_script(
            'vendor-js',
            apply_filters(
                'tms/theme/theme_js_path',
                DPT_ASSET_URI . '/vendor.js',
                'vendor.js'
            ),
            [ 'jquery' ],
            apply_filters(
                'tms/theme/asset_mod_time',
                static::get_theme_asset_mod_time( 'vendor.js' ),
                'vendor.js'
            ),
            true
        );

        $theme_default_color = apply_filters(
            'tms/theme/theme_default_color',
            DEFAULT_THEME_COLOR
        );

        $selected_theme = apply_filters(
            'tms/theme/theme_selected',
            Settings::get_setting( 'theme_color' ) ?? $theme_default_color
        );

        $this->enqueue_theme( $selected_theme );

        /**
         * Add localizations to window.s object.
         */
        \wp_localize_script( 'theme-js', 's', ( new \Strings() )->s() );

        \wp_localize_script( 'theme-js', 'themeData', [
            'assetsUri' => esc_url( get_template_directory_uri() ),
        ] );

        \wp_dequeue_style( 'wp-block-library' );

        \wp_enqueue_script( // phpcs:ignore
            'duet-date-picker-module',
            'https://cdn.jsdelivr.net/npm/@duetds/date-picker@1.4.0/dist/duet/duet.esm.js',
            [ 'jquery' ],
            null,
            false
        );

        \wp_enqueue_script( // phpcs:ignore
            'duet-date-picker-nomobule',
            'https://cdn.jsdelivr.net/npm/@duetds/date-picker@1.4.0/dist/duet/duet.js',
            [ 'jquery' ],
            null,
            false
        );
    }

    /**
     * Add attributes to enqueued script tags
     *
     * @param string $tag Script tag.
     * @param string $handle Script handle name.
     * @return string The script tag.
     */
    private function add_script_attributes( $tag, $handle ) : string {

        if ( $handle === 'duet-date-picker-module' ) {
            $tag = str_replace( '<script ', ' <script type="module" ', $tag );
            return $tag;
        }

        if ( $handle === 'duet-date-picker-nomobule' ) {
            $tag = str_replace( '<script ', ' <script nomodule ', $tag );
            return $tag;
        }
        return $tag;
    }

    /**
     * This adds assets (JS and CSS) to gutenberg in admin.
     *
     * @return void
     */
    private function editor() : void {
        $css_mod_time = static::get_theme_asset_mod_time( 'editor.css' );
        $js_mod_time  = static::get_theme_asset_mod_time( 'editor.js' );

        if ( file_exists( DPT_ASSET_CACHE_URI . '/editor.js' ) ) {
            \wp_enqueue_script(
                'editor-js',
                DPT_ASSET_URI . '/editor.js',
                [
                    'wp-i18n',
                    'wp-blocks',
                    'wp-dom-ready',
                    'wp-edit-post',
                ],
                $js_mod_time,
                true
            );
        }

        if ( file_exists( DPT_ASSET_CACHE_URI . '/editor.css' ) ) {
            \wp_enqueue_style(
                'editor-css',
                DPT_ASSET_URI . '/editor.css',
                [],
                $css_mod_time,
                'all'
            );
        }
    }

    /**
     * Admin assets.
     */
    private function admin_assets() : void {
        $css_mod_time = static::get_theme_asset_mod_time( 'admin.css' );
        $js_mod_time  = static::get_theme_asset_mod_time( 'admin.js' );

        \wp_enqueue_script(
            'admin-js',
            apply_filters(
                'tms/theme/admin_js_path',
                DPT_ASSET_URI . '/admin.js',
                'admin.js'
            ),
            [
                'jquery',
                'wp-data',
                'wp-core-data',
                'wp-editor',
            ],
            $js_mod_time,
            true
        );

        \wp_enqueue_style(
            'admin-css',
            DPT_ASSET_URI . '/admin.css',
            [],
            $css_mod_time,
            'all'
        );
    }

    /**
     * This function disables jQuery Migrate.
     *
     * @param \WP_Scripts $scripts The scripts object.
     *
     * @return void
     */
    private function disable_jquery_migrate( $scripts ) : void {
        if ( ! empty( $scripts->registered['jquery'] ) ) {
            $scripts->registered['jquery']->deps = array_diff(
                $scripts->registered['jquery']->deps,
                [ 'jquery-migrate' ]
            );
        }
    }

    /**
     * Add SVG definitions to footer.
     */
    private function include_svg_icons() : void {
        $svg_icons_path = \get_template_directory() . '/assets/dist/icons.svg';

        if ( file_exists( $svg_icons_path ) ) {
            include_once $svg_icons_path;
        }
    }

    /**
     * This enables cache busting for theme CSS and JS files by
     * returning a microtime timestamp for the given files.
     * If the file is not found for some reason, it uses the theme version.
     *
     * @param string $filename The file to check.
     *
     * @return int|string A microtime amount or the theme version.
     */
    protected static function get_theme_asset_mod_time( $filename = '' ) {
        return file_exists( DPT_ASSET_CACHE_URI . '/' . $filename )
            ? filemtime( DPT_ASSET_CACHE_URI . '/' . $filename )
            : DPT_THEME_VERSION;
    }

    /**
     * Get available icon choices.
     *
     * @return string[]
     */
    protected function get_theme_icons() {
        return [
            'icon-ambulanssi'      => 'Ambulanssi',
            'icon-auto'            => 'Auto',
            'icon-bussi'           => 'Bussi',
            'icon-chat'            => 'Chat',
            'icon-finlaysoninalue' => 'Finlaysonin alue',
            'icon-haulitorni'      => 'Haulitorni',
            'icon-idea'            => 'Idea',
            'icon-info'            => 'Info',
            'icon-jaakiekko'       => 'Jääkiekko',
            'icon-jarvi'           => 'Järvi',
            'icon-juna'            => 'Juna',
            'icon-kahvikuppi'      => 'Kahvikuppi',
            'icon-kalastus'        => 'Kalastus',
            'icon-kamera'          => 'Kamera',
            'icon-kannykka'        => 'Kännykkä',
            'icon-kasvu'           => 'Kasvu',
            'icon-kattaus'         => 'Kattaus',
            'icon-kaupunki'        => 'Kaupunki',
            'icon-kavely'          => 'Kävely',
            'icon-kello'           => 'Kello',
            'icon-kirja'           => 'Kirja',
            'icon-koira'           => 'Koira',
            'icon-koti'            => 'Koti',
            'icon-koulu'           => 'Koulu',
            'icon-laiva'           => 'Laiva',
            'icon-lapsi'           => 'Lapsi',
            'icon-latu'            => 'Latu',
            'icon-lehti'           => 'Lehti',
            'icon-leikkipuisto'    => 'Leikkipuisto',
            'icon-lentokone'       => 'Lentokone',
            'icon-lukko'           => 'Lukko',
            'icon-metso'           => 'Metso',
            'icon-mies'            => 'Mies',
            'icon-muistilista'     => 'Muistilista',
            'icon-musiikki'        => 'Musiikki',
            'icon-nainen'          => 'Nainen',
            'icon-nasinneula'      => 'Näsinneula',
            'icon-nuija'           => 'Nuija',
            'icon-nuotio'          => 'Nuotio',
            'icon-osaaminen'       => 'Osaaminen',
            'icon-osaaminen2'      => 'Osaaminen 2',
            'icon-paikka'          => 'Paikka',
            'icon-peukku'          => 'Peukku',
            'icon-puisto'          => 'Puisto',
            'icon-pyora'           => 'Pyorä',
            'icon-raatihuone'      => 'Raatihuone',
            'icon-raha'            => 'Raha',
            'icon-ratikka'         => 'Ratikka',
            'icon-ratinanstadion'  => 'Ratinanstadion',
            'icon-sairaala'        => 'Sairaala',
            'icon-sauna'           => 'Sauna',
            'icon-sieni'           => 'Sieni',
            'icon-sopimus'         => 'Sopimus',
            'icon-soutuvene'       => 'Soutuvene',
            'icon-sydan'           => 'Sydän',
            'icon-tammerkoski'     => 'Tammerkoski',
            'icon-teatteri'        => 'Teatteri',
            'icon-tehdas'          => 'Tehdas',
            'icon-tehtava'         => 'Tehtävä',
            'icon-teltta'          => 'Teltta',
            'icon-timantti'        => 'Timantti',
            'icon-tori'            => 'Tori',
            'icon-wifi'            => 'Wifi',
            'icon-alykas'          => 'Älykas',
        ];
    }

    /**
     * Get available accessibility icon choices.
     *
     * @return string[]
     */
    public static function get_accessibility_icons() {
        return [
            'aaniopastus'                        => __( 'Audio guide', 'tms-theme-base' ),
            'avustaja'                           => __( 'Assistant', 'tms-theme-base' ),
            'hissi'                              => __( 'Lift', 'tms-theme-base' ),
            'induktiosilmukka'                   => __( 'Induction loop', 'tms-theme-base' ),
            'info'                               => __( 'Information', 'tms-theme-base' ),
            'internet'                           => __( 'Internet', 'tms-theme-base' ),
            'isa'                                => __( 'Accessible entrance, Accessible toilet, Accessible parking', 'tms-theme-base' ),
            'kahvila'                            => __( 'Café', 'tms-theme-base' ),
            'kauppa'                             => __( 'Shop', 'tms-theme-base' ),
            'kokoustilaa'                        => __( 'Meeting room', 'tms-theme-base' ),
            'lainattavia-valineita'              => __( 'Assistive device lending', 'tms-theme-base' ),
            'lastenhoitotila'                    => __( 'Baby changing', 'tms-theme-base' ),
            'latauspiste'                        => __( 'Charging point', 'tms-theme-base' ),
            'lipunmyynti'                        => __( 'Ticket sales', 'tms-theme-base' ),
            'luiska'                             => __( 'Ramp', 'tms-theme-base' ),
            'nakovammaisia_helpottavat_palvelut' => __( 'Services for the visually impaired', 'tms-theme-base' ),
            'opaskoirat_sallittu'                => __( 'Service dogs allowed', 'tms-theme-base' ),
            'pistekirjoitus'                     => __( 'Braille', 'tms-theme-base' ),
            'porrashissi'                        => __( 'Stairlift', 'tms-theme-base' ),
            'portaat'                            => __( 'Staircase', 'tms-theme-base' ),
            'pyoratuolihissi'                    => __( 'Platform lift', 'tms-theme-base' ),
            'rollaattori'                        => __( 'Rollator, walker', 'tms-theme-base' ),
            'sahkomopon_sailytys'                => __( 'Electric mobility scooter storage', 'tms-theme-base' ),
            'sailytyslokerot'                    => __( 'Lockers', 'tms-theme-base' ),
            'suuri_teksti'                       => __( 'Large print', 'tms-theme-base' ),
            'vaatesailytys'                      => __( 'Cloakroom', 'tms-theme-base' ),
            'viitomakielinen_palvelu'            => __( 'Sign-language services', 'tms-theme-base' ),
            'wc_oikea'                           => __( 'Accessible toilet: one-sided access', 'tms-theme-base' ),
            'wc_vasen'                           => __( 'Accessible toilet: one-sided access', 'tms-theme-base' ),
            'wc'                                 => __( 'WC', 'tms-theme-base' ),
        ];
    }
}
