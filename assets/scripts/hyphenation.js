/**
 * A global hyphenation controller using the Hyphenopoly library.
 *
 * Copyright (c) 2021. Geniem Oy
 */

// Require Hyphenopoly
const hyphenopoly = require( 'hyphenopoly' );

// Use jQuery as $ within this file scope.
const $ = jQuery;

/**
 * The controller class.
 */
export default class Hyphenation {
    constructor() {
        if ( instance ) {
            return instance;
        }

        this.initialized = false;
        this.setup();
        this.initHyphenopoly();

        const instance = this;
    }

    setup() {
        // This is eventually replaced with a soft hyphen.
        this.initialHyphenCharacter = '˽';
        // Get the assets URI from theme data.
        this.assetsUri = window.themeData.assetsUri + '/assets/dist/hyphenopoly/';
    }

    /**
     * Initializes the Hyphenopoly library for current language.
     *
     * @return {hyphenopoly} Hyphenopoly hyphenation library
     */
    initHyphenopoly() {
        if ( this.initialized ) {
            return this.hyphenator;
        }

        this.lang = document.documentElement.getAttribute( 'lang' ).toLowerCase();

        const promise = hyphenopoly.config( {
            'require': [ this.lang ],
            'hyphen': this.initialHyphenCharacter,
            'paths': {
                'maindir': this.assetsUri,
                'patterndir': this.assetsUri,
            },
            'loader': 'https',
        } );

        return promise.then( ( hyphenator ) => {
            this.initialized = true;
            this.hyphenator = hyphenator;

            return hyphenator;
        } );
    }

    /**
     * A method for hyphenating texts in elements. Automatically uses
     * the language defined for the HTML document.
     *
     * @param {string} selector A selector for jQuery.
     * @return {boolean} True on success, false on failure.
     */
    hyphenate( selector ) {
        if ( typeof hyphenopoly === 'undefined' ) {
            console.error( 'Unable to hyphenate. Hyphenopoly is missing.' ); // eslint-disable-line no-console
            return false;
        }

        this.initHyphenopoly().then( ( h ) => {
            this.setup();
            // Get elements and bail if none is found.
            const elements = $( selector );
            if ( elements.length === 0 ) {
                return false;
            }

            // Initialize a hyphenator for current language
            // and hyphenate elements once it's ready.
            try {
                const regExp = new RegExp( this.initialHyphenCharacter, 'g' );

                elements.each( ( idx, node ) => {
                    const element = $( node );

                    if (
                        typeof element.innerHTML === 'undefined'
                        || element.innerHTML.length < 1
                        || element.hasClass( 'no-hyphens' )
                    ) {
                        return;
                    }

                    element.innerHTML = h( element.innerHTML ).replace( regExp, '­' );
                } );
            }
            catch ( e ) {
                console.error( 'An error occurred while hyphenating.', e ); // eslint-disable-line no-console
                return false;
            }

            return true;
        } );
    }
}
