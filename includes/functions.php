<?php

defined('ABSPATH') || exit;

if (!function_exists('lcfdev_get_most_viewed')) {
    function lcfdev_get_most_viewed(array $args = []): array
    {
        return LCFDev_Views_Tracker::instance()->query->get_most_viewed($args);
    }
}
