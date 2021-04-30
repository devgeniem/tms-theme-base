<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\ACF\Layouts\AccordionImageLayout;
use TMS\Theme\Base\ACF\Layouts\AccordionTableLayout;
use TMS\Theme\Base\ACF\Layouts\AccordionVideoLayout;
use TMS\Theme\Base\ACF\Layouts\AccordionWysiwygLayout;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class AccordionFields extends Field\Group {

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
            'title'           => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'     => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'sections'        => [
                'label'        => 'Haitarit',
                'instructions' => '',
                'button'       => 'Lisää uusi',
            ],
            'section_title'   => [
                'label'        => 'Haitarin otsikko',
                'instructions' => '',
            ],
            'section_content' => [
                'label'        => 'Haitarin sisältö',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_rows( 4 )
            ->set_new_lines( 'wpautop' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['description']['instructions'] );

        $sections_field = ( new Field\Repeater( $strings['sections']['label'] ) )
            ->set_key( "${key}_sections" )
            ->set_name( 'sections' )
            ->set_layout( 'block' )
            ->set_button_label( $strings['sections']['button'] )
            ->set_instructions( $strings['sections']['instructions'] );

        $section_title_field = ( new Field\Text( $strings['section_title']['label'] ) )
            ->set_key( "${key}_section_title" )
            ->set_name( 'section_title' )
            ->set_instructions( $strings['section_title']['instructions'] );

        $section_content_field = ( new Field\FlexibleContent( $strings['section_content']['label'] ) )
            ->set_key( "${key}_section_content" )
            ->set_name( 'section_content' )
            ->set_instructions( $strings['section_content']['instructions'] );

        $section_content_layouts = [
            AccordionWysiwygLayout::class,
            AccordionImageLayout::class,
            AccordionVideoLayout::class,
        ];

        if ( is_plugin_active( 'tablepress/tablepress.php' ) ) {
            $section_content_layouts[] = AccordionTableLayout::class;
        }

        $section_content_layouts = apply_filters(
            'tms/acf/field/' . $section_content_field->get_key() . '/layouts',
            $section_content_layouts
        );

        foreach ( $section_content_layouts as $section_content_layout ) {
            $section_content_field->add_layout( new $section_content_layout( $key ) );
        }

        $sections_field->add_fields( [
            $section_title_field,
            $section_content_field,
        ] );

        return [
            $title_field,
            $description_field,
            $sections_field,
        ];
    }
}
