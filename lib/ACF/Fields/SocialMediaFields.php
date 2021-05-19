<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class SocialMediaFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class SocialMediaFields extends \Geniem\ACF\Field\Group {

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
            'title'         => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'   => [
                'label'        => 'Teksti',
                'instructions' => '',
            ],
            'flocker_embed' => [
                'label'        => 'Flocker upote',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_instructions( $strings['description']['instructions'] );

        $flocker_embed_field = ( new Field\Textarea( $strings['flocker_embed']['label'] ) )
            ->set_key( "${key}_flocker_embed" )
            ->set_name( 'flocker_embed' )
            ->set_instructions( $strings['flocker_embed']['instructions'] );

        return [
            $title_field,
            $description_field,
            $flocker_embed_field,
        ];
    }
}
