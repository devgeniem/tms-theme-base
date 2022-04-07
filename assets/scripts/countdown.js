/**
 * Countdown JS controller.
 */

/**
 * Class Countdown
 */
export default class Countdown {

    /**
     * Cache dom elements for use in the class's methods
     *
     * @return {void}
     */
    cache() {
        this.countdowns = document.querySelectorAll( '.countdown' );
    }

    /**
     * Initialize countdown.
     *
     * @param {Element} countdown Countdown element.
     */
    initCountdown( countdown ) {
        if ( countdown.dataset.timestamp ) {
            countdown.days_container = countdown.querySelector( '.countdown__time--days' );
            countdown.hours_container = countdown.querySelector( '.countdown__time--hours' );
            countdown.minutes_container = countdown.querySelector( '.countdown__time--minutes' );
            countdown.offset = countdown.dataset.offset ? countdown.dataset.offset : 0;

            const target = parseInt( countdown.dataset.timestamp, 10 ) * 1000;

            this.updateCountdownTime( countdown, target, null );

            const MINUTE_IN_MS = 1000 * 60;

            const interval = setInterval( () => {
                this.updateCountdownTime( countdown, target, interval );
            }, MINUTE_IN_MS );
        }
    }

    /**
     * Update countdown time.
     *
     * @param {Element} countdown  Countdown element.
     * @param {number}  targetTime Timestamp in milliseconds.
     * @param {?number} interval   Interval ID
     *
     * @return {void}
     */
    updateCountdownTime( countdown, targetTime, interval ) {
        const
            dateNow = new Date(),
            timezoneOffset = parseInt( countdown.offset, 10 );

        dateNow.setHours( dateNow.getHours() + timezoneOffset );

        const
            now = dateNow.getTime(),
            diff = targetTime - now,
            days = Math.floor( diff / ( 1000 * 60 * 60 * 24 ) ),
            hours = Math.floor( ( diff % ( 1000 * 60 * 60 * 24 ) ) / ( 1000 * 60 * 60 ) ),
            minutes = Math.floor( ( diff % ( 1000 * 60 * 60 ) ) / ( 1000 * 60 ) );

        const isDaysExpired = days < 0;
        const isHoursExpired = hours < 0;
        const isMinutesExpired = minutes < 0;

        if ( ! isDaysExpired && countdown.days_container ) {
            countdown.days_container.querySelector( '.countdown__figure' ).innerHTML = days.toString();
        }

        if ( ! isHoursExpired && countdown.hours_container ) {
            countdown.hours_container.querySelector( '.countdown__figure' ).innerHTML = hours.toString();
        }

        if ( ! isMinutesExpired && countdown.minutes_container ) {
            countdown.minutes_container.querySelector( '.countdown__figure' ).innerHTML = minutes.toString();
        }

        const isExpired = isDaysExpired && isHoursExpired && isMinutesExpired;

        if ( isExpired ) {
            countdown.classList.add( 'countdown--is-expired' );

            if ( interval !== null ) {
                clearInterval( interval );
            }
        }
    }

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        this.cache();

        if ( this.countdowns ) {
            Array.from( this.countdowns ).map( this.initCountdown.bind( this ) );
        }
    }
}
