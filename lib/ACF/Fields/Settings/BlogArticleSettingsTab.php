<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\BlogArticle;

/**
 * Class BlogArticleSettings
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class BlogArticleSettingsTab extends \Geniem\ACF\Field\Tab {

    /**
     * Where should the tab switcher be located
     *
     * @var string
     */
    protected $placement = 'left';

    /**
     * Tab strings.
     *
     * @var array
     */
    protected $strings = [
        'tab'              => 'Blogiartikkelien arkisto',
        'blog_name'        => [
            'title'        => 'Blogin nimi',
            'instructions' => '',
        ],
        'blog_description' => [
            'title'        => 'Blogin kuvausteksti',
            'instructions' => '',
        ],
        'blog_logo'        => [
            'title'        => 'Blogin tunnistekuva',
            'instructions' => '',
        ],
        'blog_subtitle'    => [
            'title'        => 'Blogin apuotsikko',
            'instructions' => '',
        ],
        'highlight'        => [
            'title'        => 'Korostettu blogiartikkeli',
            'instructions' => '',
        ],
    ];

    /**
     * The constructor for tab.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) { // phpcs:ignore
        $label = $this->strings['tab'];

        parent::__construct( $label );

        $this->sub_fields( $key );
    }

    /**
     * Register sub fields.
     *
     * @param string $key Field tab key.
     */
    public function sub_fields( $key ) {
        $strings = $this->strings;

        try {
            $blog_name_field = ( new Field\Text( $strings['blog_name']['title'] ) )
                ->set_key( "${key}_blog_name" )
                ->set_name( 'blog_name' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['blog_name']['instructions'] );

            $blog_subtitle_field = ( new Field\Text( $strings['blog_subtitle']['title'] ) )
                ->set_key( "${key}_blog_subtitle" )
                ->set_name( 'blog_subtitle' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['blog_subtitle']['instructions'] );

            $blog_description_field = ( new Field\Textarea( $strings['blog_description']['title'] ) )
                ->set_key( "${key}_blog_description" )
                ->set_name( 'blog_description' )
                ->set_rows( 4 )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['blog_description']['instructions'] );

            $blog_logo_field = ( new Field\Image( $strings['blog_logo']['title'] ) )
                ->set_key( "${key}_blog_logo" )
                ->set_name( 'blog_logo' )
                ->set_return_format( 'id' )
                ->set_preview_size( 'thumbnail' )
                ->set_wrapper_width( 50 )
                ->set_instructions( $strings['blog_logo']['instructions'] );

            $higlight_field = ( new Field\PostObject( $strings['highlight']['title'] ) )
                ->set_key( "${key}_blog_archive_highlight" )
                ->set_name( 'blog_archive_highlight' )
                ->set_post_types( [ BlogArticle::SLUG ] )
                ->allow_null()
                ->set_instructions( $strings['highlight']['instructions'] );

            $this->add_fields( [
                $blog_name_field,
                $blog_subtitle_field,
                $blog_description_field,
                $blog_logo_field,
                $higlight_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
