<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base\Helpers;

use DustPress\Helper;

/**
 * This helper provides a functionality to print an image element with its srcset.
 * The helper can either get the image URLs from WP with a given ID or print
 * the element with custom source URLs.
 *
 * @example: {@image id=image.id alt=image.alt title=image.caption size=large /}
 */
class ImageAdvanced extends Helper {

    /**
     * Outputs the image markup or an error
     *
     * @return string The HTML markup or an error message.
     * @throws \Exception Thrown if image data is missing or has inconsistencies.
     */
    public function output() : string {

        // Store the parameters.
        $image_data = $this->get_image_data( $this->params );

        // If blog_id has been set we are working on the network and need extra stuff.
        if ( empty( $image_data['blog_id'] ) ) {
            return $this->get_image_output( $image_data );
        }

        switch_to_blog( (int) $image_data['blog_id'] );

        $image_output = $this->get_image_output( $image_data );

        restore_current_blog();

        return $image_output;
    }

    /**
     * Gets and formats the data from the parameters given to the image helper tag.
     *
     * @param \Dust\Evaluate\Parameters $params The parameters.
     *
     * @return array $image_data The formatted array.
     */
    private function get_image_data( \Dust\Evaluate\Parameters $params ) : array {

        /**
         * Default structure and all params provided.
         */
        $image_data = [
            'id'      => ( isset( $params->id ) ? (int) $params->id : null ),
            'src'     => $params->src ?? null,
            'size'    => $params->size ?? null,
            'srcset'  => $params->srcset ?? null,
            'sizes'   => $params->sizes ?? null,
            'attrs'   => [],
            'blog_id' => $params->blog_id ?? null,
        ];

        /**
         * This is a key-value(ish) based attribute constructing system.
         * These keys (alt, class, title...) are looped and asked if they are present
         * in the parameters object. If they are, and the _value_ portion isn't
         * empty, we match that the given value is one of the allowed.
         *
         * This way, we are supporting the full attributes list, we have a simple
         * validation and we can take advantage of nice features like build in lazy loading.
         *
         * Values array is defined so that first value is the default from spec,
         * and if the developer given value is default, we unset the value to keep
         * output nicer.
         *
         * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#attributes
         */
        $allowed_attributes = [
            // Global attributes (which are relevant)
            'accesskey'      => [],
            'class'          => [],
            'id'             => [],
            'itemid'         => [],
            'lang'           => [],
            'nonce'          => [],
            'part'           => [],
            'slot'           => [],
            // 'style' -- please do not add style, because it's an antipattern
            'tabindex'       => [],
            // Image attributes
            'alt'            => [],
            'title'          => [],
            'crossorigin'    => [ 'anonymous', 'use-credentials' ],
            'decoding'       => [ 'auto', 'sync', 'async' ],
            'width'          => [],
            'height'         => [],
            'ismap'          => [],
            'loading'        => [ 'eager', 'lazy' ],
            'referrerpolicy' => [
                'no-referrer',
                'no-referrer-when-downgrade',
                'origin',
                'origin-when-cross-origin',
                // 'unsafe-url' -- we are not allowing because it's inherently insecure
            ],
            'usemap'         => [],
        ];

        $allowed_attributes = \apply_filters(
            'dustpress/image/allowed_attributes',
            $allowed_attributes
        );

        foreach ( $allowed_attributes as $attribute => $allowed_values ) {
            if ( ! isset( $params->$attribute ) || empty( $params->$attribute ) ) {
                continue;
            }

            $attribute_value = $params->$attribute ?? null;

            // We have values to check against
            if ( ! empty( $allowed_values ) ) {
                if (
                    ! in_array( $attribute_value, $allowed_values, true ) ||
                    $attribute_value === $allowed_values[0]
                ) {
                    continue;
                }
            }

            $image_data['attrs'][ $attribute ] = $attribute_value;
        }

        return \apply_filters( 'dustpress/image/image_data', $image_data );
    }

    /**
     * Get image output.
     *
     * @param array $image_data Get image output.
     *
     * @return mixed Image output data, error or empty value on failure.
     * @throws \Exception Thrown if image data is missing or has inconsistencies.
     */
    private function get_image_output( array $image_data ) : string {
        return null === $image_data['id']
            ? $this->generate_output_using_src( $image_data )
            : $this->generate_output_using_id( $image_data );
    }

