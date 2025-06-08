<?php

defined('ABSPATH') || exit;

if (!class_exists('LCFDev_Views_Tracker')) {
    class LCFDev_Views_Tracker
    {
        /**
         * Instance
         * 
         * @var LCFDev_Views_Tracker
         */
        private static $instance = null;

        /**
         * Database
         * 
         * @var LCFDev_Views_Tracker_Database
         */
        public $database;

        /**
         * Tracker
         * 
         * @var LCFDev_Views_Tracker_Counter
         */
        public $tracker;

        /**
         * Query
         * 
         * @var LCFDev_Views_Tracker_Query
         */
        public $query;

        /**
         * Instance
         * 
         * @return LCFDev_Views_Tracker
         */
        public static function instance()
        {
            if (self::$instance === null) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        /**
         * Constructor
         * 
         * @return void
         */
        private function __construct()
        {
            // Initialize the classes
            $this->database = new LCFDev_Views_Tracker_Database();
            $this->tracker = new LCFDev_Views_Tracker_Counter($this->database);
            $this->query = new LCFDev_Views_Tracker_Query($this->database);

            // Install the database
            register_activation_hook(LCFDEV_VIEWS_TRACKER_FILE, [$this->database, 'install']);

            // Track the view
            add_action('template_redirect', [$this->tracker, 'track_view']);
        }
    }
}
