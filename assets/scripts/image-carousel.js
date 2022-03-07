/**
 * Copyright (c) 2021. Geniem Oy
 */

import '@accessible360/accessible-slick';
import Common from './common';
import { LEFT, RIGHT } from '@wordpress/keycodes';

// Use jQuery as $ within this file scope.
const $ = jQuery;

export default class ImageCarousel {
    cache() {
        this.carousels = $( '.image-carousel' );
    }

    initCarousels() {
        // Translations are defined in models/strings.php,
        // and loaded to windows.s in lib/Assets.php.
        const translations = window.s.gallery || {
            next: 'Next',
            previous: 'Previous',
            close: 'Close',
            goto: 'Go to slide',
        };

        const icons = {
            prev: Common.makeIcon( 'chevron-left' ),
            next: Common.makeIcon( 'chevron-right' ),
        };

        const prevSrText = `<span class="is-sr-only">${ translations.previous }</span>`;
        const nextSrText = `<span class="is-sr-only">${ translations.next }</span>`;

        const arrowClass = 'button button--icon image-carousel__modal-control';

        const buttons = {
            prevArrow: Common.makeButton( icons.prev + prevSrText, `${ arrowClass } slick-prev` ),
            nextArrow: Common.makeButton( icons.next + nextSrText, `${ arrowClass } slick-next` ),
        };

        $( this.carousels ).each( ( n, element ) => {
            this.constructCarousel( element, buttons, translations );
        } );
    }

    /**
     * Constructs the carousel, or two if we have sync defined.
     *
     * @param {HTMLElement} container    Main carousel element.
     * @param {Object}      buttons      Buttons to use.
     * @param {Object}      translations Translations.
     * @return {*|jQuery|HTMLElement} Constructed main carousel.
     */
    constructCarousel( container = undefined, buttons = {}, translations = {} ) {
        const $container = $( container );
        const carousel = $container.find( '.image-carousel__items--primary' );
        const modalCarouselId = '#' + carousel.attr( 'data-slider-for' ) || false;

        const carouselOptions = {
            prevArrow: $container.find( '.slick-prev' ),
            nextArrow: $container.find( '.slick-next' ),
            customPaging( slider, i ) {
                const dotIcon = '<span class="slick-dot-icon" aria-hidden="true"></span>';
                const srLabel = `<span class="is-sr-only">${ translations.goto } ${ i + 1 }</span>`;
                return $( Common.makeButton( dotIcon + srLabel ) );
            },
            centerMode: true,
            centerPadding: '1rem',
            slidesToShow: 3,
            variableWidth: true,
            arrowsPlacement: 'afterSlides',
        };

        if ( modalCarouselId ) {
            // Add necessary things to the original carousel to support linking with another carousel.
            carouselOptions.regionLabel = translations.main_carousel;
            carouselOptions.asNavFor = modalCarouselId;

            const modalCarousel = $( modalCarouselId );

            // Start the modal carousel.
            modalCarousel.slick( {
                centerMode: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                asNavFor: '#' + modalCarousel.attr( 'data-slider-for' ),
                prevArrow: buttons.prevArrow,
                nextArrow: buttons.nextArrow,
                regionLabel: translations.modal_carousel,
                arrowsPlacement: 'afterSlides',
            } );

            // Bind next/prev handlers to arrow keys.
            document.addEventListener( 'keydown', ( event = undefined ) => {
                const e = event || window.event;
                const { keyCode } = e;

                // Left arrow key
                if ( keyCode === LEFT ) {
                    modalCarousel.slick( 'slickPrev' );
                }

                // Right arrow key
                if ( keyCode === RIGHT ) {
                    modalCarousel.slick( 'slickNext' );
                }
            } );

            modalCarousel.on( 'setPosition', ( event, slick ) => {
                //Make only the current slide focusable, for screenreaders
                $( slick.$slider ).find( '.slick-slide' ).attr( 'tabindex', '0' );
                $( slick.$slider ).find( '.slick-slide:not(.slick-current)' ).removeAttr( 'tabindex' );

                // Transalate Slick Slider stuff
                this.translateCarousels( translations );
            } );
        }

        // Start the main carousel.
        carousel.slick( carouselOptions );

        carousel.on( 'setPosition', ( event, slick ) => {
            // Make the centered image selectable, rest disabled.
            // This way user can't open the "wrong" image and get confused of the results.
            $( slick.$slider ).find( '.slick-slide button' ).removeAttr( 'disabled' );
            $( slick.$slider ).find( '.slick-slide:not(.slick-current) button' ).attr( 'disabled', '' );

            // Transalate Slick Slider stuff
            this.translateCarousels( translations );
        } );

        let allLoaded = true;

        carousel.find( 'img' ).each( ( idx, el ) => {
            if ( ! $( el ).prop( 'complete' ) ) {
                allLoaded = false;
            }
        } );

        if ( ! allLoaded ) {
            carousel.slick( 'refresh' );
        }

        return carousel;
    }

    translateCarousels( translations ) {
        $( '.slick-track' ).find( '.slick-slide' ).each( function() {
            const thisElem = $( this );
            let newStr = thisElem.attr( 'aria-label' ).replace( 'slide', translations.slide );

            if ( newStr.includes( 'centered' ) ) {
                newStr = newStr.replace( /\((.*?)\)/g, '' ).trim() + ' (' + translations.centered + ')';
                thisElem.attr( 'aria-label', newStr );
            }

            // Clean up other than .slick-current slide
            if ( ! thisElem.hasClass( 'slick-current' ) ) {
                newStr = newStr.replace( /\((.*?)\)/g, '' ).trim();
            }

            thisElem.attr( 'aria-label', newStr );

        } );

    }

    docReady() {
        this.cache();
        this.initCarousels();
    }
}
