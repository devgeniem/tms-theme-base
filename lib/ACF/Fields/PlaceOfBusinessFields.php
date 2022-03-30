<?php

namespace TMS\Theme\Base\ACF\Fields;

use Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Integrations\Tampere\PlaceOfBusinessApiController;
use TMS\Theme\Base\Logger;
use TMS\Theme\Base\PostType\Contact;

/**
 * Class PlaceOfBusinessFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class PlaceOfBusinessFields extends \Geniem\ACF\Field\Group {

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
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        add_filter(
            'acf/load_field/name=place_of_business',
            [ $this, 'fill_place_of_business_choices' ]
        );
    }

    /**
     * This returns all sub fields of the parent groupable.
     *
     * @return array
     * @throws \Geniem\ACF\Exception In case of invalid ACF option.
     */
    protected function sub_fields() : array {
        $strings = [
            'title'        => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description'  => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'contacts'     => [
                'label'        => 'Yhteystiedot',
                'instructions' => '',
            ],
            'place_of_business' => [
                'label'        => 'Tampere-sivuston toimipaikat',
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

        $place_of_business_field = ( new Field\Select( $strings['place_of_business']['label'] ) )
            ->set_key( "${key}_place_of_business" )
            ->set_name( 'place_of_business' )
            ->allow_multiple()
            ->allow_null()
            ->use_ui()
            ->set_instructions( $strings['place_of_business']['instructions'] );

        return [
            $title_field,
            $description_field,
            $place_of_business_field,
        ];
    }

    /**
     * Fill API contacts field choices
     *
     * @param array $field ACF field.
     *
     * @return array
     */
    public function fill_place_of_business_choices( array $field ) : array {
        $api     = new PlaceOfBusinessApiController();
        $results = $api->get();

        if ( empty( $results ) ) {
            return $field;
        }

        foreach ( $results as $result ) {
            $field['choices'][ $result->id ] = $result->title;
        }

        return $field;
    }
}
