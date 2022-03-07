/**
 * Accordion JS controller.
 */

import Indicate from 'indicate';

/**
 * Class Accordion
 */
export default class Accordion {

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.mainContainer = document.querySelectorAll( '.bulmally-accordion' );

        if ( this.mainContainer ) {

            this.dropdownTogglers = document.querySelectorAll( '.accordion__title-button' );
            this.dropdowns = document.querySelectorAll( '.accordion__content' );

            // Hide all toggleable elements with JS.
            for ( let i = 0; i < this.dropdowns.length; i++ ) {

                if ( ! this.dropdowns[ i ].classList.contains( 'js-active-phase' ) ) {
                    this.dropdowns[ i ].classList.add( 'is-hidden' );
                }
            }
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
                this.dropdownTogglers[ i ].addEventListener(
                    'click',
                    () => this.toggleDropdown( this.dropdownTogglers[ i ] )
                );
            }
        }
    }

    /**
     * Toggles a dropdown content visibility.
     *
     * @param {HTMLButtonElement} clickedToggler The toggler button that was clicked.
     *
     * @return {void}
     */
    toggleDropdown( clickedToggler ) {
        const containerId = clickedToggler.getAttribute( 'aria-controls' );
        const dropDownContent = document.querySelector( `#${ containerId }` );

        this.toggleAriaExpanded( clickedToggler );
        dropDownContent.classList.toggle( 'is-hidden' );

        if ( ! clickedToggler.classList.contains( 'is-hidden' )
        && ! clickedToggler.classList.contains( 'accordion--table-initialized' ) ) {

            const accordionTables = dropDownContent.getElementsByTagName( 'table' );

            if ( accordionTables.length > 0 ) {
                new Indicate( accordionTables, { arrows: true } );

                clickedToggler.classList.add( 'accordion--table-initialized' );
            }
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
        const ariaExpandedState = toggler.getAttribute( 'aria-expanded' ) === 'false' ? true : false;
        toggler.setAttribute( 'aria-expanded', ariaExpandedState );

        if ( toggler.classList.contains( 'active-accordion' ) ) {
            toggler.classList.remove( 'active-accordion' );
        }
        else {
            toggler.classList.add( 'active-accordion' );
        }

    }

    /**
     * Add an unique identifier to each accordion.
     *
     * @return {void}
     */
    separateAccordions() {

        // A number to start the accordion separation from
        let separatorNo = 0;

        // Go through each dropdown toggler and add a number from separatorNo to
        // separate the id and the ariaControls for each button
        this.dropdownTogglers.forEach( ( element ) => {
            let id = element.id;
            let ariaControls = element.getAttribute( 'aria-controls' );

            id = id + separatorNo;
            ariaControls = ariaControls + separatorNo;

            element.setAttribute( 'id', id );
            element.setAttribute( 'aria-controls', ariaControls );

            separatorNo++;
        } );

        // To start the counting from the beginning, we want to match the togglers to content
        separatorNo = 0;

        // Go through each dropdown and add a number from separatorNo to
        // separate the ids
        this.dropdowns.forEach( ( element ) => {
            let id = element.id;
            id = id + separatorNo;

            element.setAttribute( 'id', id );
            separatorNo++;
        } );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.cache();
        this.events();
        this.separateAccordions();
    }
}
