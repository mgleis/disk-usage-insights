TODOs


// TODO: Es müssen zuerst nur die Directories gescannt werden, danach die files!



# Internal list of Todos

- Submit plugin to the WP repository here: https://wordpress.org/plugins/developers/add/
- Support for large folder structurs (xhr load)
- Support for Soft Links
- Integrate Charts
 - icicle? https://observablehq.com/@d3/icicle/2?intent=fork
- Add Live Preview Button
 - see https://wordpress.org/plugins/visualizer/
 - seee https://krasenslavov.com/how-to-add-live-preview-for-your-wordpress-org-plugins-with-blueprints/
 - docs see https://wordpress.github.io/wordpress-playground/
 - final link: https://playground.wordpress.net/?php-extension-bundle=light&plugin=disk-usage-insights&url=/wp-admin/tools.php?page=disk-usage-insights%2Fsrc%2FPlugin.php
 - blueprint editor: https://playground.wordpress.net/builder/builder.html


# Queue based approach (version 2)
- Create a file YYYY-MM-DD_HHMMSS_MS.db and reference this in the upcoming worker
- Setup a Worker that runs for 10 seconds again and again



calculateDirTotalSizes
calculateDirFileSizes
calculateDirFileCount
calculateLargestFiles
    SELECT * FROM index WHERE name = 'file_sizes' ORDER BY value DESC LIMIT 5;
calculateLargestFoldersRecursive
    SELECT * FROM index WHERE name = 'dir_recursizve_sizes' ORDER BY value DESC LIMIT 10;
calculateLargestFolders
    SELECT * FROM index WHERE name = 'dir_sizes' ORDER BY value DESC LIMIT 10;
calculateFoldersWithMostFiles
    SELECT * FROM index WHERE name = 'dir_file_count' ORDER BY value DESC LIMIT 10;
calculateLargestFilesFoldersFirstLevel
