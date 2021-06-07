<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Field\TextEditor;
use TMS\Theme\Base\Logger;

/**
 * Class TextBlockFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class TextBlockFields extends Field\Group {

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
            'text' => [
                'label'        => 'Sisältö',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $text_field = ( new TextEditor( $strings['text']['label'] ) )
            ->set_key( "${key}_text" )
            ->set_name( 'text' )
            ->set_required()
            ->set_height( 300 )
            ->set_instructions( $strings['text']['instructions'] );

        return [ $text_field ];
    }
}
