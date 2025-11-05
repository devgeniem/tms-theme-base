<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\ACF\Layouts;

use Geniem\ACF\Exception;
use Geniem\ACF\Field;
use TMS\Theme\Base\Logger;

/**
 * Class AccordionVideoLayout
 *
 * @package TMS\Theme\Base\ACF\Layouts
 */
class AccordionVideoLayout extends BaseLayout {

    /**
     * Layout key
     */
    const KEY = '_accordion_video';

    /**
     * Create the layout
     *
     * @param string $key Key from the flexible content.
     */
    public function __construct( string $key ) {
        parent::__construct(
            'Video',
            $key . self::KEY,
            'accordion_video'
        );

        $this->add_layout_fields();
    }

    /**
     * Add layout fields
     *
     * @return void
     */
    private function add_layout_fields() : void {
        $strings = [
            'video' => [
                'label'        => 'Video',
                'instructions' => '',
            ],
            'video_iframe' => [
                'label'        => 'Videon upotuskoodi',
                'instructions' => 'Lisää tähän videon upotuskoodi, jos videota ei voi käyttää pelkän urlin kautta (esim. Quickchannel).',
            ],
        ];

        $key = $this->get_key();

        try {
            $video_field = ( new Field\Oembed( $strings['video']['label'] ) )
                ->set_key( "{$key}_video" )
                ->set_name( 'video' )
                ->set_instructions( $strings['video']['instructions'] );

            $video_iframe_field = ( new Field\Textarea( $strings['video_iframe']['label'] ) )
                ->set_key( "{$key}_video_iframe" )
                ->set_name( 'video_iframe' )
                ->set_instructions( $strings['video_iframe']['instructions'] );

            $fields = [
                $video_field,
                $video_iframe_field,
            ];

            $this->add_fields(
                $this->filter_layout_fields( $fields, $key, self::KEY )
            );
        }
        catch ( Exception $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
    }
}
