<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

use Closure;
use Geniem\LinkedEvents\LinkedEventsClient;
use Geniem\LinkedEvents\LinkedEventsException;
use GuzzleHttp\Exception\GuzzleException;
use TMS\Theme\Base\Interfaces\Controller;

/**
 * Class LinkedEvents
 *
 * @package TMS\Theme\Base
 */
class LinkedEvents implements Controller {

    /**
     * Hooks
     */
    public function hooks() : void {
        add_action(
            'wp_ajax_event_search',
            Closure::fromCallable( [ $this, 'admin_event_search_callback' ] )
        );
    }

    protected function admin_event_search_callback() : void {
        $params = $_GET['params'];
        $client = new LinkedEventsClient( 'https://pirkanmaaevents.fi/api/v2/' );

        try {
            $events = $client->get_all( 'event', $params );
        }
        catch ( LinkedEventsException $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
        catch ( GuzzleException $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }
        catch ( \JsonException $e ) {
            ( new Logger() )->error( $e->getMessage(), $e->getTrace() );
        }

        wp_send_json( $events );
    }

    public static function normalize_event( $event ) : array {
        $lang_key = Localization::get_current_language();

        return [
            'name'              => $event->name->{$lang_key},
            'short_description' => $event->short_description->{$lang_key},
            'description'       => nl2br( $event->description->{$lang_key} ),
        ];
    }
}
