/**
 * Copyright (c) 2021. Geniem Oy
 * Modal JS controller.
 */
import '@accessible360/accessible-slick';
import SlideDeck from './slide-deck';
import Common from './common';
import { ESCAPE, TAB } from '@wordpress/keycodes';

// Use jQuery as $ within this file scope.
const $ = jQuery;

/**
 * Class Modal
 */
export default class Modal {

    /**
     * This method is run when a new instance of the class is created.
     */
    constructor() {
        this.lightBoxGallery = false;
    }

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.rootEl = document.documentElement;
        this.$modals = [];
        this.$modalButtons = document.querySelectorAll( '[data-modal-button]' );
        this.$modalCloses = document.querySelectorAll(
            '.modal-background, .modal-close, .modal-card-head .delete, .modal-card-foot .button, .modal-close-button'
        );

        // Strings
        this.galleries = document.getElementsByClassName( 'js-image-gallery' );

        if ( ! Common.empty( this.galleries ) ) {
            this.nextImage = this.galleries[ 0 ].getAttribute( 'data-next' );
            this.prevImage = this.galleries[ 0 ].getAttribute( 'data-previous' );
            this.nextIcon = this.galleries[ 0 ].getAttribute( 'data-next-icon' );
            this.prevIcon = this.galleries[ 0 ].getAttribute( 'data-previous-icon' );
        }
    }

    /**
     * Add event listeners.
     *
     * @return {void}
     */
    events() {
        // Bind handlers to each modal open button.
        this.eventsModalOpenRegistration();

        // Bind handlers to each modal close button.
        if ( this.$modalCloses.length > 0 ) {
            this.$modalCloses.forEach( ( button ) => {
                button.addEventListener( 'click', () => {
                    this.closeModals();
                } );
            } );
        }

        // Bind modal closing handler to ESC key.
        document.addEventListener( 'keydown', ( event ) => {
            const { keyCode } = event;
            if ( keyCode === ESCAPE ) {
                this.closeModals();
            }
        } );
    }

    /**
     * Events: Modal Open Registration.
     *
     * @return {void}
     */
    eventsModalOpenRegistration() {
        if ( this.$modalButtons.length < 1 ) {
            return;
        }

        this.$modalButtons.forEach( ( button ) => {

            const modal = document.getElementById( button.getAttribute( 'aria-controls' ) );
            modal.isOpened = 0;
            modal.gallery = false;

            // Add this modal to list of modals if it hasn't been added yet.
            // This ensures that there can be more than one trigger for the same modal.
            if ( this.$modals.length === 0 || ! this.$modals.some( ( element ) => element.id === modal.id ) ) {
                this.$modals.push( modal );
            }

            button.addEventListener( 'click', ( e ) => {
                e.preventDefault();

                const clickedButton = e.currentTarget;
                modal.openingButton = clickedButton;

                if ( clickedButton.hasAttribute( 'href' ) && modal.gallery === false ) {
                    this.initializeGallery( modal );
                    modal.galleryInitialized = true;
                    this.focusGallerySlide( modal );
                }
                else if ( clickedButton.hasAttribute( 'href' ) ) {
                    this.focusGallerySlide( modal );
                }
                this.openModal( modal );
                this.toggleAriaExpanded( clickedButton );
            } );
        } );
    }

    /**
     * Initialize a modal.
     *
     * @param {HTMLElement} modal Modal HTML Element.
     * @return {void}
     */
    initializeGallery( modal ) {
        const openingElement = modal.openingButton;
        const currentTarget = openingElement.href;
        let currentIndex = 0;

        // Find all images related to this modal
        const thumbnails = document.querySelectorAll(
            '[data-gallery="' + openingElement.getAttribute( 'aria-controls' ) + '"]'
        );

        // Create wrapper HTML
        const slideDeckHTML = document.createElement( 'div' );
        slideDeckHTML.className = 'slide-deck';
        slideDeckHTML.id = modal.id + '-slide-deck';
        const slideDeckList = document.createElement( 'ul' );

        // Loop slides and add to slide deck
        // If slide URL == currentTarget, store index in currentIndex
        thumbnails.forEach( ( thumb, index ) => {
            const slide = document.createElement( 'li' );
            slide.className = 'slide';

            if ( thumb.classList.contains( 'video' ) ) {

                // Get youtube ID.
                const youTubeID = this.getYouTubeId( thumb.href );

                // Construct embed code
                slide.innerHTML = '<div class="image is-16by9">'
                    + '<iframe class="has-ratio" width="640" height="480" data-src="//www.youtube.com/embed/'
                    + youTubeID + '?enablejsapi=1" frameborder="0" allowfullscreen></iframe></div>';

            }
            else {

                // Create an img inside the slide with src of the original thumbnail
                // being linked to and alt of the nested thumbnail.
                const alt = thumb.querySelector( 'img' ).getAttribute( 'alt' );
                const caption = thumb.getAttribute( 'data-caption' ) || '';
                const author = thumb.getAttribute( 'data-author' ) || '';
                const title = thumb.getAttribute( 'data-image_title_and_artist' ) || '';

                const figImgAttrs = 'loading="lazy" tabindex="-1" class="js-gallery-image"';
                const figImg = `<img src="${ thumb.href }" alt="${ alt }" ${ figImgAttrs }>`;

                const capClasses = [ 'columns', 'image-caption', 'image-block__meta', 'pt-2', 'is-multiline' ];
                const capContents = [];

                if ( author.trim().length > 1 ) {
                    // eslint-disable-next-line max-len
                    const authorClasses = 'column is-12 is-2-desktop ml-6 has-text-right image-block__author-name has-text-small';
                    capContents.push( `<div class="${ authorClasses }">${ author }</div>` );
                    capClasses.push( 'is-reversed' );
                }
                if ( title.trim().length > 1 || caption.trim().length > 1 ) {
                    const capStrings = [
                        `<strong class="is-block has-text-white">${ title }</strong>`,
                        caption,
                    ];
                    capContents.push( `<div class="column keep-vertical-spacing">${ capStrings.join( ' ' ) }</div>` );
                }

                const figCap = capContents.length > 0
                    ? `<figcaption class="${ capClasses.join( ' ' ) }">${ capContents.join( '' ) }</figcaption>`
                    : '';

                slide.innerHTML = `<figure>${ figImg }${ figCap }</figure>`;
            }

            slideDeckList.appendChild( slide );

            if ( thumb.getAttribute( 'src' ) === currentTarget ) {
                currentIndex = index;
            }
        } );

        slideDeckHTML.appendChild( slideDeckList );

        // Add HTML inside modal
        modal.querySelector( '.modal-content' ).appendChild( slideDeckHTML );

        // create a new SlideDeck with ID of modal
        modal.gallery = new SlideDeck( {
            id: slideDeckHTML.id,
            slideNav: true,
            next: this.nextImage,
            prev: this.prevImage,
            useIcons: true,
            nextIcon: 'chevron-right',
            prevIcon: 'chevron-left',
            buttonClass: 'gallery-button button is-primary',
        } );

        // Set current using setSlide and currentIndex
        modal.gallery.setSlides( currentIndex );
    }

    /**
     * Focus a particular slide in an existing slidedeck.
     *
     * @param {HTMLElement} modal Gallery slide HTML Element.
     * @return {void}
     */
    focusGallerySlide( modal ) {
        if ( typeof modal.gallery === 'undefined' || typeof modal.gallery === 'boolean' ) {
            return;
        }

        const currentTarget = modal.openingButton.href;
        let currentIndex = 0;

        // If slide URL == currentTarget, store index in currentIndex
        modal.gallery.slides.forEach( ( slide, index ) => {
            const image = slide.querySelector( 'img' );
            if (
                ( image !== null && image.getAttribute( 'src' ) === currentTarget )
                || slide.querySelector( 'iframe' ) !== null
            ) {
                currentIndex = index;
            }
        } );

        modal.gallery.setSlides( currentIndex );
    }

    /**
     * This handles opening the modal that was associated with
     * the clicked modal opening button.
     *
     * @param {Element} modal The modal that is opened.
     * @return {void}
     */
    openModal( modal ) {
        this.rootEl.classList.add( 'is-clipped' );
        modal.classList.add( 'is-active' );
        modal.isOpened = 1;

        const slickSlider = $( modal ).find( '.image-carousel__items' );
        if ( slickSlider && slickSlider.hasClass( 'slick-initialized' ) ) {
            slickSlider.slick( 'refresh' );
        }

        // Collect each focusable element inside the modal.
        // eslint-disable-next-line max-len
        const focusableElements = modal.querySelectorAll( 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), [tabindex="0"]' );

        // Set first and last focusable element as class parameters.
        // Set focus to the first focusable element.
        if ( focusableElements.length ) {
            modal.focusableElements = focusableElements;
            modal.focusableElements.first = focusableElements[ 0 ];
            modal.focusableElements.last = focusableElements[ focusableElements.length - 1 ];
            modal.focusableElements.first.focus();

            // Bind modal focus loop handler to document when modal is opened.
            // If pressed key was 'Tab', call tab handling method.
            document.addEventListener( 'keydown', ( event ) => {
                const { keyCode } = event;
                if ( keyCode === TAB ) {
                    this.handleModalTabbing( event, modal );
                }

                if ( keyCode === ESCAPE ) {
                    this.closeModals();
                }
            } );
        }

        if ( typeof modal.gallery !== 'undefined' && typeof modal.gallery !== 'boolean' ) {

            // Activate arrow key binding
            modal.gallery.arrowKeys = true;
        }
    }

    /**
     * This closes all modals and sets focus back to the element that was used
     * to open the current visible modal.
     *
     * @return {void}
     */
    closeModals() {
        this.rootEl.classList.remove( 'is-clipped' );
        this.$modals.forEach( ( modal ) => {
            modal.classList.remove( 'is-active' );
            if ( modal.isOpened ) {
                modal.openingButton.focus();
                this.toggleAriaExpanded( modal.openingButton );
                modal.isOpened = 0;

                if ( typeof modal.gallery !== 'boolean' ) {

                    // Deactivate arrow key binding
                    modal.gallery.arrowKeys = false;
                }
            }
        } );
    }

    /**
     * This handles Tab key presses and loops focus back to the first
     * focusable element inside the modal. If a user navigates backwards
     * using shift + tab, the loop is handled properly to the opposite direction.
     *
     * @param {KeyboardEvent|Event} e     Key press event.
     * @param {Element}             modal The modal that is currently visible.
     * @return {void}
     */
    handleModalTabbing( e, modal ) {
        const { shiftKey } = e;
        // If shift + tab pushed.
        if ( shiftKey ) {

            // Focus the last element if focus was on the first element.
            if ( modal.focusableElements.first === document.activeElement ) {
                e.preventDefault();
                modal.focusableElements.last.focus();
            }
        }
        else if ( modal.focusableElements.last === document.activeElement ) {
            e.preventDefault();
            modal.focusableElements.first.focus();
        }
    }

    /**
     * Get the toggler aria-expanded current state and set a new opposite state to it.
     *
     * @param {HTMLElement} toggler The toggler element.
     * @return {void}
     */
    toggleAriaExpanded( toggler ) {
        const ariaExpandedState = toggler.getAttribute( 'aria-expanded' ) === 'false' ? 'true' : 'false';
        toggler.setAttribute( 'aria-expanded', ariaExpandedState );
    }

    /**
     * Get a YouTube ID from a YouTube URL.
     *
     * @param {string} url YouTube URL.
     * @return {Object} The Youtube ID
     */
    getYouTubeId( url ) {
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match( regExp );

        return ( match && match[ 2 ].length === 11 ) ? match[ 2 ] : null;
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
