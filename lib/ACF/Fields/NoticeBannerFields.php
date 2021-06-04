<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class NoticeBannerFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class NoticeBannerFields extends Field\Group {

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
                'label'        => 'Teksti',
                'instructions' => '',
            ],
            'link'             => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
            'background_color' => [
                'label'        => 'Taustaväri',
                'instructions' => '',
            ],
            'icon'             => [
                'label'        => 'Ikoni',
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

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "${key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $background_color_field = ( new Field\Select( $strings['background_color']['label'] ) )
            ->set_key( "${key}_background_color" )
            ->set_name( 'background_color' )
            ->set_choices( [
                'primary'   => 'Pääväri',
                'secondary' => 'Toissijainen väri',
            ] )
            ->set_default_value( 'has-background-black' )
            ->set_instructions( $strings['background_color']['instructions'] );

        // TODO: Tehdään tämä myös ikoninostoon.
        $icons = apply_filters( 'tms/theme/icons', [] );
        $icons = apply_filters( 'tms/acf/field/' . $key . '_icon/choices', $icons );

        $icon_field = ( new Field\Select( $strings['icon']['label'] ) )
            ->set_key( "${key}_icon" )
            ->set_name( 'icon' )
            ->set_choices( $icons )
            ->allow_null()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['icon']['instructions'] );

        return [
            $title_field,
            $description_field,
            $link_field,
            $background_color_field,
            $icon_field,
        ];
    }
}