    /**
     * Get the custom HTML srcset markup with the given settings
     *
     * @param array $image_data The given srcset and sizes.
     *
     * @return string            The image markup.
     */
    private function get_image_markup( array $image_data ) : string {

        /**
         * The img tag attributes as key-value.
         */
        $attributes = [
            'id'  => $image_data['id'] ?? null,
            'src' => $image_data['src'] ?? null,
        ];

        // Set our custom attributes as first class attributes.
        if ( ! empty( $image_data['attrs'] ) ) {
            foreach ( $image_data['attrs'] as $attr_key => $attr_val ) {
                $attributes[ $attr_key ] = $attr_val;
            }
        }

        /**
         * We didn't get src from parameters, so we'll be using the ID.
         */
        if ( empty( $attributes['src'] ) ) {
            $image_src_array = wp_get_attachment_image_src(
                $image_data['id'],
                $image_data['size']
            );

            if ( ! is_array( $image_src_array ) ) {
                return $this->image_helper_error(
                    'No image found from the database with the given id.'
                );
            }

            [ $image_src, $image_width, $image_height ] = $image_src_array;

            $attributes['src']    = $image_src ?? false;
            $attributes['width']  = $image_width ?? false;
            $attributes['height'] = $image_height ?? false;
        }

        // Check that the srcset is given as an array.
        if ( ! is_array( $image_data['sizes'] ) ) {
            return $this->image_helper_error( 'Given sizes attribute is not an array.' );
        }

        try {
            $attributes = $this->generate_sizes_and_srcsets( $image_data['id'], $attributes );
        }
        catch ( \Exception $e ) {
            return $this->image_helper_error( $e->getMessage() );
        }

        $attributes = \apply_filters(
            'dustpress/image/image_attributes',
            $attributes
        );

        $attributes_joined = [];
        foreach ( $attributes as $attribute_key => $attribute_value ) {
            if ( empty( $attribute_key ) || empty( $attribute_value ) ) {
                continue;
            }

            $attributes_joined[] = sprintf( '%s="%s"', $attribute_key, $attribute_value );
        }

        /**
         * Concatenate all of the images strings together.
         *
         * @noinspection RequiredAttributes The src attribute comes from $attributes_joined.
         * @noinspection HtmlRequiredAltAttribute The alt attribute comes from $attributes_joined.
         * @noinspection HtmlUnknownAttribute It's the sprintf %s, silly.
         */
        $html = sprintf( '<img %s>', implode( ' ', $attributes_joined ) );

        return apply_filters( 'dustpress/image/markup', $html );
    }

