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
            'title' => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'text' => [
                'label'        => 'Sisältö',
                'instructions' => '',
            ],
            'link' => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
            'background_color' => [
                'label'        => 'Taustaväri',
                'instructions' => '',
            ],
            'width' => [
                'label'        => 'Leveys',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->redipress_include_search()
            ->set_instructions( $strings['title']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "{$key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $text_field = ( new TextEditor( $strings['text']['label'] ) )
            ->set_key( "{$key}_text" )
            ->set_name( 'text' )
            ->set_required()
            ->set_height( 300 )
            ->redipress_include_search()
            ->set_instructions( $strings['text']['instructions'] );

        $background_color_field = ( new Field\Radio( $strings['background_color']['label'] ) )
            ->set_key( "{$key}_background_color" )
            ->set_name( 'background_color' )
            ->set_wrapper_width( 50 )
            ->set_layout( 'horizontal' )
            ->set_choices( [
                'no_color'  => 'Ei taustaväriä',
                'primary'   => 'Pääväri',
                'secondary' => 'Toissijainen väri',
            ] )
            ->set_instructions( $strings['background_color']['instructions'] );

        $width_field = ( new Field\Radio( $strings['width']['label'] ) )
            ->set_key( "{$key}_width" )
            ->set_name( 'width' )
            ->set_wrapper_width( 50 )
            ->set_layout( 'horizontal' )
            ->set_choices( [
                'is-align-full' => '100%',
                'is-align-wide' => '75%',
                'is-align-half' => '50%',
            ] )
            ->set_instructions( $strings['width']['instructions'] );

        return [
            $title_field,
            $link_field,
            $text_field,
            $background_color_field,
            $width_field,
        ];
    }
}
