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

        this.container.find( '.hero__control--play-large' ).on( 'click', this.initPlayVideo.bind( this ) );
        this.container.find( '.hero__control--play' ).on( 'click', this.playVideo.bind( this ) );
        this.container.find( '.hero__control--pause' ).on( 'click', this.pauseVideo.bind( this ) );
        this.container.find( '.hero__control--stop' ).on( 'click', this.stopVideo.bind( this ) );
        this.container.find( '.hero__control--mute' ).on( 'click', this.muteVideo.bind( this ) );
        this.container.find( '.hero__video' ).on( 'click', this.toggleVideo.bind( this ) );
        this.container.find( '.hero__control--volume' ).on( 'change', this.alterVolume.bind( this ) );
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
     * Initial play video, before the video is activated
     *
     * @return {void}
     */
    initPlayVideo() {
        const el = this.getVideoElement();

        if ( el && el.paused ) {
            el.play();
            this.container.find( '.hero__video' ).removeClass( 'is-hidden' );
            const pauseBtn = this.container.find( '.hero__control--pause' );
            const playBtn = this.container.find( '.hero__control--play-large' );
            const videoParent = this.container.find( '.hero__video' ).parent();
            const videoControls = this.container.find( '.hero__video-controls' );

            playBtn.addClass( 'is-hidden' );
            videoParent.attr( 'aria-hidden', false );
            videoParent.removeAttr( 'tabindex' );
            videoControls.removeClass( 'is-hidden' );
            pauseBtn.trigger( 'focus' );
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
        }
    }

    /**
     * Stop video
     *
     * @return {void}
     */
    stopVideo() {
        const el = this.getVideoElement();

        if ( el ) {
            el.pause();
            el.currentTime = 0;
        }
    }

    /**
     * Mute or unmute video
     *
     * @return {void}
     */
    muteVideo() {
        const el = this.getVideoElement();

        if ( el ) {
            el.muted = ! el.muted;

            // Change volume slider value based on mute state
            const volumeSlider = this.container.find( '.hero__control--volume' );
            volumeSlider.val( el.muted ? 0 : 100 );
            this.container.find( '.hero__volume' ).toggleClass( 'is-hidden', el.muted );
            this.container.find( '.hero__volume-none' ).toggleClass( 'is-hidden', ! el.muted );
        }
    }

    /**
     * Alter video volume
     *
     * @return {void}
     */
    alterVolume() {
        const el = this.getVideoElement();
        const volumeSlider = this.container.find( '.hero__control--volume' );
        const volume = volumeSlider.val();

        // Unmute video on slider change and change icons
        if ( el.muted && volume > 0 ) {
            el.muted = false;
            this.container.find( '.hero__volume' ).removeClass( 'is-hidden' );
            this.container.find( '.hero__volume-none' ).addClass( 'is-hidden' );
        }

        el.volume = volume / 100;
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
