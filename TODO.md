# TODOs



# Open

# Version 1.2
- save paths relativ to root
    WP_LANG_DIR
- store root in database

# Version 1.x
- idea: data must be saved to wp-content/disk-usage-insights directory!
    - that dir should also contain an empty index.php file
    - add a hook to delete snapshots on uninstall (not needed right now)
- reduce size of database
    - 2024-12-13 default installation on raidboxes uses ... bytes for 1 snapshot (too much)
            9:30 -- 430.080
            9:56 -- 729.088
            - 823.000
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
- Submit plugin to the WP repository here: https://wordpress.org/plugins/developers/add/
- Support for large folder structurs (xhr load)
- Support for Soft Links
- Integrate Charts
 - icicle? https://observablehq.com/@d3/icicle/2?intent=fork
- Add Live Preview Button
 - see https://wordpress.org/plugins/visualizer/
 - seee https://krasenslavov.com/how-to-add-live-preview-for-your-wordpress-org-plugins-with-blueprints/
 - docs see https://wordpress.github.io/wordpress-playground/
 - final link: https://playground.wordpress.net/?php-extension-bundle=light&plugin=disk-usage-insights&url=/wp-admin/tools.php?page=disk-usage-insights
 - blueprint editor: https://playground.wordpress.net/builder/builder.html
