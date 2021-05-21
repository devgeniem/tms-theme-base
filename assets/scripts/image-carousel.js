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
        this.carousels = $( '.image-carousel__items--primary' );
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

        const arrowClass = 'button button--icon is-primary';
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
     * @param {HTMLElement} element Main carousel element.
     * @param {Object} buttons Buttons to use.
     * @param {Object} translations Translations.
     * @return {*|jQuery|HTMLElement} Constructed main carousel.
     */
    constructCarousel( element = undefined, buttons = {}, translations = {} ) {
        const carousel = $( element );
        const modalCarouselId = '#' + carousel.attr( 'data-slider-for' ) || false;

        const carouselOptions = {
            prevArrow: buttons.prevArrow,
            nextArrow: buttons.nextArrow,
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
            carouselOptions.regionLabel = 'main image carousel';
            carouselOptions.asNavFor = modalCarouselId;

            const modalCarousel = $( modalCarouselId );

            // Start the modal carousel.
            modalCarousel.slick( {
                centerMode: true,
                slidesToShow: 1,
                slidesToScroll: 1,
                fade: true,
                asNavFor: '#' + modalCarousel.attr( 'data-slider-for' ),
                prevArrow: carouselOptions.prevArrow,
                nextArrow: carouselOptions.nextArrow,
                regionLabel: 'modal image carousel',
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
        }

        // Start the main carousel.
        carousel.slick( carouselOptions );

        return carousel;
    }

    docReady() {
        this.cache();
        this.initCarousels();
    }
}
