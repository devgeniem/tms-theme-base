<?php
/**
 * Copyright (c) 2023. Hion Digital
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class AnchorLinksFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class AnchorLinksFields extends Field\Group {

    /**
     * The constructor for field.
     *
     * @param string $label Label.
     * @param null   $key   Key.
     * @param null   $name  Name.
     */
    public function __construct( $label = '', $key = null, $name = null ) {
        parent::__construct( $label, $key, $name );

        try {
            $this->add_fields( $this->sub_fields() );
        }
        catch ( \Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title'            => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'      => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'anchor_links'     => [
                'label'        => 'Ankkurilinkit',
                'instructions' => '',
                'button'       => 'Lisää linkki',
            ],
            'anchor_link'      => [
                'label'        => 'Ankkurilinkki',
                'instructions' => 'Kirjoita URL-kenttään "#" ja haluamasi lohkon tai komponentin HTML-ankkuri, esim. #lohkon-ankkuri',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\ExtendedWysiwyg( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_tabs( 'visual' )
            ->set_toolbar( 'tms-minimal' )
            ->disable_media_upload()
            ->set_height( 100 )
            ->set_instructions( $strings['description']['instructions'] );

        $anchor_links_field = ( new Field\Repeater( $strings['anchor_links']['label'] ) )
            ->set_key( "{$key}_anchor_links" )
            ->set_name( 'anchor_links' )
            ->set_layout( 'block' )
            ->set_button_label( $strings['anchor_links']['button'] )
            ->set_instructions( $strings['anchor_links']['instructions'] );

        $anchor_link_field = ( new Field\Link( $strings['anchor_link']['label'] ) )
            ->set_key( "{$key}_anchor_link" )
            ->set_name( 'anchor_link' )
            ->set_instructions( $strings['anchor_link']['instructions'] );

        $anchor_links_field->add_field( $anchor_link_field );

        return [
            $title_field,
            $description_field,
            $anchor_links_field,
        ];
    }
}
