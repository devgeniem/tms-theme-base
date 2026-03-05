/**
 * Google Translate Dropdown JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery;

/**
 * Class GtranslateDropdown
 */
export default class GtranslateDropdown {

    constructor() {
        this.isGoogleLoaded = false;
        this.eventsAttached = false;
    }

    /**
     * Toggle gtranslate dropdown
     *
     * @param {Event} event - Click event
     * @return {void}
     */
    toggleGtranslateDropdown( event ) {
        event.preventDefault();
        const $dropdown = $( '#js-gtranslate-dropdown' );
        const $trigger = $( event.currentTarget );

        if ( $dropdown.hasClass( 'is-hidden' ) ) {
            $dropdown.removeClass( 'is-hidden' );
            $trigger.attr( 'aria-expanded', 'true' );
        }
        else {
            $dropdown.addClass( 'is-hidden' );
            $trigger.attr( 'aria-expanded', 'false' );
        }
    }

    /**
     * Close gtranslate dropdown when clicking outside
     *
     * @param {Event} event - Click event
     * @return {void}
     */
    closeGtranslateOnOutsideClick( event ) {
        const $dropdown = $( '#js-gtranslate-dropdown' );
        const $wrapper = $( '.gtranslate-wrapper' );

        if ( ! $dropdown.hasClass( 'is-hidden' )
            && ! $wrapper.is( event.target )
            && $wrapper.has( event.target ).length === 0 ) {
            $dropdown.addClass( 'is-hidden' );
            $( '.gtranslate-trigger' ).attr( 'aria-expanded', 'false' );
        }
    }

    /**
     * Handle ESC key to close dropdown
     *
     * @param {Event} event - Keydown event
     * @return {void}
     */
    handleEscKey( event ) {
        if ( event.key === 'Escape' || event.keyCode === 27 ) {
            const $dropdown = $( '#js-gtranslate-dropdown' );
            if ( ! $dropdown.hasClass( 'is-hidden' ) ) {
                $dropdown.addClass( 'is-hidden' );
                $( '.gtranslate-trigger' ).attr( 'aria-expanded', 'false' ).focus();
            }
        }
    }

    /**
     * Load Google Translate API
     *
     * @return {void}
     */
    loadGoogleTranslateAPI() {
        if ( this.isGoogleLoaded || document.querySelector( 'script[src*="translate.google.com"]' ) ) {
            return;
        }

        const script = document.createElement( 'script' );
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        document.head.appendChild( script );
        this.isGoogleLoaded = true;

        // Define the callback function globally
        window.googleTranslateElementInit = this.initGoogleTranslate.bind( this );
    }

    /**
     * Initialize Google Translate
     *
     * @return {void}
     */
    initGoogleTranslate() {
        const container = document.getElementById( 'google_translate_element_custom' );

        // eslint-disable-next-line no-undef
        if ( ! container || typeof google === 'undefined' || ! google.translate ) {
            return;
        }

        // Get current language from data attribute or default to 'fi'
        const currentLang = container.dataset.lang || 'fi';

        // eslint-disable-next-line no-undef
        new google.translate.TranslateElement( {
            pageLanguage: currentLang,
            autoDisplay: false,
        }, 'google_translate_element_custom' );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        // Only attach events once and if elements exist
        if ( ! this.eventsAttached && $( '.gtranslate-trigger' ).length ) {
            // Handle gtranslate dropdown toggle
            $( '.gtranslate-trigger' ).on( 'click', this.toggleGtranslateDropdown.bind( this ) );

            // Close gtranslate dropdown when clicking outside
            $( document ).on( 'click', this.closeGtranslateOnOutsideClick.bind( this ) );

            // Handle ESC key
            $( document ).on( 'keydown', this.handleEscKey.bind( this ) );

            this.eventsAttached = true;
        }

        // Check if Google Translate container exists and load API
        if ( $( '#google_translate_element_custom' ).length ) {
            this.loadGoogleTranslateAPI();
        }
    }
}
