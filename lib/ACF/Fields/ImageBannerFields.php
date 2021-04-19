<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

class ImageBannerFields extends \Geniem\ACF\Field\Group {

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
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title' => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'image' => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
            'link'  => [
                'label'        => 'Painike',
                'instructions' => '',
            ],
            'align' => [
                'label'        => 'Tasaus',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "${key}_image" )
            ->set_name( 'image' )
            ->set_required()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['image']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "${key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $align_field = ( new Field\Select( $strings['align']['label'] ) )
            ->set_key( "${key}_align" )
            ->set_name( 'align' )
            ->set_choices( [
                'has-text-left'     => 'Vasen',
                'has-text-right'    => 'Oikea',
                'has-text-centered' => 'Keskitetty',
            ] )
            ->set_default_value( 'has-text-centered' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['align']['instructions'] );

        return [
            $title_field,
            $image_field,
            $link_field,
            $align_field,
        ];
    }
}
