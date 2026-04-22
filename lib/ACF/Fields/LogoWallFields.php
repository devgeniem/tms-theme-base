<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class LogoWallFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class LogoWallFields extends Field\Group {
    /**
     * Allowed filetypes in logo field. Affects logo instructions.
     *
     * @var array
     */
    private array $allowed_filetypes = [ 'jpg', 'png', 'svg' ];

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
        $file_types_list = implode( ', ', $this->allowed_filetypes );

        $strings = [
            'header' => [
                'label'        => 'Logoseinän otsikko',
                'instructions' => '',
            ],
            'rows'   => [
                'label'        => 'Logot',
                'instructions' => 'Yhteistyökumppaneiden, tai vastaavien linkitetyt logot',
                'button'       => 'Lisää logo',
            ],
            'logo'   => [
                'label'        => 'Logo',
                'instructions' => "Sallitut tiedostomuodot: {$file_types_list}.",
            ],
            'link'   => [
                'label'        => 'Linkki',
                'instructions' => '',
            ],
        ];

        $key = $this->get_key();

        $header_field = ( new Field\Text( $strings['header']['label'] ) )
            ->set_key( "{$key}_header" )
            ->set_name( 'header' )
            ->set_instructions( $strings['header']['instructions'] );

        $rows_field = ( new Field\Repeater( $strings['rows']['label'] ) )
            ->set_key( "{$key}_rows" )
            ->set_name( 'rows' )
            ->set_min( 1 )
            ->set_max( 20 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $logo_field = ( new Field\Image( $strings['logo']['label'] ) )
            ->set_key( "{$key}_logo" )
            ->set_name( 'logo' )
            ->set_wrapper_width( 50 )
            ->set_mime_types( $this->allowed_filetypes )
            ->set_required()
            ->set_instructions( $strings['logo']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "{$key}_link" )
            ->set_name( 'link' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $rows_field->add_fields( [
            $logo_field,
            $link_field,
        ] );

        return [
            $header_field,
            $rows_field,
        ];
    }
}
