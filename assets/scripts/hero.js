/**
 * Hero JS controller.
 */

// Use jQuery as $ within this file scope.
const $ = jQuery; // eslint-disable-line no-unused-vars
import debounce from 'lodash.debounce';

/**
 * Class Hero
 */
export default class Hero {

    /**
     * Constructor
     *
     * @return {void}
     */
    constructor() {
        this.BOX_BREAKPOINT = 1024;
    }

    /**
     * Cache elements
     *
     * @return {void}
     */
    cache() {
        this.container = $( '.hero' );
    }

    /**
     * Events
     *
     * @return {void}
     */
    events() {
        $( window ).resize( debounce( this.adjustSpacing.bind( this ), 250 ) );

        this.container.find( '.hero__control--play' ).on( 'click', this.playVideo.bind( this ) );
        this.container.find( '.hero__control--pause' ).on( 'click', this.pauseVideo.bind( this ) );
        this.container.find( '.hero__video' ).on( 'click', this.toggleVideo.bind( this ) );
    }

    /**
     * Adjust spacing for hero box overflow
     *
     * @return {void}
     */
    adjustSpacing() {
        if ( ! this.container.hasClass( 'hero--box' ) ) {
            return;
        }

        const margin = $( window ).width() < this.BOX_BREAKPOINT
            ? this.container.find( '.hero__box' ).outerHeight() / 2
            : 0;

        this.container.css( 'marginBottom', margin );
    }

    /**
     * Toggle video
     *
     * @return {void}
     */
    toggleVideo() {
        const el = this.getVideoElement();

        if ( ! el ) {
            return;
        }

        if ( el.paused ) {
            this.playVideo();
        }
        else {
            this.pauseVideo();
        }
    }

    /**
     * Play video
     *
     * @return {void}
     */
    playVideo() {
        const el = this.getVideoElement();

        if ( el && el.paused ) {
            el.play();
            this.container.toggleClass( 'has-video-playing' );
            this.container.find( '.hero__video' ).removeClass( 'is-hidden' );
            const pauseBtn = this.container.find( '.hero__control--pause' );

            pauseBtn.focus();

            window.setTimeout( () => {
                pauseBtn.fadeOut( 200, function() {
                    $( this ).prop( 'style', '' );
                } );
            }, 1000 );
        }
    }

    /**
     * Pause video
     *
     * @return {void}
     */
    pauseVideo() {
        const el = this.getVideoElement();

        if ( el && ! el.paused ) {
            el.pause();
            this.container.toggleClass( 'has-video-playing' );
            this.container.find( '.hero__control--play' ).focus();
        }
    }

    /**
     * Get video element
     *
     * @return {boolean|Object} jQuery selected video element or false.
     */
    getVideoElement() {
        const $video = this.container.find( '.hero__video' );

        return $video.length === 0
            ? false
            : $video.get( 0 );
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        if ( $( '.hero' ).length === 0 ) {
            return;
        }

        this.cache();
        this.events();
        this.adjustSpacing();
    }
}
