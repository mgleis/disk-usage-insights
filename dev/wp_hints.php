<?php

// This file contains function/class/const definitions of used WordPress functionality
// with the only purpose to let Visual Studio Code suppress warnings of
// unknown functions.

return;

const ABSPATH = '';
const WP_CONTENT_DIR = '';
const WP_PLUGIN_URL = '';

class WP_Admin_Bar {
    function add_menu(array $a) {}
}

function esc_html($s) {}
function is_user_logged_in() {}
function wp_get_current_user() { return new stdClass(); }
function add_action($name, $callback, int $priority = 0) {}
function add_submenu_page($a, $b, $c, $d, $e, $f) {}
function number_format_i18n(mixed $value, $digits = 0) {}
function wp_enqueue_script(string $s, string $s2, array $a, string $s3, array $a2) {}
function plugins_url(string $s = '', string $s2 = ''): string { return ''; }
function wp_enqueue_style(string $s, string $s2, array $a, string $s3) {}
function admin_url(string $s): string { return ''; }
function plugin_dir_url(string $s): string { return ''; }
function plugin_dir(string $s): string { return ''; }
function wp_create_nonce(string $s): string { return ''; }
function check_ajax_referer(string $s) {}
function wp_die() {}
function esc_attr(string $s): string { return $s; }
function esc_url(string $s): string { return $s; }
function is_admin() {}
function is_multisite() {}
function current_user_can(string $c) {}
function wp_get_wp_version(): string { return '6.7.1'; }
function get_locale(): string { return 'en_US'; }
function sanitize_file_name(string $s): string { return $s; }
function wp_unslash(string $s): string { return $s; }
function wp_remote_get(string $url, array $args = array()) {
    $str = file_get_contents($url);
    return ['body' => $str];
}
function wp_delete_file(string $file): bool {
    return unlink($file);
}