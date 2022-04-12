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
            accessible_title: window.s.modaal.accessible_title || 'Dialog Window - Close (Press escape to close)',
            close_aria_label: window.s.modaal.close || 'Close (Press escape to close)',
        } );
    }

    /**
     * A function to make all the items unfocusable except the opened modaal
     */
    disableBackgroundItemFocusing() {
        const modalContainer = document.querySelector( '.modaal-container' );

        // Collect each focusable element inside the modal.
        const focusableElements = modalContainer.querySelectorAll(
            'a[href], button:not([disabled]), [tabindex="0"]'
        );

        // Set first and last focusable element as class parameters.
        // Set focus to the first focusable element.
        if ( focusableElements.length ) {
            focusableElements.first = focusableElements[ 0 ];
            focusableElements.last = focusableElements[ focusableElements.length - 1 ];
            focusableElements.first.focus();

            // Bind modal focus loop handler to document when modal is opened.
            // If pressed key was 'Tab', call tab handling method.
            document.addEventListener( 'keydown', ( event ) => {
                const e = event || window.event;
                if ( e.keyCode === 9 ) {
                    // If shift + tab pushed.
                    if ( e.shiftKey ) {
                        // Focus the last element if focus was on the first element.
                        if ( focusableElements.first === document.activeElement ) {
                            e.preventDefault();
                            focusableElements.last.focus();
                        }
                    }
                    else if ( focusableElements.last === document.activeElement ) {
                        e.preventDefault();
                        focusableElements.first.focus();
                    }
                }
            }
            );
        }
    }
}