    /**
     * Get all the registered image sizes along with their dimensions
     *
     * @global array $_wp_additional_image_sizes
     *
     * @param int $image_id ID from the image data.
     *
     * @return array $image_sizes The image sizes
     */
    private function get_wp_image_sizes_array( $image_id ) : array {

        // The registered image sizes.
        global $_wp_additional_image_sizes;

        // The default WordPress image sizes. Exclude the thumbnail size.
        $default_image_sizes = [ 'medium', 'medium_large', 'large' ];

        // Loop through the sizes and get the corresponding options from the db.
        foreach ( $default_image_sizes as $size ) {
            $image_sizes[ $size ]['width']  = (int) get_option( "{$size}_size_w" );
            $image_sizes[ $size ]['height'] = (int) get_option( "{$size}_size_h" );
            $image_sizes[ $size ]['crop']   = get_option( "{$size}_crop" ) ?: false;
        }

        // Add custom sizes to the array.
        if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) ) {
            $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );
        }

        // The final array in which we have the properly formatted urls and widths.
        $srcset_array = [];

        // Loop through the sizes in the array and get the urls and widths from WP.
        foreach ( $image_sizes as $size => $size_options ) {
            $url            = wp_get_attachment_image_src( $image_id, $size )[0];
            $width          = $size_options['width'];
            $entry          = $url . ' ' . $width . 'w';
            $srcset_array[] = $entry;
        }

        return $srcset_array;
    }

    /**
     * Generate sizes and srcsets from provided data.
     *
     * @param int   $id         Image Database ID.
     * @param array $attributes Attributes we already have.
     *
     * @return array
     * @throws \Exception If given srcset attribute is not an array.
     */
    private function generate_sizes_and_srcsets( $id, array $attributes ) : array {
        // Concatenate the given sizes to a comma separated list and construct the sizes string.
        $attributes['sizes'] = implode( ', ', $id['sizes'] );

        // Either use the srcset array that is given or fetch the urls and widths using the WP sizes.
        $srcset_array = $id['srcset'] ?? $this->get_wp_image_sizes_array( $id );

        // Check that the srcset is given as an array.
        if ( ! is_array( $srcset_array ) ) {
            throw new \Exception( 'Given srcset attribute is not an array.' );
        }

        // Construct the srcset string.
        $attributes['srcset'] = implode( ', ', $srcset_array );

        return $attributes;
    }

    /**
     * Generalized error responder.
     *
     * @param string $error Error message.
     * @param string $title Title for the error message.
     *
     * @return string
     */
    private function image_helper_error(
        $error = 'No error provided, weird',
        $title = 'DustPress image helper error'
    ) : string {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            return '<p><strong>' . $title . ':</strong><em>' . $error . '</em></p>';
        }

        return '';
    }

    /**
     * Generate img output with known image ID.
     *
     * @param array $image_data Image data.
     *
     * @return string
     *
     * @throws \Exception If prerequisites are missing, or fails.
     */
    private function generate_output_using_id( array $image_data ) : string {
        try {
            // SRC also given.
            if ( $image_data['src'] !== null ) {
                throw new \Exception(
                    'Image id and custom src both given. Only one of these parameters can be used.'
                );
            }
            // Only the ID given as the original image source.
            if ( $image_data['size'] === null ) {
                throw new \Exception(
                    'No image size attribute given. When using the ID, ' .
                    'you have to give a registered size name to the helper.'
                );
            }

            // ID and size given.

            // No custom responsive parameters given
            if ( null === $image_data['srcset'] && null === $image_data['sizes'] ) {

                // Return the WordPress default img-tag from the full-sized image with a source set.
                $the_image_markup = wp_get_attachment_image(
                    $image_data['id'],
                    $image_data['size'],
                    false,
                    $image_data['attrs']
                );

                if ( $the_image_markup || ! empty( $the_image_markup ) ) {
                    return $the_image_markup;
                }

                throw new \Exception(
                    'No image found from the database with the given id.'
                );
            }

            // Custom responsive parameters are given.

            // SRCSET exists but no SIZES attribute is given.
            if ( null === $image_data['sizes'] ) {
                throw new \Exception(
                    'Srcset exists but no sizes attribute is given.'
                );
            }

            // Both custom responsive parameters and the id is given.
            return $this->get_image_markup( $image_data );
        }
        catch ( \Exception $e ) {
            return $this->image_helper_error( $e->getMessage() );
        }
    }

    /**
     * Generate img output with custom src attribute.
     *
     * @param array $image_data Image data.
     *
     * @return string
     *
     * @throws \Exception If prerequisites are missing, or fails.
     */
    private function generate_output_using_src( array $image_data ) : string {
        try {
            // No SRC given either.
            if ( $image_data['src'] === null ) {
                throw new \Exception(
                    'Image id nor custom src given. ' .
                    'The helper needs at least one of these parameters.'
                );
            }

            // Only the SRC given as the original image source.

            // When using the custom SRC, both SRCSET and SIZES need to be given.
            if ( $image_data['srcset'] === null ) {
                throw new \Exception(
                    'Srcset not given. Both the srcset and ' .
                    'the sizes are needed when using a custom src.'
                );
            }

            // When using the custom SRC, both SRCSET and SIZES need to be given.
            if ( $image_data['sizes'] === null ) {
                throw new \Exception(
                    'Sizes not given. Both the srcset and the sizes ' .
                    'are needed when using a custom src.'
                );
            }

            return $this->get_image_markup( $image_data );
        }
        catch ( \Exception $e ) {
            return $this->image_helper_error( $e->getMessage() );
        }
    }
}
