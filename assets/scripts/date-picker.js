/**
 * Copyright (c) 2021. Geniem Oy
 * Date Picker controller.
 */

import { defineCustomElements } from '@duetds/date-picker/dist/loader';

/**
 * Export the class reference.
 */
export default class DatePicker {

    /**
     * Run when the document is ready.
     *
     * @return {void}
     */
    docReady() {
        const pickers = document.querySelectorAll( 'duet-date-picker' );
        const dateFormat = /^(\d{1,2})\.(\d{1,2})\.(\d{4})$/;
        const localization = window.s.datepicker || {
            buttonLabel: 'Pick a date',
            placeholder: 'dd.mm.yyyy',
            selectedDateMessage: 'The chosen date is',
            prevMonthLabel: 'Previous month',
            nextMonthLabel: 'Next month',
            monthSelectLabel: 'Month',
            yearSelectLabel: 'Year',
            closeLabel: 'Close window',
            calendarHeading: 'Pick a date',
            dayNames: [
                'Sunday', 'Monday', 'Tuesday', 'Wednesday',
                'Thursday', 'Friday', 'Saturday',
            ],
            monthNames: [
                'January', 'February', 'March', 'April',
                'May', 'June', 'July', 'August',
                'September', 'October', 'November', 'December',
            ],
            monthNamesShort: [
                'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec',
            ],
            locale: 'en-GB',
        };

        pickers.forEach( ( picker ) => {
            picker.dateAdapter = {
                parse( value = '', createDate ) {
                    const matches = value.match( dateFormat );

                    if ( matches ) {
                        return createDate( matches[ 3 ], matches[ 2 ], matches[ 1 ] );
                    }
                },
                format( date ) {
                    return `${ date.getDate() }.${ date.getMonth() + 1 }.${ date.getFullYear() }`;
                },
            };

            if ( ! picker.dataset.hasOwnProperty( 'locale' ) ) {
                localization.locale = picker.dataset.locale;
            }

            picker.localization = localization;
        } );
    }
}

defineCustomElements( window );
