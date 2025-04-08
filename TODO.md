# TODOs

Goal: PHP 7.0 as min requirement (supports most systems)
    oder lieber 8.0?
    mal schauen, was der compat check ergibt

- WordPress Version + PHP Version https://wordpress.org/about/stats/
- WP Version / PHP Compatability https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/


Respect Special Content Folders:
    https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#Moving_wp-content_folder


# Open

# Version 1.6
- pagination for tables

- delete single file
- delete multiple files
- delete directories
- hide wordpress files


# Version 1.x
- idea: data must be saved to wp-content/disk-usage-insights directory!
    - that dir should also contain an empty index.php file
    - add a hook to delete snapshots on uninstall (not needed right now)
- worker should take a look at max_execution_time
- show error-jobs on snapshot result page
- exclude wp-core files from output
- create a workflow for steps before publishing next plugin version
    - ensure that IDE shows no PHP errors
    - Plugin Check
    - check sonarcloud
- make sure that CSS is nice
- make sure that safety issues are met
- clean up the building of URLs (admin_url etc.)
- clean up referencing of __DIR__ . '/../../../output'
- switch templating with mustache or similar
- make better error message for corrupt databases
- "Largest Plugin Folder" + "Largest Theme Folders"
- create grpahical output
- make it browsable

# Later
- Support for Soft Links
- Integrate Charts
 - icicle? https://observablehq.com/@d3/icicle/2?intent=fork


# Escaping
see https://developer.wordpress.org/apis/security/escaping/
- esc_html()    -> HTML
- esc_js()      -> Inline JS
- esc_url()     -> ALl URLs
- esc_attr()    -> HTML attribute


[X] Add Live Preview Button
 - see https://wordpress.org/plugins/visualizer/
 - seee https://krasenslavov.com/how-to-add-live-preview-for-your-wordpress-org-plugins-with-blueprints/
 - docs see https://wordpress.github.io/wordpress-playground/
 - final link: https://playground.wordpress.net/?php-extension-bundle=light&plugin=disk-usage-insights&url=/wp-admin/tools.php?page=disk-usage-insights
 - blueprint editor: https://playground.wordpress.net/builder/builder.html
