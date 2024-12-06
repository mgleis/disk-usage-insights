<?php

// This file contains function/class/const definitions of used WordPress functionality
// with the only purpose to let Visual Studio Code suppress warnings of
// unknown functions.

return;

const ABSPATH = '';
const WP_CONTENT_DIR = '';
const WP_PLUGIN_URL = '';

class WP_Admin_Bar {
    function add_menu() {}
}

function esc_html($s) {}
function is_user_logged_in() {}
function wp_get_current_user() { return new stdClass(); }
function add_action($name, $callback) {}
function add_submenu_page($a, $b, $c, $d, $e, $f) {}
function number_format_i18n() {}
function wp_enqueue_script() {}
function plugins_url() {}
function wp_enqueue_style() {}
function admin_url() {}
function plugin_dir_url() {}
function wp_create_nonce() {}
function check_ajax_referer() {}
function wp_die() {}
function esc_attr() {}
function esc_url() {}
function is_admin() {}
function is_multisite() {}
function current_user_can() {}