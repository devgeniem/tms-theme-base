/*
 *  Copyright (c) 2021. Geniem Oy
 */

/**
 * Exove news JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class ExoveNews
 */
export default class ExoveNews {

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.exoveContent = $( '.topical-content' );
    }

    /**
     * Remove empty html elements
     *
     * @return {void}
     */
    removeEmptyHtmlElements() {
        if ( ! this.exoveContent.length ) {
            return;
        }

        this.exoveContent.find( 'a, br' ).each( function() {
            if ( $.trim( $( this ).text() ) === '' && ! $( this ).hasClass( 'anchor-link' ) ) {
                $( this ).remove();
            }
        } );

        this.exoveContent.find( 'p' ).each( function() {
            if ( $( this ).is( ':empty' ) ) {
                $( this ).remove();
            }
        } );

        this.exoveContent.find( '.process-accordion__heading' ).each( function() {
            $(this).addClass('js-toggle');
        } );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.cache();
        this.removeEmptyHtmlElements();
    }
}
