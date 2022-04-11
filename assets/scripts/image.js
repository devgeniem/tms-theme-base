/**
 * Copyright (c) 2021. Geniem Oy
 * Image controller.
 */

import 'modaal';

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class Image {
    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        $( '.has-modal' ).modaal( {
            type: 'image',
            background_scroll: false,
            after_open: this.disableBackgroundItemFocusing,
            before_close: this.setInertToFalse,
            accessible_title: window.s.modaal.accessible_title || 'Dialog Window - Close (Press escape to close)',
            close_aria_label: window.s.modaal.close || 'Close (Press escape to close)',
        } );
    }

    /**
     * A function to make all the items unfocusable except the opened lightbox gallery
     */
    disableBackgroundItemFocusing() {

        // Check the body if modaal-noscroll class is dynamically added, meaning that the gallery is open.
        if ( $( 'body' ).hasClass( 'modaal-noscroll' ) ) {

            // Not cached for the .modaal-wrapper is generated dynamically, and to make sure we get every item here
            const allContainers = $( 'body' ).children().not( '.modaal-wrapper' );

            // Set inert to true on all the containers outside of the modaal-wrapper
            allContainers.each( ( index, item ) => {

                // Small timeout to make sure this applies to everything - without timeout this won't work
                window.setTimeout( item.inert = true, 100 );

            } );
        }
    }

    /**
     * Set inert to false on all items, used after closing the lightbox gallery - this makes the items
     * focusable again.
     */
    setInertToFalse() {

        // Not cached, to make sure we have everything
        const allContainers = $( 'body' ).children().not( '.modaal-wrapper' );

        // Set inert to true on all the containers outside of the modaal-wrapper
        allContainers.each( ( index, item ) => {
            item.inert = false;
        } );
    }
}
