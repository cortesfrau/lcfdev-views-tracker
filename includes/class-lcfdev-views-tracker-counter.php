<?php

defined('ABSPATH') || exit;

if (!class_exists('LCFDev_Views_Tracker_Counter')) {
    class LCFDev_Views_Tracker_Counter
    {
        /**
         * Database manager
         * 
         * @var LCFDev_Views_Tracker_Database
         */
        private $database;

        /**
         * Constructor
         * 
         * @param LCFDev_Views_Tracker_Database $database
         * @return void
         */
        public function __construct(LCFDev_Views_Tracker_Database $database)
        {
            $this->database = $database;
        }

        /**
         * Track the view
         * 
         * @return void
         */
        public function track_view(): void
        {
            if (!$this->should_track_view()) {
                return;
            }

            global $post, $wpdb;

            $post_id = $post->ID;
            $post_type = $post->post_type;
            $today = current_time('Y-m-d');
            $table = $this->database->get_table_name();

            $wpdb->query($wpdb->prepare(
                "INSERT INTO $table (post_id, post_type, view_date, views)
                 VALUES (%d, %s, %s, 1)
                 ON DUPLICATE KEY UPDATE views = views + 1",
                $post_id,
                $post_type,
                $today
            ));
        }

        /**
         * Determine if the current view should be tracked
         * This method helps avoid counting bot visits
         * 
         * @return bool
         */
        private function should_track_view(): bool
        {
            // Don't track if it's an admin request
            if (is_admin()) {
                return false;
            }

            // Don't track if the user can edit posts
            if (current_user_can('edit_posts')) {
                return false;
            }

            // Check if we have a valid post context
            global $post;
            if (!$post || !isset($post->ID)) {
                return false;
            }

            // Don't track if it's an AJAX request
            if (wp_doing_ajax()) {
                return false;
            }

            // Don't track if it's a REST API request
            if (defined('REST_REQUEST') && REST_REQUEST) {
                return false;
            }

            // Don't track if it's a cron job
            if (wp_doing_cron()) {
                return false;
            }

            // Don't track if the request method is not GET
            if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
                return false;
            }

            // Check if user agent indicates a bot
            if ($this->is_bot_user_agent()) {
                return false;
            }

            // Don't track if it's likely a bot (very restrictive conditions)
            if ($this->is_likely_bot_behavior()) {
                return false;
            }

            return true;
        }

        /**
         * Check if the current user agent indicates a bot
         * 
         * @return bool
         */
        private function is_bot_user_agent(): bool
        {
            if (empty($_SERVER['HTTP_USER_AGENT'])) {
                return true;
            }

            $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

            $bot_patterns = [
                'bot',
                'crawl',
                'spider',
                'scan',
                'scrape',
                'googlebot',
                'bingbot',
                'yahoobot',
                'baiduspider',
                'yandexbot',
                'facebookexternalhit',
                'twitterbot',
                'linkedinbot',
                'pinterestbot',
                'whatsapp',
                'telegram',
                'slackbot',
                'curl',
                'wget',
                'python-requests',
                'java/',
                'php/',
                'headless',
                'phantom',
                'selenium',
                'puppeteer',
                'chrome-lighthouse'
            ];

            foreach ($bot_patterns as $pattern) {
                if (strpos($user_agent, $pattern) !== false) {
                    return true;
                }
            }

            return false;
        }

        /**
         * Check for likely bot behavior patterns
         * 
         * @return bool
         */
        private function is_likely_bot_behavior(): bool
        {
            // Very restrictive check: no referrer AND no cookies AND no accept headers
            if (
                empty($_SERVER['HTTP_REFERER']) &&
                empty($_COOKIE) &&
                empty($_SERVER['HTTP_ACCEPT'])
            ) {
                return true;
            }

            // Check for suspicious accept headers (typical bot behavior)
            if (
                isset($_SERVER['HTTP_ACCEPT']) &&
                $_SERVER['HTTP_ACCEPT'] === '*/*'
            ) {
                return true;
            }

            return false;
        }
    }
}
