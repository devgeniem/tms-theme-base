<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Fields;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class IconLinksFields
 *
 * @package TMS\Theme\Base\ACF\Fields
 */
class IconLinksFields extends Field\Group {

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
                'instructions' => '',
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
            ->set_key( "${key}_rows" )
            ->set_name( 'rows' )
            ->set_min( 3 )
            ->set_max( 9 )
            ->set_layout( 'block' )
            ->set_button_label( $strings['rows']['button'] )
            ->set_instructions( $strings['rows']['instructions'] );

        $icon_field = ( new Field\Select( $strings['icon']['label'] ) )
            ->set_key( "${key}_icon" )
            ->set_name( 'icon' )
            ->set_choices( [
                'icon-ambulanssi'      => 'Ambulanssi',
                'icon-auto'            => 'Auto',
                'icon-bussi'           => 'Bussi',
                'icon-chat'            => 'Chat',
                'icon-finlaysoninalue' => 'Finlaysonin alue',
                'icon-haulitorni'      => 'Haulitorni',
                'icon-idea'            => 'Idea',
                'icon-info'            => 'Info',
                'icon-jaakiekko'       => 'Jääkiekko',
                'icon-jarvi'           => 'Järvi',
                'icon-juna'            => 'Juna',
                'icon-kahvikuppi'      => 'Kahvikuppi',
                'icon-kalastus'        => 'Kalastus',
                'icon-kamera'          => 'Kamera',
                'icon-kannykka'        => 'Kännykkä',
                'icon-kattaus'         => 'Kattaus',
                'icon-kaupunki'        => 'Kaupunki',
                'icon-kavely'          => 'Kävely',
                'icon-kasvu'           => 'Kasvu',
                'icon-kello'           => 'Kello',
                'icon-kirja'           => 'Kirja',
                'icon-koira'           => 'Koira',
                'icon-koti'            => 'Koti',
                'icon-koulu'           => 'Koulu',
                'icon-laiva'           => 'Laiva',
                'icon-lapsi'           => 'Lapsi',
                'icon-latu'            => 'Latu',
                'icon-lehti'           => 'Lehti',
                'icon-leikkipuisto'    => 'Leikkipuisto',
                'icon-lentokone'       => 'Lentokone',
                'icon-lukko'           => 'Lukko',
                'icon-metso'           => 'Metso',
                'icon-mies'            => 'Mies',
                'icon-muistilista'     => 'Muistilista',
                'icon-musiikki'        => 'Musiikki',
                'icon-nainen'          => 'Nainen',
                'icon-nasinneula'      => 'Näsinneula',
                'icon-nuija'           => 'Nuija',
                'icon-nuotio'          => 'Nuotio',
                'icon-osaaminen'       => 'Osaaminen',
                'icon-paikka'          => 'Paikka',
                'icon-peukku'          => 'Peukku',
                'icon-puisto'          => 'Puisto',
                'icon-pyora'           => 'Pyörä',
                'icon-raatihuone'      => 'Raatihuone',
                'icon-raha'            => 'Raha',
                'icon-ratikka'         => 'Ratikka',
                'icon-ratinanstadion'  => 'Ratinan stadion',
                'icon-sairaala'        => 'Sairaala',
                'icon-sauna'           => 'Sauna',
                'icon-sieni'           => 'Sieni',
                'icon-sopimus'         => 'Sopimus',
                'icon-soutuvene'       => 'Soutuvene',
                'icon-sydan'           => 'Sydän',
                'icon-tammerkoski'     => 'Tammerkoski',
                'icon-teatteri'        => 'Teatteri',
                'icon-tehdas'          => 'Tehdas',
                'icon-tehtava'         => 'Tehtävä',
                'icon-teltta'          => 'Teltta',
                'icon-timantti'        => 'Timantti',
                'icon-tori'            => 'Tori',
                'icon-wifi'            => 'Wifi',
                'icon-alykas'          => 'Älykäs',
            ] )
            ->allow_null()
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['icon']['instructions'] );

        $title_field = ( new Field\Text( $strings['title']['label'] ) )
            ->set_key( "${key}_title" )
            ->set_name( 'title' )
            ->set_wrapper_width( 50 )
            ->set_instructions( $strings['title']['instructions'] );

        $description_field = ( new Field\Textarea( $strings['description']['label'] ) )
            ->set_key( "${key}_description" )
            ->set_name( 'description' )
            ->set_wrapper_width( 50 )
            ->set_rows( 2 )
            ->set_instructions( $strings['description']['instructions'] );

        $link_field = ( new Field\Link( $strings['link']['label'] ) )
            ->set_key( "${key}_link" )
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
