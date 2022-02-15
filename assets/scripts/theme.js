/**
 * Copyright (c) 2021. Geniem Oy
 * Theme controller.
 */

import Common from './common';
import Accordion from './accordion';
import MapLayout from './map-layout';
import CopyToClipboard from './copy-to-clipboard';
import Hero from './hero';
import Table from './table';
import Image from './image';
import Modal from './modal';
import ImageCarousel from './image-carousel';
import Header from './header';
import BackToTop from './back-to-top';
// Hyphenation is disabled because of JS errors caused by the used library
// import Hyphenation from './hyphenation';
import FlyOutNav from './fly-out-nav';
import PrimaryNav from './primary-nav';
import Dropdown from './dropdown';
import Home from './home';
import Toggle from './toggle';
import Sitemap from './sitemap';
import ExternalLinks from './external-links';
import DatePicker from './date-picker';
import GravityFormsPatch from './gravity-forms-patch';

const globalControllers = {
    Common,
    Accordion,
    MapLayout,
    CopyToClipboard,
    Hero,
    Table,
    Image,
    Modal,
    ImageCarousel,
    Header,
    BackToTop,
    // Hyphenation is disabled because of JS errors caused by the used library
    // Hyphenation,
    FlyOutNav,
    PrimaryNav,
    Dropdown,
    Home,
    Toggle,
    Sitemap,
    ExternalLinks,
    DatePicker,
    GravityFormsPatch,
};

const templateControllers = {
};

/**
 * Class Theme
 *
 * This singleton controls theme's JS class running.
 */
class Theme {
    /**
     * The constructor creates the singleton and binds the docready event.
     *
     * @return {Theme} Either the instance of the class or nothing.
     */
    constructor() {
        if ( instance ) {
            return instance;
        }

        // Initialize the controller maps.
        this._templateControllers = {};
        this._globalControllers = {};

        // Load controllers.
        this.setGlobalControllers();
        this.setTemplateControllers();

        this.init();

        // Bind run controllers on document ready.
        document.addEventListener( 'DOMContentLoaded', ( e ) => this.runDocReady( e ) );

        const instance = this;
    }

    /**
     * Runs the 'init' function for all included scripts.
     *
     * @return {void}
     */
    init() {
        // Run all global scripts.
        for ( const className in this._globalControllers ) {
            if ( ! this._globalControllers.hasOwnProperty( className ) ) {
                continue;
            }
            if ( typeof this._globalControllers[ className ].init === 'function' ) {
                this._globalControllers[ className ].init();
            }
        }

        // Run template-specific scripts
        for ( const className in this._templateControllers ) {
            if ( ! this._templateControllers.hasOwnProperty( className ) ) {
                continue;
            }
            if ( Theme.documentHasClass( className )
                && typeof this._templateControllers[ className ].init === 'function'
            ) {
                this._templateControllers[ className ].init();
            }
        }
    }

    /**
     * A getter for all controllers.
     *
     * @return {Object} A hash map of all controllers.
     */
    get controllers() {
        return this._templateControllers.concat( this._globalControllers );
    }

    /**
     * This method returns a controller by its class name if it is found.
     *
     * @param {string} name The class name of a controller.
     * @return {Object|boolean} The controller instance or false if not found.
     */
    getController( name ) {
        if ( typeof this._globalControllers[ name ] !== 'undefined' ) {
            return this._globalControllers[ name ];
        }
        else if ( typeof this._templateControllers[ name ] !== 'undefined' ) {
            return this._templateControllers[ name ];
        }
        return false;
    }

    /**
     * Set the globally run scripts.
     *
     * @return {void}
     */
    setGlobalControllers() {
        if ( globalControllers ) {
            for ( const className in globalControllers ) {
                // Skip non-function iterations.
                if ( typeof globalControllers[ className ] !== 'function' ) {
                    continue;
                }

                // Set the class reference as a property under the Theme instance.
                this[ className ] = globalControllers[ className ];

                // Construct the class and set it under the class property.
                this._globalControllers[ className ] = new globalControllers[ className ]();
            }
        }
    }

    /**
     * Set the template specific scripts.
     *
     * @return {void}
     */
    setTemplateControllers() {
        if ( templateControllers ) {
            for ( const className in templateControllers ) {
                // Set the template's controller only if the class is defined properly
                // and the current view has the same class name.
                if ( typeof templateControllers[ className ] === 'function' && Theme.documentHasClass( className ) ) {
                    // Set the class reference as a property under the Theme instance.
                    this[ className ] = templateControllers[ className ];

                    // Construct the class and set it under the class property.
                    this._templateControllers[ className ] = new templateControllers[ className ]();
                }
            }
        }
    }

    /**
     * Run theme scripts for the html elements class list.
     *
     * @return {void}
     */
    runDocReady() {
        // Run all global scripts
        for ( const className in this._globalControllers ) {
            if ( ! this._globalControllers.hasOwnProperty( className ) ) {
                continue;
            }
            if ( typeof this._globalControllers[ className ].docReady === 'function' ) {
                this._globalControllers[ className ].docReady();
            }
        }

        // Run template-specific scripts
        for ( const className in this._templateControllers ) {
            if ( ! this._templateControllers.hasOwnProperty( className ) ) {
                continue;
            }
            if ( Theme.documentHasClass( className )
                && typeof this._templateControllers[ className ].docReady === 'function'
            ) {
                this._templateControllers[ className ].docReady();
            }
        }

        this.addDataCmdListener();
    }

    /**
     * Check whether the body has the given class.
     *
     * @param {string} docClass The body class string.
     * @return {boolean} True of false
     */
    static documentHasClass( docClass ) {
        return document.documentElement.classList.contains( docClass );
    }

    /**
     * Finds parent element with data-cmd attribute.
     *
     * @param {Object} element Target element.
     * @return {Object|boolean} Returns object if foundAttr
     */
    findCmdAttribute( element ) {
        let cmdAttr, cmdCtrl, hrefAttr;
        let foundAttr = false;
        let foundLink = false;

        while ( element && element.nodeName && element.getAttribute ) {
            // Find data-cmds
            if ( ! foundAttr ) {
                cmdAttr = element.getAttribute( 'data-cmd' );
                cmdCtrl = element.getAttribute( 'data-ctrl' );

                if ( cmdAttr && cmdCtrl ) {
                    foundAttr = { cid: cmdAttr, el: element, ctrl: cmdCtrl };
                }
            }

            // Find links
            if ( ! foundLink ) {
                hrefAttr = element.getAttribute( 'href' );

                if ( hrefAttr ) {
                    foundLink = { href: hrefAttr, el: element };
                }
            }
            element = element.parentNode;
        }

        if ( foundAttr ) {
            return {
                cmd: foundAttr,
                link: foundLink,
            };
        }
        return false;
    }

    /**
     * Add global listener to listen click events. If clicked dom element or parent node
     * has data-cmd and data-ctrl attributes, call the corresponding method
     * in defined controller, if exists.
     *
     * @return {void}
     */
    addDataCmdListener() {
        jQuery( document ).on( 'click', ( e ) => {
            const captured = this.findCmdAttribute( e.target );

            if ( captured ) {
                const command = captured.cmd.cid;
                const controllerName = captured.cmd.ctrl;
                const controllerInstance = this.getController( controllerName );

                if ( controllerInstance && typeof controllerInstance[ command ] === 'function' ) {
                    Common.stop( e );

                    // Set the event as the first parameter and the actual captured element as the second parameter.
                    controllerInstance[ command ].call( controllerInstance, e, captured.cmd.el );
                }
            }
        } );
    }
}

export default new Theme();
