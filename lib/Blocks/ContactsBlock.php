<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Blocks;

use Geniem\ACF\Block;
use TMS\Theme\Base\ACF\Fields\ContactsFields;
use TMS\Theme\Base\ACF\Fields\ImageFields;
use TMS\Theme\Base\ACF\Fields\MapFields;
use TMS\Theme\Base\PostType\Contact;
use TMS\Theme\Base\Settings;

/**
 * Class MapBlock
 *
 * @package TMS\Theme\Base\Blocks
 */
class MapBlock extends BaseBlock {

    /**
     * The block name (slug, not shown in admin).
     *
     * @var string
     */
    const NAME = 'contacts';

    /**
     * The block acf-key.
     *
     * @var string
     */
    const KEY = 'contacts';

    /**
     * The block icon
     *
     * @var string
     */
    protected $icon = 'groups';

    /**
     * Create the block and register it.
     */
    public function __construct() {
        $this->title = 'Yhteystiedot';

        parent::__construct();
    }

    /**
     * Create block fields.
     *
     * @return array
     */
    protected function fields() : array {
        $group = new ContactsFields( $this->title, self::NAME );

        return apply_filters(
            'tms/block/' . self::KEY . '/fields',
            $group->get_fields()
        );
    }

    /**
     * This filters the block ACF data.
     *
     * @param array  $data       Block's ACF data.
     * @param Block  $instance   The block instance.
     * @param array  $block      The original ACF block array.
     * @param string $content    The HTML content.
     * @param bool   $is_preview A flag that shows if we're in preview.
     * @param int    $post_id    The parent post's ID.
     *
     * @return array The block data.
     */
    public function filter_data( $data, $instance, $block, $content, $is_preview, $post_id ) : array { // phpcs:ignore
        if ( empty( $data['contacts'] ) ) {
            return $data;
        }


        $the_query = new \WP_Query( [
            'post_type'      => Contact::SLUG,
            'posts_per_page' => 100,
            'fields'         => 'ids',
            'post__in'       => array_map( 'absint', $data['contacts'] ),
            'no_found_rows'  => true,
            'meta_key'       => 'last_name',
            'orderby'        => [
                'menu_order' => 'ASC',
                'meta_value' => 'ASC', // phpcs:ignore
            ],
        ] );

        if ( ! $the_query->have_posts() ) {
            return $data;
        }

        $default_image           = Settings::get_setting( 'contacts_default_image' );
        $field_keys              = $data['fields'];
        $data['filled_contacts'] = array_map( function ( $id ) use ( $field_keys, $default_image ) {
            $fields = [];

            foreach ( $field_keys as $field_key ) {
                $fields[ $field_key ] = get_field( $field_key, $id );

                if ( $field_key === 'image' && empty( $fields[ $field_key ] ) && ! empty( $default_image ) ) {
                    $fields[ $field_key ] = $default_image;
                }
            }

            if ( isset( $fields['phone_repeater'] ) ) {
                $fields['phone_repeater'] = array_filter( $fields['phone_repeater'], function ( $item ) {
                    return ! empty( $item['phone_text'] ) || ! empty( $item['phone_number'] );
                } );
            }

            return $fields;
        }, $the_query->posts );

        $data['column_class'] = in_array( 'image', $field_keys, true )
            ? 'is-6'
            : 'is-6 is-3-desktop';

        return apply_filters( 'tms/acf/block/' . self::KEY . '/data', $data );
    }
}
