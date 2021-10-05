<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class SiteController
 *
 * @package TMS\Theme\Base
 */
class SiteController implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_action(
            'wp_initialize_site',
            \Closure::fromCallable( [ $this, 'initialize_site' ] ),
            999,
            1
        );
    }

    /**
     * Initialize the newly created site.
     *
     * @param \WP_Site $new_site Site object.
     *
     * @return void
     */
    protected function initialize_site( \WP_Site $new_site ) : void {
        $blog_id = $new_site->blog_id ?? null;

        if ( empty( $blog_id ) ) {
            return;
        }

        // Switch to the new site
        switch_to_blog( $blog_id );

        // Force switch to locale as it's not handled automatically
        $locale = get_blog_option( $blog_id, 'WPLANG' );
        $locale = ! empty( $locale ) ? $locale : 'en_US';
        switch_to_locale( $locale );

        $this->init_front_page();
        $this->init_page_for_posts();
        $this->init_main_menu();
        $this->init_site_settings();
        $this->activate_plugins();

        restore_current_blog();
    }

    /**
     * Set the site's first and only page's template.
     *
     * @return void
     *
     * @throws \Exception If no pages are found.
     */
    protected function init_front_page() : void {
        try {
            $page_id = get_pages()[0]->ID ?? null;

            if ( empty( $page_id ) ) {
                throw new \Exception(
                    'Could not initialize the front page.'
                );
            }

            $template_set = update_post_meta( $page_id, '_wp_page_template', 'models/page-front-page.php' );

            if ( ! $template_set ) {
                throw new \Exception(
                    'Could not set the front page template.'
                );
            }
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Create a page for posts.
     *
     * @return void
     *
     * @throws \Exception If failed to create a new page or update the page_for_posts option.
     */
    protected function init_page_for_posts() : void {
        try {
            $page_id = wp_insert_post( [
                'post_type'   => PostType\Page::SLUG,
                'post_title'  => __( 'News', 'tms-theme-base' ),
                'post_status' => 'publish',
            ], true );

            if ( is_wp_error( $page_id ) ) {
                throw new \Exception( $page_id->get_error_message() );
            }

            $page_for_posts_set = update_option( 'page_for_posts', $page_id );

            if ( ! $page_for_posts_set ) {
                throw new \Exception(
                    'Could not set the page_for_posts option.'
                );
            }
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Create the primary navigation menu.
     *
     * @return void
     *
     * @throws \Exception If failed to create the menu or set its location.
     */
    protected function init_main_menu() : void {
        try {
            $menu_id = wp_create_nav_menu(
                __( 'Main menu', 'tms-theme-base' )
            );

            if ( is_wp_error( $menu_id ) ) {
                throw new \Exception( $menu_id->get_error_message() );
            }

            // Set the menu location
            $locations            = get_theme_mod( 'nav_menu_locations' );
            $locations['primary'] = $menu_id;
            $menu_location_set    = set_theme_mod( 'nav_menu_locations', $locations );

            if ( ! $menu_location_set ) {
                throw new \Exception(
                    'Could not set the primary menu location.'
                );
            }
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Create a site settings post and initialize settings.
     *
     * @return void
     *
     * @throws \Exception If failed to create the post.
     */
    protected function init_site_settings() : void {
        try {
            $settings_id = wp_insert_post( [
                'post_type'   => PostType\Settings::SLUG,
                'post_title'  => __( 'Site settings', 'tms-theme-base' ),
                'post_status' => 'publish',
            ], true );

            if ( is_wp_error( $settings_id ) ) {
                throw new \Exception( $settings_id->get_error_message() );
            }

            // Set default content for the 404 page
            $title     = _x( 'Page not found', 'theme-frontend', 'tms-theme-base' );
            $title_set = update_field( '404_title', $title, $settings_id );

            $description = sprintf(
                '<p>%1$s</p><p>%2$s<br />%3$s<br />%4$s</p>',
                // @codingStandardsIgnoreStart
                _x( 'Sorry, the page you requested was not found. Please check that you spelled the address correctly. Or did you follow a link?', 'theme-frontend', 'tms-theme-base' ),
                _x( 'The page might not exist anymore or its name has changed. You might want to try to find the page you were looking for by', 'theme-frontend', 'tms-theme-base' ),
                _x( '– returning to the front page of the site', 'theme-frontend', 'tms-theme-base' ),
                _x( '– removing all characters after the last slash (/) in the address and try the shorter version of the address.', 'theme-frontend', 'tms-theme-base' )
                // @codingStandardsIgnoreEnd
            );
            $description_set = update_field( '404_description', $description, $settings_id );

            // Force the aligment setting, setting a default value on field registration does not suffice
            update_field( '404_alignment', 'has-text-left', $settings_id );

            if ( ! $title_set || ! $description_set ) {
                throw new \Exception(
                    'Could not set the 404 page content.'
                );
            }
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * Activate plugins.
     *
     * @return void
     *
     * @throws \Exception If failed to activate plugins.
     */
    protected function activate_plugins() : void {
        try {
            $plugin_valid = activate_plugin( 'wp-force-login/wp-force-login.php', '', false, true );

            if ( is_wp_error( $plugin_valid ) ) {
                throw new \Exception( $plugin_valid->get_error_message() );
            }
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
