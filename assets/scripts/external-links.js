/**
 * Copyright (c) 2021. Geniem Oy
 * External links controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Export the class reference.
 */
export default class ExternalLinks {
    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        // Add external icon for links pointing outside of the current domain
        const domain = window.location.hostname;
        const icon = '<span aria-hidden="true"><svg class="icon icon--external icon--medium ml-1"><use xlink:href="#icon-external"></use></svg></span>'; // eslint-disable-line

        $( '#main-content a:not([href*="' + domain + '"])' ).append( icon );
    }
}
