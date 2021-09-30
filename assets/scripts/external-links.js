/**
 * Copyright (c) 2021. Geniem Oy
 * External links controller.
 */

import Common from './common';

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
        const icon = Common.makeIcon( 'external', 'icon--medium ml-1' );

        $( '#main-content a[href*="//"]:not([href*="' + domain + '"])' ).append( icon );
    }
}