/**
 * Copyright (c) 2021. Geniem Oy
 *
 * SlideDeck controller.
 *
 * This class controls the gallery and slider functionalilty.
 */

import Common from './common';

/**
 * Export the class reference.
 */
export default class SlideDeck {

    /**
     * Class constructor is for binding class properties.
     *
     * @param {Object} set Settings object. Possible settings:
     *                     id <string> ID of the carousel wrapper element (required).
     */
    constructor( set ) {

        // Some variables for the instance of the carousel
        this.slideDeck = undefined;
        this.slides = undefined;
        this.index = undefined;
        this.setFocus = undefined;
        this.arrowKeys = undefined;
        this.announceSlide = 'false';

        // Make settings available to all functions
        this.settings = set;

        this.init();
    }

    /**
     * Initialization for the carousel
     *
     * @return {void}
     */
    init() {

        // Select the element and the individual slides
        this.slideDeck = document.getElementById( this.settings.id );
        this.slides = this.slideDeck.querySelectorAll( '.slide' );
        let nextText = this.settings.next || 'Seuraava';
        let prevText = this.settings.prev || 'Edellinen';

        const buttonClass = this.settings.buttonClass || 'gallery-button button is-primary';

        const useIcons = ( this.settings.useIcons !== 'false' && this.settings.length > 0 ) || true;
        const nextIcon = this.settings.nextIcon || 'chevron-right';
        const prevIcon = this.settings.prevIcon || 'chevron-left';

        this.slideDeck.className = 'active slide-deck';

        // Add the previous/next controls if there's more than one image in the gallery
        if ( this.slides.length > 1 ) {
            const controls = document.createElement( 'ul' );

            controls.className = 'gallery-controls';

            if ( useIcons ) {
                prevText = `${ Common.makeIcon( prevIcon ) } <span class="is-sr-only">${ prevText }</span>`;
                nextText = `${ Common.makeIcon( nextIcon ) } <span class="is-sr-only">${ nextText }</span>`;
            }

            controls.innerHTML
                = `<li>
                     <button type="button" class="${ buttonClass } button--prev">${ prevText }</button>
                 </li>
                 <li>
                     <button type="button" class="${ buttonClass } button--next">${ nextText }</button>
                 </li>`;

            controls.querySelector( '.button--prev' )
                .addEventListener( 'click', () => {
                    this.prevSlide();
                } );
            controls.querySelector( '.button--next' )
                .addEventListener( 'click', () => {
                    this.nextSlide();
                } );

            this.slideDeck.appendChild( controls );
        }

        // Set the index (=current slide) to 0 – the first slide
        this.index = 0;
        this.setSlides( this.index );

        // Bind next/prev handlers to arrow keys.
        document.addEventListener( 'keydown', ( event = undefined ) => {
            const e = event || window.event;

            // Left arrow key
            if ( this.arrowKeys && e.keyCode === 37 ) {
                this.prevSlide( true );
            }

            // Right arrow key
            if ( this.arrowKeys && e.keyCode === 39 ) {
                this.nextSlide( true );
            }
        } );
    }

    /**
     * Function to set a slide the current slide
     *
     * @param {number|string}  newCurrent New Current slide.
     * @param {boolean}        focus      Focus on the element.
     * @param {string}         transition Transition to use.
     * @param {boolean|string} keyboard   To see if the event was triggered when navigating with keyboard
     * @return {void}
     */
    setSlides(
        newCurrent,
        focus = undefined,
        transition = undefined,
        keyboard = undefined
    ) {
        // Both, focus and transition are optional parameters.
        // focus denotes if the focus should be set after the carousel advanced to slide number newCurrent.
        // transition denotes if the transition is going into the next or previous direction.
        // Here defaults are set:
        this.setFocus = typeof focus !== 'undefined' ? focus : false;
        transition = typeof transition !== 'undefined' ? transition : 'none';

        newCurrent = parseFloat( newCurrent );

        const length = this.slides.length;
        let newNext = newCurrent + 1;
        let newPrev = newCurrent - 1;

        // If the next slide number is equal to the length,
        // the next slide should be the first one of the this.slides.
        // If the previous slide number is less than 0.
        // the previous slide is the last of the this.slides.
        if ( newNext === length ) {
            newNext = 0;
        }
        else if ( newPrev < 0 ) {
            newPrev = length - 1;
        }

        // Reset slide classes
        for ( let i = this.slides.length - 1; i >= 0; i-- ) {
            this.slides[ i ].className = 'slide';
            this.slides[ i ].removeAttribute( 'aria-live' );

            // Force stop any videos by removing the src attribute
            this.slides[ i ].iframe = this.slides[ i ].querySelector( 'iframe' );
            if ( this.slides[ i ].iframe !== null ) {
                this.slides[ i ].iframe.removeAttribute( 'src' );
            }
        }

        // Add classes to the previous, next and current slide
        if (
            ( ! Common.empty( this.slides[ newNext ] ) )
            && ( ! Common.empty( this.slides[ newPrev ] ) )
        ) {
            this.slides[ newNext ].className = 'next slide';
            if ( transition === 'next' ) {
                this.slides[ newNext ].className = 'next slide';
            }

            this.slides[ newPrev ].className = 'prev slide';
            if ( transition === 'prev' ) {
                this.slides[ newPrev ].className = 'prev slide';
            }
        }

        this.slides[ newCurrent ].className = 'current slide';

        // Reload videos
        if ( this.slides[ newCurrent ].iframe !== null ) {

            this.slides[ newCurrent ].iframe.setAttribute(
                'src',
                this.slides[ newCurrent ].iframe.dataset.src
            );
        }

        if ( this.announceSlide ) {
            this.slides[ newCurrent ].setAttribute( 'aria-live', 'polite' );

            setTimeout( () => {
                this.slides[ newCurrent ].removeAttribute( 'aria-live' );
                this.announceSlide = false;
            }, 100 );
        }

        // Set the global index to the new current value
        this.index = newCurrent;

        // Focus on the next image immediately if arrow keys were used
        if ( keyboard || keyboard === 'true' ) {
            const currentGalleryImage = this.slides[ newCurrent ]
                .getElementsByClassName( 'js-gallery-image' );
            currentGalleryImage[ 0 ].focus();
        }
    }

    /**
     * Function to advance to the next slide
     *
     * @param {boolean} keyboard To see if the event was triggered when navigating with keyboard
     * @return {void}
     */
    nextSlide( keyboard = undefined ) {
        const length = this.slides.length;
        let newCurrent = this.index + 1;

        if ( newCurrent === length ) {
            newCurrent = 0;
        }

        this.announceSlide = true;

        // If we advance to the next slide, the previous needs to be
        // visible to the user, so the third parameter is 'prev', not
        // next.
        this.setSlides( newCurrent, false, 'prev', keyboard );
    }

    /**
     * Function to advance to the previous slide
     *
     * @param {boolean} keyboard To see if the event was triggered when navigating with keyboard
     * @return {void}
     */
    prevSlide( keyboard = undefined ) {
        const length = this.slides.length;
        let newCurrent = this.index - 1;

        if ( newCurrent < 0 ) {
            newCurrent = length - 1;
        }

        this.announceSlide = true;

        // If we advance to the previous slide, the next needs to be
        // visible to the user, so the third parameter is 'next', not
        // prev.
        this.setSlides( newCurrent, false, 'prev', keyboard );
    }
}
