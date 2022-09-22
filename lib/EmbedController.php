<?php
namespace TMS\Theme\Base;

/**
 * Class EmbedController
 *
 * This class handles custom oEmbeds.
 *
 * @package TMS\Theme\Base
 */
class EmbedController implements Interfaces\Controller {

    /**
     * Initialize the class' variables and add methods
     * to the correct action hooks.
     *
     * @return void
     */
    public function hooks() : void {
        \add_action( 'init', \Closure::fromCallable( [ $this, 'register_quickchannel_embed_handler' ] ) );
    }

    /**
     * Handle play.quickchannel.com embeds.
     */
    protected function register_quickchannel_embed_handler() {
        $quickchannel_format = '#https?://(www.)?play\.quickchannel\.com/play/([^/]+)#i';

        \wp_oembed_add_provider( $quickchannel_format, 'https://www.play.quickchannel.com/play', true );
        \wp_embed_register_handler(
            'quickchannel_video_url',
            $quickchannel_format,
            fn( $matches ) => sprintf(
                '<iframe
                    title="Quickchannel Player"
                    src="https://play.quickchannel.com/embed/%s"
                ></iframe>',
                $matches[2],
            ),
        );
    }
}
