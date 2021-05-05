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
        } );
    }
}
