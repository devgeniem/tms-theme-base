<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\PostType;

use TMS\Theme\Base\Interfaces\PostType;

/**
 * Settings
 *
 * The settings post type is used to create translatable site settings.
 *
 * There's only one post created in this post type and it can be removed only
 * by super admin. Others can modify, so the settings are updated.
 *
 * @package TMS\Theme\Base\PostType
 */
class BlogAuthor implements PostType {

    /**
     * This defines the slug of this post type.
     */
    public const SLUG = 'blog-author';

    /**
     * This defines what is shown in the url. This can
     * be different than the slug which is used to register the post type.
     *
     * @var string
     */
    private $url_slug = '';

    /**
     * Define the CPT description
     *
     * @var string
     */
    private $description = '';

    /**
     * This is used to position the post type menu in admin.
     *
     * @var int
     */
    private $menu_order = 5;

    /**
     * This defines the CPT icon.
     *
     * @var string
     */
    private $icon = 'dashicons-book-alt';

    /**
     * Constructor
     */
    public function __construct() {
        $this->url_slug    = _x( 'blog-author', 'theme CPT slugs', 'tms-theme-base' );
        $this->description = _x( 'Blog authors', 'theme CPT', 'tms-theme-base' );
    }

    /**
     * Add hooks and filters from this controller
     *
     * @return void
     */
    public function hooks() : void {
        add_action( 'init', \Closure::fromCallable( [ $this, 'register' ] ), 15 );
    }

    /**
     * Get post type slug
     *
     * @return string
     */
    public function get_post_type() : string {
        return static::SLUG;
    }

    /**
     * This registers the post type.
     *
     * @return void
     */
    private function register() {
        $labels = [
            'name'                  => 'Blogin kirjoittajat',
            'singular_name'         => 'Blogin kirjoittaja',
            'menu_name'             => 'Kirjoittajat',
            'name_admin_bar'        => 'Kirjoittajat',
            'archives'              => 'Arkistot',
            'attributes'            => 'Ominaisuudet',
            'parent_item_colon'     => 'Vanhempi:',
            'all_items'             => 'Kaikki',
            'add_new_item'          => 'Lisää uusi',
            'add_new'               => 'Lisää uusi',
            'new_item'              => 'Uusi',
            'edit_item'             => 'Muokkaa',
            'update_item'           => 'Päivitä',
            'view_item'             => 'Näytä',
            'view_items'            => 'Näytä kaikki',
            'search_items'          => 'Etsi',
            'not_found'             => 'Ei löytynyt',
            'not_found_in_trash'    => 'Ei löytynyt roskakorista',
            'featured_image'        => 'Kuva',
            'set_featured_image'    => 'Aseta kuva',
            'remove_featured_image' => 'Poista kuva',
            'use_featured_image'    => 'Käytä kuvana',
            'insert_into_item'      => 'Aseta julkaisuun',
            'uploaded_to_this_item' => 'Lisätty tähän julkaisuun',
            'items_list'            => 'Listaus',
            'items_list_navigation' => 'Listauksen navigaatio',
            'filter_items_list'     => 'Suodata listaa',
        ];

        $rewrite = [
            'slug'       => static::SLUG,
            'with_front' => false,
            'pages'      => false,
            'feeds'      => false,
        ];

        $args = [
            'label'         => $labels['name'],
            'description'   => '',
            'labels'        => $labels,
            'supports'      => [
                'title',
                'thumbnail',
                'editor',
            ],
            'hierarchical'  => false,
            'public'        => false,
            'menu_position' => $this->menu_order,
            'menu_icon'     => $this->icon,
            'show_in_menu'  => true,
            'show_ui'       => true,
            'can_export'    => false,
            'has_archive'   => false,
            'rewrite'       => $rewrite,
            'show_in_rest'  => false,
        ];

        register_post_type( static::SLUG, $args );
    }
}
