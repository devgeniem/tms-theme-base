<?php

namespace TMS\Theme\Base\Formatters;

use TMS\Plugin\ContactImporter;
use TMS\Plugin\ContactImporter\PlaceOfBusinessFacade;

/**
 * Class PlaceOfBusinessFormatter
 *
 * @package TMS\Theme\Base\Formatters
 */
class PlaceOfBusinessFormatter implements \TMS\Theme\Base\Interfaces\Formatter {

    /**
     * Define formatter name
     */
    const NAME = 'PlaceOfBusiness';

    /**
     * Hooks
     */
    public function hooks() : void {
        add_filter(
            'tms/acf/block/place-of-business/data',
            [ $this, 'format' ]
        );

        add_filter(
            'tms/acf/layout/place-of-business/data',
            [ $this, 'format' ]
        );
    }

    /**
     * Format view data
     *
     * @param array $data ACF data.
     *
     * @return array
     */
    public function format( array $data ) {
        if ( empty( $data['place_of_business'] ) && empty( $data['place_of_business_post'] ) ) {
            return $data;
        }

        if ( ! empty( $data['place_of_business_post'] ) ) {
            $the_query = new \WP_Query( [
                'post_type'      => 'placeofbusiness-cpt',
                'posts_per_page' => 100,
                'post__in'       => array_map( 'absint', $data['place_of_business_post'] ),
                'no_found_rows'  => true,
                'meta_key'       => 'title',
                'orderby'        => [
                    'menu_order' => 'ASC',
                    'meta_value' => 'ASC', // phpcs:ignore
                ],
            ] );

            $filled_places = $this->map_keys(
                $the_query->posts,
            );
        }

        if ( ! empty( $data['place_of_business'] ) ) {
            $filled_api_places = $this->map_api_results(
                $data['place_of_business'],
            );
        }

        $data['items'] = array_merge(
            $filled_places ?? [],
            $filled_api_places ?? [],
        );

        $data['column_class'] = 'is-12-mobile is-6-tablet';

        return $data;
    }

    /**
     * Map api place of businesses to post like arrays
     *
     * @param array $ids Array of API ID's.
     *
     * @return array|array[]
     */
    public function map_api_results( array $ids = [] ) : array {
        if ( empty( $ids ) ) {
            return [];
        }

        $results = ( new ContactImporter\PlaceOfBusinessApiController() )->get_results();

        if ( empty( $results ) ) {
            return [];
        }

        $results = array_map(
            fn( $result ) => ( new PlaceOfBusinessFacade( $result ) )->to_array(),
            $results
        );

        return (array) array_filter( $results, function ( $result ) use ( $ids ) {
            return in_array( $result['id'], $ids, true );
        } );
    }

    /**
     * Map fields to posts
     *
     * @param array $posts         Array of WP_Post instances.
     *
     * @return array
     */
    public function map_keys( array $posts ) : array {
        if( ! \is_plugin_active( 'tms-plugin-place-of-business-sync/plugin.php' ) ) {
            return [];
        }

        return array_map( function ( $id ) {

            foreach( \get_field_objects($id) as $field ) {
                $item[ $field['name'] ] = \get_field( $field['name'], $id );
            }

            return $item;
        }, $posts );
    }
}
