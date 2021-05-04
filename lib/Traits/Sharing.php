<?php
/**
 *  Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Traits;

use TMS\Theme\Base\Settings;

/**
 * Trait Sharing
 *
 * @package TMS\Theme\Base\Traits
 */
trait Sharing {

    /**
     * Share links
     *
     * @return mixed
     */
    public function share_links() {
        return apply_filters(
            'tms/theme/share_links',
            $this->get_share_links()
        );
    }

    /**
     * Get share links
     *
     * @return null|array
     */
    protected function get_share_links() : ?array {
        $selected_channels = Settings::get_setting( 'some_channels' );

        if ( empty( $selected_channels ) ) {
            return null;
        }

        $channels          = $this->get_channels();
        $selected_channels = array_filter( $selected_channels, fn( $item ) => isset( $channels[ $item ] ) );
        $current_post      = get_queried_object();

        if ( ! $current_post instanceof \WP_Post ) {
            return [];
        }

        return array_map( function ( $selected_channel ) use ( $channels, $current_post ) {
            $item = $channels[ $selected_channel ];

            $item['link'] = strtr(
                $item['link'],
                [
                    '%title' => $current_post->post_title,
                    '%url'   => get_the_permalink( $current_post->ID ),
                ]
            );

            return $item;
        }, $selected_channels );
    }

    /**
     * Get channels
     *
     * @return array
     */
    protected function get_channels() : array {
        $channels = [
            'facebook'  => [
                'link'          => 'https://www.facebook.com/sharer/sharer.php?u=%url',
                'icon'          => 'facebook',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on Facebook', 'tms-theme-base' ),
            ],
            'email'     => [
                'link'          => 'mailto:?subject=%title&body=%url',
                'icon'          => 'email',
                'extra_classes' => [],
                'sr_text'       => __( 'Share by email', 'tms-theme-base' ),
            ],
            'link'      => [
                'link'          => '%url',
                'icon'          => 'link',
                'extra_classes' => [ 'js-copy-to-clipboard' ],
                'sr_text'       => __( 'Copy link to clipboard', 'tms-theme-base' ),
                'callback_text' => __( 'Copied to clipboard', 'tms-theme-base' ),
            ],
            'whatsapp'  => [
                'link'          => 'https://wa.me/?text=%url',
                'icon'          => 'whatsapp',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on Whatsapp', 'tms-theme-base' ),
            ],
            'twitter'   => [
                'link'          => 'https://twitter.com/intent/tweet?text=%url',
                'icon'          => 'twitter',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on Twitter', 'tms-theme-base' ),
            ],
            'linkedin'  => [
                'link'          => 'https://www.linkedin.com/sharing/share-offsite/?url=%url',
                'icon'          => 'linkedin',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on LinkedIn', 'tms-theme-base' ),
            ],
            'vkontakte' => [
                'link'          => 'http://vk.com/share.php?url=%url&title=%title',
                'icon'          => 'vkontakte',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on Vkontakte', 'tms-theme-base' ),
            ],
            'line'      => [
                'link'          => 'line://msg/text/%url',
                'icon'          => 'line',
                'extra_classes' => [],
                'sr_text'       => __( 'Share on Line', 'tms-theme-base' ),
            ],
        ];

        return apply_filters( 'tms/theme/share_links/channels', $channels );
    }
}
