<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class ImageFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class ImageFields extends Field\Group {

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
            'image'        => [
                'label'        => 'Kuva',
                'instructions' => '',
            ],
            'caption'      => [
                'label'        => 'Vapaaehtoinen kuvateksti',
                'instructions' => '',
            ],
            'is_clickable' => [
                'label'        => 'Saa avata suuremmaksi',
                'instructions' => 'Kun valittuna, kuvan voi klikkaamalla/valitsemalla avata suurempaan kokoon.',
            ],
        ];

        $key = $this->get_key();

        $image_field = ( new Field\Image( $strings['image']['label'] ) )
            ->set_key( "${key}_image" )
            ->set_name( 'image' )
            ->set_required()
            ->set_wrapper_width( 60 )
            ->set_default_value( null )
            ->set_instructions( $strings['image']['instructions'] );

        $is_clickable = ( new Field\TrueFalse( $strings['is_clickable']['label'] ) )
            ->set_key( "${key}_is_clickable" )
            ->set_name( 'is_clickable' )
            ->use_ui()
            ->set_wrapper_width( 40 )
            ->set_default_value( true )
            ->set_instructions( $strings['is_clickable']['instructions'] );

        $caption_field = ( new Field\Wysiwyg( $strings['caption']['label'] ) )
            ->set_key( "${key}_caption" )
            ->set_name( 'caption' )
            ->set_tabs( 'visual' )
            ->set_toolbar( [ 'bold', 'italic', 'link' ] )
            ->disable_media_upload()
            ->set_instructions( $strings['caption']['instructions'] );

        return [
            $image_field,
            $is_clickable,
            $caption_field,
        ];
    }
}
