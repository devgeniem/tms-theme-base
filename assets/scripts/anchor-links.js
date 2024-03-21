/*
 *  Copyright (c) 2024. Hion Digital
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class AnchorLinks
 */
export default class AnchorLinks {

    /**
     * Scroll to anchor element
     *
     * @param {Object} event The click event object.
     *
     * @return {void}
     */
    scrollToDiv( event ) {
        $( 'html, body' ).animate( {
            scrollTop: $( $( event.target ).attr( 'href' ) ).offset().top,
        }, 200 );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.anchor-links__list a' ).on( 'click', this.scrollToDiv.bind( this ) );
    }
}
