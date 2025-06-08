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
        public $db;

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
            $this->db = new LCFDev_Views_Tracker_Database();
            $this->tracker = new LCFDev_Views_Tracker_Counter($this->db);
            $this->query = new LCFDev_Views_Tracker_Query($this->db);

            register_activation_hook(__FILE__, [$this->db, 'install']);

            $this->init_hooks();
        }

        /**
         * Get the database
         * 
         * @return LCFDev_Views_Tracker_Database
         */
        public function init_hooks(): void
        {
            // Track the view
            add_action('template_redirect', [$this->tracker, 'track_view']);
        }
    }
}
