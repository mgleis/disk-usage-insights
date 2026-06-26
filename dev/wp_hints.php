<?php

// This file contains function/class/const definitions of used WordPress functionality
// with the only purpose to let Visual Studio Code suppress warnings of
// unknown functions.

return;

const ABSPATH = '';
const WP_CONTENT_DIR = '';
const WP_PLUGIN_URL = '';
const WP_PLUGIN_DIR = '';

class WP_Admin_Bar {
    function add_menu(array $a) {}
}

function add_action($name, $callback, int $priority = 0) {}
function add_submenu_page($a, $b, $c, $d, $e, $f) {}
function admin_url(string $s): string { return ''; }
function check_admin_referer(string $s) {}
function check_ajax_referer(string $s) {}
function current_user_can(string $c) {}
function esc_attr(string $s): string { return $s; }
function esc_html($s) {}
function esc_js(string $s): string { return $s; }
function esc_url(string $s): string { return $s; }
function esc_url_raw(string $s): string { return $s; }
function get_locale(): string { return 'en_US'; }
function get_theme_root(): string { return ''; }
function is_admin() {}
function is_multisite() {}
function is_user_logged_in() {}
function number_format_i18n(mixed $value, $digits = 0) {}
function plugin_dir(string $s): string { return ''; }
function plugin_dir_url(string $s): string { return ''; }
function plugins_url(string $s = '', string $s2 = ''): string { return ''; }
function sanitize_file_name(string $s): string { return $s; }
function sanitize_text_field(string $s): string { return $s; }
function status_header(int $code, string $description = '') { http_response_code($code); if ($description) { echo $description; } }
function wp_create_nonce(string $s): string { return ''; }
function wp_delete_file(string $file): bool { return unlink($file); }
function wp_die() {}
function wp_enqueue_script(string $s, string $s2, array $a, string $s3, array $a2) {}
function wp_enqueue_style(string $s, string $s2, array $a, string $s3) {}
function wp_get_current_user() { return new stdClass(); }
function wp_get_wp_version(): string { return '6.7.1'; }
function wp_remote_get(string $url, array $args = array()) { $str = file_get_contents($url); return ['body' => $str]; }
function wp_unslash(string $s): string { return $s; }
function wp_upload_dir(): array { return ['basedir' => '']; }
function wp_verify_nonce(string $s, string $s2): string { return ''; }