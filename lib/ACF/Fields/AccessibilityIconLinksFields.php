<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class AccessibilityIconLinksFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class AccessibilityIconLinksFields extends Field\Group {

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
            'rows'        => [
                'label'        => 'Nostot',
                'instructions' => '',
                'button'       => 'Lisää nosto',
            ],
            'icon'        => [
                'label'        => 'Ikoni',
                'instructions' => '',
            ],
            'title'       => [
                'label'        => 'Otsikko',
                'instructions' => 'Jos jätät kentän tyhjäksi, elementin näkymässä näyteään valitun ikonin oletusteksti.',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'link'        => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $rows_field = ( new Field\Repeater( $strings['rows']['label'] ) )
            ->set_key( "{$key}_rows" )
            ->set_name( 'rows' )
            ->set_min( 2 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $icons = apply_filters( 'tms/theme/acc_icons', [] );
        $icons = apply_filters( 'tms/acf/field/' . $key . '_acc_icon/choices', $icons );

        $icon_field = ( new Field\Select( $strings['icon']['label'] ) )
            ->set_key( "{$key}_acc_icon" )
            ->set_name( 'acc_icon' )
            ->set_choices( $icons )
            ->allow_null()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['icon']['instructions'] );

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_wrapper_width( 50 )
            ->set_rows( 2 )
            ->set_instructions( $strings['description']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "{$key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $rows_field->add_fields( [
            $icon_field,
            $title_field,
            $description_field,
            $link_field,
        ] );

        return [
            $rows_field,
        ];
    }
}
