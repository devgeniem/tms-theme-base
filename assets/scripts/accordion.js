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

            this.openAllButton = document.querySelectorAll( '.accordion__open-all' );
            this.closeAllButton = document.querySelectorAll( '.accordion__close-all' );
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

        if ( this.mainContainer ) {
            for ( let i = 0; i < this.mainContainer.length; i++ ) {
                this.openAllButton[ i ].addEventListener(
                    'click',
                    () => this.openAllDropdowns(
                        this.mainContainer[ i ],
                        this.openAllButton[ i ],
                        this.closeAllButton[ i ]
                    )
                );

                this.closeAllButton[ i ].addEventListener(
                    'click',
                    () => this.closeAllDropdowns(
                        this.mainContainer[ i ],
                        this.closeAllButton[ i ],
                        this.openAllButton[ i ]
                    )
                );

                if ( this.dropdownTogglers ) {
                    const togglers = this.mainContainer[ i ].getElementsByClassName( 'accordion__title-button' );

                    for ( let x = 0; x < togglers.length; x++ ) {
                        togglers[ x ].addEventListener(
                            'click',
                            () => this.updateButtonStates(
                                this.mainContainer[ i ],
                                this.openAllButton[ i ],
                                this.closeAllButton[ i ]
                            )
                        );
                    }
                }
            }
        }
    }

    /**
     * Opens all dropdowns.
     *
     * @param {HTMLDivElement}    mainContainer  Main container for accordion dropdowns.
     * @param {HTMLButtonElement} openAllButton  The open all -button that was clicked.
     * @param {HTMLButtonElement} closeAllButton The close all -button to be shown.
     *
     * @return {void}
     */
    openAllDropdowns( mainContainer, openAllButton, closeAllButton ) {
        const dropdowns = mainContainer.getElementsByClassName( 'accordion__title-button' );

        for ( let i = 0; i < dropdowns.length; i++ ) {
            const containerId = dropdowns[ i ].getAttribute( 'aria-controls' );
            const dropDownContent = document.querySelector( `#${ containerId }` );
            const textOpen = dropdowns[ i ].querySelector( '.icon-text--open' );
            const textClose = dropdowns[ i ].querySelector( '.icon-text--close' );

            textOpen.setAttribute( 'aria-hidden', 'true' );
            textClose.setAttribute( 'aria-hidden', 'false' );
            dropdowns[ i ].setAttribute( 'aria-expanded', 'true' );
            if ( ! dropdowns[ i ].classList.contains( 'active-accordion' ) ) {
                dropdowns[ i ].classList.add( 'active-accordion' );
            }

            dropDownContent.classList.remove( 'is-hidden' );

            if ( ! dropdowns[ i ].classList.contains( 'is-hidden' )
            && ! dropdowns[ i ].classList.contains( 'accordion--table-initialized' ) ) {

                const accordionTables = dropDownContent.getElementsByTagName( 'table' );

                if ( accordionTables.length > 0 ) {
                    new Indicate( accordionTables, { arrows: true } );

                    dropdowns[ i ].classList.add( 'accordion--table-initialized' );
                }
            }
        }

        closeAllButton.classList.remove( 'is-hidden' );
        openAllButton.classList.add( 'is-hidden' );
    }

    /**
     * Closes all dropdowns.
     *
     * @param {HTMLDivElement}    mainContainer  Main container for accordion dropdowns.
     * @param {HTMLButtonElement} closeAllButton The close all -button that was clicked.
     * @param {HTMLButtonElement} openAllButton  The open all -button to be shown.
     *
     * @return {void}
     */
    closeAllDropdowns( mainContainer, closeAllButton, openAllButton ) {
        const dropdowns = mainContainer.getElementsByClassName( 'accordion__title-button' );

        for ( let i = 0; i < dropdowns.length; i++ ) {
            const containerId = dropdowns[ i ].getAttribute( 'aria-controls' );
            const dropDownContent = document.querySelector( `#${ containerId }` );
            const textOpen = dropdowns[ i ].querySelector( '.icon-text--open' );
            const textClose = dropdowns[ i ].querySelector( '.icon-text--close' );

            textOpen.setAttribute( 'aria-hidden', 'false' );
            textClose.setAttribute( 'aria-hidden', 'true' );
            dropdowns[ i ].setAttribute( 'aria-expanded', 'false' );
            if ( dropdowns[ i ].classList.contains( 'active-accordion' ) ) {
                dropdowns[ i ].classList.remove( 'active-accordion' );
            }

            dropDownContent.classList.add( 'is-hidden' );

            if ( dropdowns[ i ].classList.contains( 'is-hidden' )
            && dropdowns[ i ].classList.contains( 'accordion--table-initialized' ) ) {

                const accordionTables = dropDownContent.getElementsByTagName( 'table' );

                if ( accordionTables.length > 0 ) {
                    new Indicate( accordionTables, { arrows: true } );

                    dropdowns[ i ].classList.remove( 'accordion--table-initialized' );
                }
            }
        }

        openAllButton.classList.remove( 'is-hidden' );
        closeAllButton.classList.add( 'is-hidden' );
    }

    /**
     * Updates "Close all" or "Open all" -button states depending on open accordion dropdowns.
     *
     * @param {HTMLDivElement}    mainContainer  Main container for accordion dropdowns.
     * @param {HTMLButtonElement} openAllButton  The open all -button.
     * @param {HTMLButtonElement} closeAllButton The close all -button.
     *
     * @return {void}
     */
    updateButtonStates( mainContainer, openAllButton, closeAllButton ) {
        const dropdowns = mainContainer.getElementsByClassName( 'accordion__title-button' );
        const openDropdowns = mainContainer.getElementsByClassName( 'active-accordion' );

        if ( openDropdowns.length === dropdowns.length ) {
            closeAllButton.classList.remove( 'is-hidden' );
            openAllButton.classList.add( 'is-hidden' );
        }
        else {
            openAllButton.classList.remove( 'is-hidden' );
            closeAllButton.classList.add( 'is-hidden' );
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
        const textOpen = clickedToggler.querySelector( '.icon-text--open' );
        const textClose = clickedToggler.querySelector( '.icon-text--close' );

        this.toggleAriaHidden( textOpen );
        this.toggleAriaHidden( textClose );
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
     * Get the icon-texts aria-hidden current state and set a new opposite state to it.
     *
     * @param {HTMLElement} iconText The icon-text element.
     *
     * @return {void}
     */
    toggleAriaHidden( iconText ) {
        const ariaHiddenState = iconText.getAttribute( 'aria-hidden' ) === 'false' ? true : false;
        iconText.setAttribute( 'aria-hidden', ariaHiddenState );
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
