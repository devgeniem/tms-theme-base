<?php
namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class SomeLinkListFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class SomeLinkListFields extends Field\Group {

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
            'title'       => [
                'label'        => 'Otsikko',
                'instructions' => '',
            ],
            'description' => [
                'label'        => 'Kuvaus',
                'instructions' => '',
            ],
            'links'       => [
                'label'        => 'Some-linkit',
                'instructions' => '',
                'button'       => 'Lisää linkki',
            ],
            'icon'        => [
                'label'        => 'Ikoni',
                'instructions' => '',
            ],
            'link'        => [
                'label'        => 'Linkki',
                'instructions' => 'Linkkiteksti on pakollinen, muuten linkkiä ei näytetä.',
            ],
        ];

        $key = $this->get_key();

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "{$key}_title" )
            ->set_name( 'title' )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\ExtendedWysiwyg( $strings['description']['label'] ) )
            ->set_key( "{$key}_description" )
            ->set_name( 'description' )
            ->set_tabs( 'visual' )
            ->set_toolbar( 'tms-minimal' )
            ->disable_media_upload()
            ->set_height( 100 )
            ->set_instructions( $strings['description']['instructions'] );

        $links_field = ( new Field\Repeater( $strings['links']['label'] ) )
            ->set_key( "{$key}_links" )
            ->set_name( 'links' )
            ->set_layout( 'block' )
            ->set_button_label( $strings['links']['button'] )
            ->set_instructions( $strings['links']['instructions'] );

        $icon_field = ( new Field\Select( $strings['icon']['label'] ) )
            ->set_key( "{$key}_icon" )
            ->set_name( 'icon' )
            ->set_choices( [
                'facebook'  => 'Facebook',
                'instagram' => 'Instagram',
                'twitter'   => 'X (Twitter)',
                'youtube'   => 'YouTube',
                'linkedin'  => 'LinkedIn',
                'tiktok'    => 'TikTok',
                'snapchat'  => 'Snapchat',
                'spotify'   => 'Spotify',
                'threads'   => 'Threads',
            ] )
            ->set_default_value( 'facebook' )
            ->set_required()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "{$key}_link" )
            ->set_name( 'link' )
            ->set_required()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['link']['instructions'] );

        $links_field->add_fields( [
            $icon_field,
            $link_field,
        ] );

        return [
            $title_field,
            $description_field,
            $links_field,
        ];
    }
}
