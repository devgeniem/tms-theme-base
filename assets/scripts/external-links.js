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

        // Links in regular context
        $( '#main-content a[href*="//"]:not(.button, .logo-wall__link, .link-list a, [href*="' + domain + '"])' ).append( icon ); // eslint-disable-line

        // Links with icons (replace current icon with "opens in new window" -icon)
        $( '#main-content a[href*="//"]:has(.icon):not(.link-list a, [href*="' + domain + '"])' ).each( function() {
            const iconOld = $( this ).find( '.icon' );
            const iconNew = $.parseHTML( icon );

            // Copy the original icon classes to retain styling
            $( iconNew ).addClass( iconOld.attr( 'class' ) );

            // Remove the old icon
            $( this ).find( '.icon' ).remove();

            // Append new icon (links may have a child element like a span or p in which the icon is appended)
            if ( $( this ).children().length > 0 ) {
                $( this ).children().first().append( iconNew );
            }
            else {
                $( this ).append( iconNew );
            }
            $( this ).children( '.icon' ).attr( 'aria-hidden', 'true' );
        } );

        // Translations are defined in models/strings.php,
        // and loaded to windows.s in lib/Assets.php.
        const translations = window.s.common || {
            target_blank: 'Opens in a new window',
        };

        // Add instructional text for screen readers on links which open a new window/tab
        $( 'a[target="_blank"]' ).append( `<span class="is-sr-only">(${ translations.target_blank })</span>` );
    }
}
