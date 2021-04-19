<?php
/**
 * Copyright (c) 2021. Geniem Oy
 */

namespace TMS\Theme\Base;

/**
 * Class Logger
 *
 * A class to be implemented in a project to enable logging.
 *
 * @package TMS\Theme\Base
 */
class Logger {

    /**
     * The log level defines which entries are logged.
     * If log level is higher than the message level, the log entry is omitted.
     *
     * To change the log level, define the "LOG_LEVEL" constant.
     *
     * Levels:
     *
     * DEBUG (100)
     * INFO (200)
     * NOTICE (250)
     * WARNING (300)
     * ERROR (400)
     * CRITICAL (500)
     * ALERT (550)
     * EMERGENCY (600)
     *
     * Defaults to DEBUG(100), meaning all entries are logged.
     *
     * @var int
     */
    private $log_level = 100;

    /**
     * The DEBUG log level.
     */
    const DEBUG = 100;

    /**
     * The INFO log level.
     */
    const INFO = 200;

    /**
     * The NOTICE log level.
     */
    const NOTICE = 250;

    /**
     * The WARNING log level.
     */
    const WARNING = 300;

    /**
     * The ERROR log level.
     */
    const ERROR = 400;

    /**
     * The CRITICAL log level.
     */
    const CRITICAL = 500;

    /**
     * The ALERT log level.
     */
    const ALERT = 550;

    /**
     * The EMERGENCY log level.
     */
    const EMERGENCY = 600;

    /**
     * Logger constructor.
     *
     * Sets the log level from a constant if it exists.
     */
    public function __construct() {
        if ( defined( 'GENIEM_LOG_LEVEL' ) ) {
            $this->log_level = GENIEM_LOG_LEVEL ?: $this->log_level;
        }
    }

    /**
     * Log a debug message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function debug( string $message, $context = null ) : void {
        if ( static::DEBUG >= $this->log_level ) {
            $this->log( $message, $context, 'DEBUG' );
        }
    }

    /**
     * Log an info message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function info( string $message, $context = null ) : void {
        if ( static::INFO >= $this->log_level ) {
            $this->log( $message, $context, 'INFO' );
        }
    }

    /**
     * Log a notice message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function notice( string $message, $context = null ) : void {
        if ( static::NOTICE >= $this->log_level ) {
            $this->log( $message, $context, 'NOTICE' );
        }
    }

    /**
     * Log a warning message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function warning( string $message, $context = null ) : void {
        if ( static::WARNING >= $this->log_level ) {
            $this->log( $message, $context, 'WARNING' );
        }
    }

    /**
     * Log an error message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function error( string $message, $context = null ) : void {
        if ( static::ERROR >= $this->log_level ) {
            $this->log( $message, $context, 'ERROR' );
        }
    }

    /**
     * Log a critical message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function critical( string $message, $context = null ) : void {
        if ( static::CRITICAL >= $this->log_level ) {
            $this->log( $message, $context, 'CRITICAL' );
        }
    }

    /**
     * Log an alert message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function alert( string $message, $context = null ) : void {
        if ( static::ALERT >= $this->log_level ) {
            $this->log( $message, $context, 'ALERT' );
        }
    }

    /**
     * Log an emergency message.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     */
    public function emergency( string $message, $context = null ) : void {
        if ( static::EMERGENCY >= $this->log_level ) {
            $this->log( $message, $context, 'EMERGENCY' );
        }
    }

    /**
     * The actual logging method.
     *
     * @param string $message The log message.
     * @param mixed  $context The error context data.
     * @param string $level   The log level as string.
     */
    protected function log( string $message, $context, string $level ) : void {
        $context = empty( $context )
            ? ''
            : ' - Context: ' . addslashes(
                str_replace(
                    PHP_EOL,
                    '',
                    print_r( $context, true ) // phpcs:ignore
                )
            ); // phpcs:ignore
        $context = preg_replace( '/(\s+)/', ' ', $context ); // Remove multiple consecutive spaces.
        error_log( "Geniem Logger - $level - $message$context" ); // phpcs:ignore
    }
}
