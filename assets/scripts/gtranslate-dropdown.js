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
        this.isGoogleLoadInProgress = false;
        this.isGoogleInitialized = false;
        this.eventsAttached = false;
        this.gtranslateCheckRetries = 0;
        this.gtranslateCheckMaxRetries = 3;
        this.gtranslateCheckDelay = 1500;
        this.cookiebotEventsBound = false;
    }

    /**
     * Check if Cookiebot consent allows loading Google Translate
     *
     * @return {boolean} True when consent permits loading Google Translate.
     */
    hasConsentForGtranslate() {
        if ( typeof window.Cookiebot === 'undefined' ) {
            return true;
        }

        if ( ! window.Cookiebot.consent ) {
            return false;
        }

        return Boolean( window.Cookiebot.consent.preferences );
    }

    /**
     * Toggle gtranslate text visibility
     *
     * @param {boolean} isVisible - Show or hide non-cookie paragraphs
     * @return {void}
     */
    setDropdownVisibility( isVisible ) {
        const $dropdownContent = $( '.gtranslate-dropdown__content' );
        const $cookieTextContainer = $( '.gtranslate-cookie-text-container' );
        const $translateContainer = $( '#google_translate_element_custom' );

        if ( ! $dropdownContent.length ) {
            return;
        }

        const paragraphs = $dropdownContent.find( 'p:not(.gtranslate-cookie-text)' );

        if ( isVisible ) {
            paragraphs.removeClass( 'is-hidden' );
            $cookieTextContainer.addClass( 'is-hidden' );
            $translateContainer.removeClass( 'is-hidden' );
        }
        else {
            paragraphs.addClass( 'is-hidden' );
            $cookieTextContainer.removeClass( 'is-hidden' );
            $translateContainer.addClass( 'is-hidden' );
        }
    }

    /**
     * Attach dropdown event handlers once
     *
     * @return {void}
     */
    attachEventsIfNeeded() {
        if ( this.eventsAttached || ! $( '.gtranslate-trigger' ).length ) {
            return;
        }

        // Handle gtranslate dropdown toggle
        $( '.gtranslate-trigger' ).on( 'click', this.toggleGtranslateDropdown.bind( this ) );

        // Close gtranslate dropdown when clicking outside
        $( document ).on( 'click', this.closeGtranslateOnOutsideClick.bind( this ) );

        // Handle ESC key
        $( document ).on( 'keydown', this.handleEscKey.bind( this ) );

        this.eventsAttached = true;
    }

    /**
     * Handle consent-dependent loading and visibility
     *
     * @return {void}
     */
    handleConsentFlow() {
        const hasConsent = this.hasConsentForGtranslate();

        this.attachEventsIfNeeded();
        this.setDropdownVisibility( hasConsent );

        if ( hasConsent ) {
            this.loadGoogleTranslateAPI();
            this.initGoogleTranslate();
        }
    }

    /**
     * Bind Cookiebot consent events once
     *
     * @return {void}
     */
    bindCookiebotEvents() {
        if ( this.cookiebotEventsBound ) {
            return;
        }

        window.addEventListener( 'CookiebotOnConsentReady', this.handleConsentFlow.bind( this ) );
        window.addEventListener( 'CookiebotOnAccept', this.handleConsentFlow.bind( this ) );
        window.addEventListener( 'CookiebotOnDecline', this.handleConsentFlow.bind( this ) );

        this.cookiebotEventsBound = true;
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
        // The callback must exist before script execution.
        window.googleTranslateElementInit = this.initGoogleTranslate.bind( this );

        // eslint-disable-next-line no-undef
        if ( typeof google !== 'undefined' && google.translate ) {
            this.isGoogleLoaded = true;
            this.initGoogleTranslate();
            return;
        }

        if ( this.isGoogleLoadInProgress || this.isGoogleLoaded ) {
            return;
        }

        const existingScript = document.querySelector( 'script[src*="translate.google.com/translate_a/element.js"]' );

        if ( existingScript ) {
            return;
        }

        this.isGoogleLoadInProgress = true;
        this.gtranslateCheckRetries = 0;

        const script = document.createElement( 'script' );
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        script.defer = true;

        script.addEventListener( 'load', () => {
            this.isGoogleLoadInProgress = false;
            this.isGoogleLoaded = true;

            // Init explicitly in case callback was skipped by cache/timing quirks.
            this.initGoogleTranslate();

            setTimeout( () => {
                this.checkGoogleTranslateLoaded();
            }, this.gtranslateCheckDelay );
        } );

        script.addEventListener( 'error', () => {
            this.isGoogleLoadInProgress = false;
            this.isGoogleLoaded = false;
            this.gtranslateCheckRetries = 0;
        } );

        document.head.appendChild( script );

        // Check if the script loaded successfully after a short delay
        setTimeout( () => {
            this.checkGoogleTranslateLoaded();
        }, this.gtranslateCheckDelay );
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

        // eslint-disable-next-line no-undef
        if ( typeof google.translate.TranslateElement !== 'function' ) {
            if ( this.gtranslateCheckRetries < this.gtranslateCheckMaxRetries ) {
                this.gtranslateCheckRetries += 1;
                setTimeout( () => {
                    this.initGoogleTranslate();
                }, 300 );
            }
            return;
        }

        if ( this.isGoogleInitialized || container.children.length > 0 ) {
            this.isGoogleInitialized = true;
            return;
        }

        // Get current language from data attribute or default to 'fi'
        const currentLang = container.dataset.lang || 'fi';

        // eslint-disable-next-line no-undef
        new google.translate.TranslateElement( {
            pageLanguage: currentLang,
            autoDisplay: false,
        }, 'google_translate_element_custom' );

        this.isGoogleInitialized = true;
    }

    /**
     * Check if Google Translate loaded successfully, show cookie message if not
     *
     * @return {void}
     */
    checkGoogleTranslateLoaded() {
        const container = document.getElementById( 'google_translate_element_custom' );

        if ( container ) {
            // Check if the container has been populated with Google Translate content
            const hasGoogleContent = container.children.length > 0;

            if ( ! hasGoogleContent && this.gtranslateCheckRetries < this.gtranslateCheckMaxRetries ) {
                this.gtranslateCheckRetries += 1;
                setTimeout( () => {
                    this.checkGoogleTranslateLoaded();
                }, this.gtranslateCheckDelay );
                return;
            }

            // If no Google Translate content, show cookie disabled message and hide other elements
            if ( ! hasGoogleContent ) {
                // Show the cookie message container
                const cookieTextContainer = document.querySelector( '.gtranslate-cookie-text-container' );
                if ( cookieTextContainer ) {
                    cookieTextContainer.classList.remove( 'is-hidden' );
                }

                // Hide other paragraph elements in the dropdown
                const dropdownContent = document.querySelector( '.gtranslate-dropdown__content' );
                if ( dropdownContent ) {
                    const paragraphs = dropdownContent.querySelectorAll( 'p:not(.gtranslate-cookie-text)' );
                    paragraphs.forEach( ( p ) => {
                        p.classList.add( 'is-hidden' );
                    } );
                }
            }
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        if ( ! $( '#google_translate_element_custom' ).length ) {
            return;
        }

        this.bindCookiebotEvents();
        this.loadGoogleTranslateAPI();
        this.handleConsentFlow();
    }
}
