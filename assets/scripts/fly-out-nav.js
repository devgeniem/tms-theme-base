/*
 *  Copyright (c) 2021. Geniem Oy
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars

/**
 * Class FlyOutNav
 */
export default class FlyOutNav {

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.navbarMenu = document.getElementById( 'js-navbar-menu' );

        if ( this.navbarMenu ) {
            this.dropdownTogglers = this.navbarMenu.querySelectorAll( '.dropdown-toggler' );
        }
    }

    /**
     * Add event listeners.
     *
     * @return {void}
     */
    events() {
        if ( this.dropdownTogglers ) {
            for ( let i = 0; i < this.dropdownTogglers.length; i++ ) {
                this.dropdownTogglers[ i ].addEventListener( 'click', ( event ) => this.toggleDropdown( event ) );
            }
        }

        const $triggerItem = $( '.fly-out-nav .navbar-item--trigger-only' );

        $triggerItem.on( 'click', this.dropdownLinkClick.bind( this ) );
        $( '.menu-item', $triggerItem ).on( 'click', this.dropdownChildLinkClick.bind( this ) );
    }

    /**
     * Handle dropdown click.
     *
     * @param {Event} event A click event.
     */
    dropdownLinkClick( event ) {
        event.preventDefault();

        const toggler = $( event.target ).next( '.dropdown-toggler' );

        if ( toggler.length > 0 ) {
            toggler.get( 0 ).click();
        }
    }

    /**
     * Handle dropdown child click.
     *
     * @param {Event} event A click event.
     */
    dropdownChildLinkClick( event ) {
        event.stopPropagation();
    }

    /**
     * Toggles a dropdown menu visibility.
     *
     * @param {Event} event A click event.
     *
     * @return {void}
     */
    toggleDropdown( event ) {
        event.stopPropagation();

        const target = event.currentTarget;
        const containerId = target.getAttribute( 'aria-controls' );
        const dropdownMenu = this.navbarMenu.querySelector( `#${ containerId }` );

        this.toggleAriaExpanded( target );
        dropdownMenu.classList.toggle( 'is-hidden-touch' );
        this.toggleAncestorActiveState( target, 'has-dropdown' );
    }

    /**
     * Set the 'is-active' state for an ancestor of an element
     * with the matching class name.
     *
     * @param {HTMLElement} child     A child element to start the search from.
     * @param {HTMLElement} className A target class name for the ancestor.
     *
     * @return {void}
     */
    toggleAncestorActiveState( child, className ) {
        let ancestor = child.parentNode;
        while ( ancestor ) {
            if ( ancestor.classList.contains( className ) ) {
                ancestor.classList.toggle( 'is-active' );
                return;
            }
            ancestor = ancestor.parentNode ? ancestor.parentNode : false;
        }
    }

    /**
     * Get the toggler's aria-expanded current state and set a new opposite state to it.
     * Also set the opened container's aria-hidden state to the new value's opposite.
     *
     * @param {HTMLElement} toggler The toggler element.
     *
     * @return {void}
     */
    toggleAriaExpanded( toggler ) {
        const ariaExpandedState = toggler.getAttribute( 'aria-expanded' ) === 'false';
        toggler.setAttribute( 'aria-expanded', ariaExpandedState );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.cache();
        this.events();
    }
}
