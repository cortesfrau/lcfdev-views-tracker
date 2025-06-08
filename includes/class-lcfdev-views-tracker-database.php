<?php

defined('ABSPATH') || exit;

if (!class_exists('LCFDev_Views_Tracker_Database')) {
    class LCFDev_Views_Tracker_Database
    {
        /**
         * Table name
         * 
         * @var string
         */
        private $table;

        /**
         * Constructor
         * 
         * @return void
         */
        public function __construct()
        {
            global $wpdb;
            $this->table = $wpdb->prefix . 'lcfdev_post_views';
        }

        /**
         * Create the table
         * 
         * @return void
         */
        public function install(): void
        {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE {$this->table} (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                post_id BIGINT UNSIGNED NOT NULL,
                post_type VARCHAR(20) NOT NULL,
                view_date DATE NOT NULL,
                views INT UNSIGNED NOT NULL DEFAULT 1,
                UNIQUE KEY post_date (post_id, post_type, view_date),
                KEY idx_post_date (post_id, post_type, view_date)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }

        /**
         * Get the table name
         * 
         * @return string
         */
        public function get_table_name(): string
        {
            return $this->table;
        }
    }
}
