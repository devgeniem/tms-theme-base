/**
 * Common JS controller.
 *
 * Use this class to run scripts globally and to provide
 * modular helper functions for other scripts classes.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery;

/**
 * Export the class reference.
 */
export default class Common {
    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        // The accessible outline style container.
        this.focusStyleContainer = document.getElementById( 'accessible-outline' );

        // Scroll to links. Used for smooth-scroll links.
        this.$scrollToLink = $( '.js-scroll-to, .js-scroll-children a' );
    }

    /**
     * All common events
     *
     * @return {void}
     */
    events() {
        // Add the outline nullifying style to the
        // container in the document head when using the mouse.
        document.body.addEventListener( 'mousedown', () => {
            this.nullifyOutline();
        } );

        // Add the outline nullifying style to the
        // container in the document head on touchevents.
        document.body.addEventListener( 'touchstart', () => {
            this.nullifyOutline();
        } );

        // Remove the outline nullifying style from the
        // container in the document head when using the keyboard.
        document.body.addEventListener( 'keydown', () => {
            if ( this.focusStyleContainer ) {
                this.focusStyleContainer.innerHTML = '';
            }
        } );

        // Scroll to element on page. Will act as normal link if no element with the given ID (href of link) exists.
        // Because of the "normal link fallback" we are not using the global event listener here.
        this.$scrollToLink.click( function( e ) {
            const trigger = this;

            // Get ID of element to scroll to
            const targetId = $( e.currentTarget ).attr( 'href' );
            const $targetElement = $( targetId );

            // Return early if no element with given ID exists.
            if ( ! $targetElement.length ) {
                return;
            }

            e.preventDefault();

            // Scroll to element.
            const newPosition = $targetElement.offset();
            $( 'html, body' ).stop().animate( { scrollTop: newPosition.top }, 500, function() {
                // Trigger a change to make sure the url is updated and focus changes properly.
                // @link https://codepen.io/ppscvalentin/pen/JNNBzQ
                window.location.href = trigger.href;
            } );
        } );
    }

    /**
     * Add nullifying outline CSS rules to focusStyleContainer.
     *
     * @return {void}
     */
    nullifyOutline() {
        if ( this.focusStyleContainer ) {
            this.focusStyleContainer.innerHTML = '*:focus{outline:none!important;}';
        }
    }

    /**
     * Fallback for object-fit (needed for IE browsers that are older than Edge 16).
     *
     * @return {void}
     */
    objectFitFallback() {
        if ( ! ( 'objectFit' in document.documentElement.style ) ) {
            // Get all object fit containers
            const containers = Common.$( '.js-object-fit-container' );

            // Loop containers nodelist with Internet Explorer compatible way to use Array.prototype.forEach for iteration.
            Array.prototype.forEach.call( containers, ( container ) => {
                // Get object fit image element
                const img = Common.$1( '.js-object-fit-img', container );

                if ( img ) {
                    // Get object fit image url
                    const imgUrl = img.getAttribute( 'src' );

                    // Add class to container
                    container.classList.add( 'js-object-fit-fallback' );

                    // Add image as bg to container
                    container.style.backgroundImage = 'url(' + imgUrl + ')';
                }
            } );
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.cache();
        this.events();
        this.objectFitFallback();

        // Hyphenation is disabled because of JS errors caused by the used library
        // this.hyphenateElements();
    }

    /**
     * Toggles the state of an elements aria-hidden attribute. If the element does not have an aria-hidden attribute,
     * one will be created and set to false.
     *
     * @param {Object} element The nodelist containing the elements
     * @return {void}
     */
    static toggleAriaHidden( element ) {
        const ariaExpandedState = element.getAttribute( 'aria-hidden' ) === 'false';
        element.setAttribute( 'aria-hidden', ariaExpandedState );
    }

    /**
     * Adds classname from each element in a nodelist or array of elements.
     *
     * @param {Object} elements  The nodelist containing the elements
     * @param {string} className The classlist to remove
     * @return {void}
     */
    static addClassToAll( elements, className ) {
        // Return early if missing required attributes.
        if ( ! elements || ! className ) {
            return;
        }

        Array.from( elements ).forEach( ( element ) => {
            element.classList.add( className );
        } );
    }

    /**
     * Removes classname from each element in a nodelist or array of elements.
     *
     * @param {Object} elements  The nodelist containing the elements
     * @param {string} className The classlist to remove
     * @return {void}
     */
    static removeClassFromAll( elements, className ) {
        // Return early if missing required attributes.
        if ( ! elements || ! className ) {
            return;
        }

        Array.from( elements ).forEach( ( element ) => {
            element.classList.remove( className );
        } );
    }

    /**
     * Set attribute on each element in a nodelist or array of elements.
     *
     * @param {Object} elements  The nodelist or array containing the elements
     * @param {string} attribute The attribut to change
     * @param {string} value     The value of the attribute
     * @return {void}
     */
    static setAttrOnAll( elements, attribute, value ) {
        // // Return early if missing required attributes.
        if ( ! elements || ! attribute || ! value ) {
            return;
        }

        Array.from( elements ).forEach( ( element ) => {
            element.setAttribute( attribute, value );
        } );
    }

    /**
     * Get all the siblings of an element. Optionally specify a classname to only get the elements with said class.
     *
     * @param {Object} element   The element whose siblings to find.
     * @param {string} classname Optional classname that siblings must match.
     * @return {Object}          Returns empty array if no matches are found; otherwise,
     *                           it returns all matching elements.
     */
    static siblings( element, classname ) {
        // Return empty array if missing required attributes
        if ( ! element ) {
            return [];
        }

        return Array.prototype.filter.call( element.parentNode.children,
            function( child ) {
                if ( classname ) {
                    if ( ! child.classList.contains( classname ) ) {
                        return false;
                    }
                }
                return child !== element;
            }
        );
    }

    /**
     * Select a list of matching child elements from an array of elements.
     *
     * @param {Object} elements A nodelist. Use each item in nodelist as context when finding the children.
     * @param {string} selector The query selector string.
     * @return {Object}   Returns empty array if no matches are found; otherwise, it returns all matching elements.
     */
    static childrenFromEach( elements, selector ) {
        // Return empty array if missing required attributes or elements empty
        if ( ! elements || ! selector || ! elements.length > 0 ) {
            return [];
        }

        const arrayOfNodes = Array.prototype.map.call( elements,
            function( element ) {
                // Get all matching elements.
                const matchingChildren = ( element ).querySelectorAll( selector );

                // Convert nodelist to array.
                return [ ...matchingChildren ];
            }
        );

        // Flatten the array
        // https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Array/Reduce?v=a
        const flattened = arrayOfNodes.reduce( ( a, b ) => [ ...a, ...b ] );
        return flattened;
    }

    /**
     * Offers safe way to stop a JS event.
     *
     * Example usage:
     * Theme.Common.stop(e);
     *
     * @param {Event|Object} e Event object.
     * @return {void}
     */
    static stop( e ) {
        e.preventDefault ? e.preventDefault() : ( e.returnValue = false );
    }

    /**
     * Select a list of matching elements, context is optional.
     *
     * @param {string}          selector The query selector string.
     * @param {Object|document} context  A query context object.
     * @return {Object}                   Returns empty nodelist if no matches are found; otherwise,
     *                                    it returns a nodelist of matching elements.
     */
    static $( selector, context = document ) {
        return ( context || document ).querySelectorAll( selector );
    }

    /**
     * Select the first match only, context is optional.
     *
     * @param {string} selector The query selector string.
     * @param {Object} context  A query context object.
     * @return {Object|null}     Returns null if no matches are found; otherwise, it returns the first matching element.
     */
    static $1( selector, context ) {
        return ( context || document ).querySelector( selector );
    }

    /**
     * Makes a button element
     *
     * @param {string} contents What to add inside the button element.
     * @param {string} classes  CSS Classes to add to the element.
     * @param {string} type     Type of the button, 'button' as default.
     *
     * @return {string} Button element HTML.
     */
    static makeButton( contents = '', classes = '', type = 'button' ) {
        const button = document.createElement( 'button' );

        if ( classes.length ) {
            button.className = classes;
        }

        if ( type.length ) {
            button.type = type;
        }

        if ( contents.length ) {
            button.insertAdjacentHTML( 'afterbegin', contents );
        }

        return button.outerHTML;
    }

    /**
     * Generates a svg container that uses icon with an ID.
     *
     * @param {string} icon    Icon ID.
     * @param {string} classes CSS Classes to add to the element.
     *
     * @return {string} Icon HTML String.
     */
    static makeIcon( icon = '', classes = '' ) {
        let iconClass = 'icon';

        if ( classes.length ) {
            iconClass += ` ${ classes }`;
        }

        return `<svg class="${ iconClass }"><use xlink:href="#icon-${ icon }" /></svg>`;
    }

    /**
     * Helper function for removing classes
     *
     * @param {HTMLElement} el        HTML element
     * @param {string}      className Class name
     * @return {void}
     */
    static removeClass( el, className ) {
        return el.classList.remove( className );
    }

    /**
     * Helper function for detecting if an element has a class
     *
     * @param {HTMLElement} el        HTML Element
     * @param {string}      className Class name
     * @return {Object} The element with a class
     */
    static hasClass( el, className ) {
        return el.classList.contains( className );
    }

    /**
     * This is a custom empty check for parameters.
     *
     * @param {*} param The checked element.
     * @return {boolean} True or false.
     */
    static empty( param ) {
        return typeof param === 'undefined'
            || param === ''
            || param === null
            || param === false
            || param.length === 0;
    }

    /**
     * This parses and returns a given URL parameter value.
     *
     * @param {string} name The name of the parameter
     *
     * @return {boolean|string} The parameter.
     */
    static getUrlParameter( name = '' ) {
        const paramName = name
            .replace( /[\[]/, '\\[' )
            .replace( /[\]]/, '\\]' );
        const regex = new RegExp( '[\\?&]' + paramName + '=([^&#]*)' );
        const results = regex.exec( location.search );

        return results === null
            ? false
            : decodeURIComponent(
                results[ 1 ].replace( /\+/g, ' ' )
            );
    }

    /**
     * Set a cookie.
     *
     * @param {string} name     Cookie name.
     * @param {string} value    Cookie value.
     * @param {number} duration Cookie duration in days.
     *
     * @return {void}
     */
    static setCookie( name, value, duration ) {
        const date = new Date();
        date.setTime( date.getTime() + ( duration * 24 * 60 * 60 * 1000 ) );

        document.cookie = `${ name }=${ value };expires${ date.toLocaleDateString() };path=/`;
    }

    /**
     * Check if cookie exists.
     *
     * @param {string} name Cookie name.
     *
     * @return {boolean} True if the cookie exists
     */
    static cookieExists( name ) {
        return document.cookie.split( ';' ).some( ( item ) => item.trim().startsWith( `${ name }=` ) );
    }

    /**
     * Hyphenate entry titles.
     *
     * @return {void}
     */
    hyphenateElements() {
        const selectors = [
            '.hyphenate',
            'h1', '.h1',
            'h2', '.h2',
            'h3', '.h3',
            'h4', '.h4',
            'h5', '.h5',
            'h6', '.h6',
        ];

        const hyphenationController = window.Theme.getController( 'Hyphenation' );

        selectors.forEach( ( selector ) => {
            hyphenationController.hyphenate( selector );
        } );
    }
}
