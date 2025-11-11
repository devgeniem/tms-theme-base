<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields\Settings;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use Geniem\ACF\Field\Tab;
use TMS\Theme\Base\Logger;

/**
 * Class PageSettingsTab
 *
 * @package TMS\Theme\Base\ACF\Tab
 */
class SitemapSettingsTab extends Tab {

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
        'tab'                       => 'Sivukartta',
        'enable_sibling_navigation' => [
            'title'        => 'Sivukartan osoitteet',
            'instructions' => 'Syötä osoitteet, jotka lisätään sivukarttaan.',
        ],
        'sitemap_links'             => [
            'title'        => 'Sivukartan linkit',
            'instructions' => 'Linkit jotka lisätään sivukarttaan Cookiebottia varten.',
            'button_label' => 'Lisää linkki',
        ],
        'sitemap_link'              => [
            'title'        => 'Linkki',
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
            $sitemap_links_field = ( new Field\Repeater( $strings['sitemap_links']['title'] ) )
                ->set_key( "{$key}sitemap_links" )
                ->set_name( 'sitemap_links' )
                ->set_button_label( $strings['sitemap_links']['button_label'] )
                ->set_instructions( $strings['sitemap_links']['instructions'] );

            $sitemap_link_field = ( new Field\Link( $strings['sitemap_link']['title'] ) )
                ->set_key( "{$key}_sitemap_link" )
                ->set_name( 'sitemap_link' )
                ->set_instructions( $strings['sitemap_link']['instructions'] );

            $sitemap_links_field->add_field( $sitemap_link_field );

            $this->add_fields( [
                $sitemap_links_field,
            ] );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
