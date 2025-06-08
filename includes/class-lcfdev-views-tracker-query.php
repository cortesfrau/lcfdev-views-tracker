<?php

defined('ABSPATH') || exit;

if (!class_exists('LCFDev_Views_Tracker_Query')) {
    class LCFDev_Views_Tracker_Query
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
         * Get the most viewed posts
         * 
         * @param array $args
         * @return array
         */
        public function get_most_viewed(array $args = []): array
        {
            global $wpdb;
            $table = $this->database->get_table_name();

            $defaults = [
                'post_type' => 'post',
                'taxonomy' => null,
                'terms' => null,
                'period' => 'all', // day, week, month, year, all
                'limit' => 10
            ];
            $args = wp_parse_args($args, $defaults);

            // Sanitize and validate arguments
            $args['limit'] = absint($args['limit']);
            if ($args['limit'] <= 0) {
                $args['limit'] = 10;
            }

            // Build date filter
            $date_filter = '';
            $allowed_periods = ['day', 'week', 'month', 'year', 'all'];
            if (in_array($args['period'], $allowed_periods, true)) {
                switch ($args['period']) {
                    case 'day':
                        $date_filter = "AND v.view_date = CURDATE()";
                        break;
                    case 'week':
                        $date_filter = "AND v.view_date >= CURDATE() - INTERVAL 7 DAY";
                        break;
                    case 'month':
                        $date_filter = "AND v.view_date >= CURDATE() - INTERVAL 30 DAY";
                        break;
                    case 'year':
                        $date_filter = "AND v.view_date >= CURDATE() - INTERVAL 1 YEAR";
                        break;
                    default:
                        $date_filter = '';
                        break;
                }
            }

            // Build taxonomy filter
            $taxonomy_filter = '';
            $taxonomy_join = '';
            if (!empty($args['taxonomy']) && !empty($args['terms'])) {
                $taxonomy_join = "JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id 
                                  JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id 
                                  JOIN {$wpdb->terms} t ON tt.term_id = t.term_id";

                $terms = is_array($args['terms']) ? $args['terms'] : [$args['terms']];
                $terms_placeholders = implode(',', array_fill(0, count($terms), '%s'));
                $taxonomy_filter = $wpdb->prepare(
                    "AND tt.taxonomy = %s AND t.slug IN ($terms_placeholders)",
                    array_merge([$args['taxonomy']], $terms)
                );
            }

            // Build the complete query
            $base_query = "SELECT p.ID, p.post_title, SUM(v.views) as total_views
                           FROM {$table} v
                           JOIN {$wpdb->posts} p ON v.post_id = p.ID
                           {$taxonomy_join}
                           WHERE p.post_type = %s 
                           AND p.post_status = 'publish'
                           {$date_filter}
                           {$taxonomy_filter}
                           GROUP BY p.ID, p.post_title
                           ORDER BY total_views DESC
                           LIMIT %d";

            $query = $wpdb->prepare($base_query, $args['post_type'], $args['limit']);

            $results = $wpdb->get_results($query);

            return $results ? $results : [];
        }
    }
}
