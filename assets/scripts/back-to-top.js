/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class BackToTop
 */
export default class BackToTop {

    /**
     * Scroll to top
     *
     * @param {Object} event The click event object.
     *
     * @return {void}
     */
    scrollToTop( event ) {
        event.preventDefault();

        $( 'html, body' ).animate( {
            scrollTop: 0,
        }, 200 );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '#js-back-to-top' ).on( 'click', this.scrollToTop.bind( this ) );
    }
}
