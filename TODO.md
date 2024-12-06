TODOs

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

- Phase 1: collect all directories and files with name + size
  - $q->push(new ScanForSubDirsJob($root));
    - $q->push(new ScanForFilesJob($directory))
    - $subdirs = fetch subdirectories
    - foreach $subdirs $q->push(new ScanForSubDirsJob($subir))
    - $q->push(new CheckForEndOfPhase1())
        - if $q->size() == 0 $q->push(new StartAnalysisJob())

- Phase 2: start analysis phase
- StartAnalysisJob (created after step 1):
    $q->push(new Analysis1Job());
        // do work
        $q->push(new CheckForEndOfPhase2());
            if $q->size() == 0 $q->push(new FinishAnalysisJob())
    $q->push(new Analysis2Job());
        // do work
        $q->push(new CheckForEndOfPhase2());
    $q->push(new Analysis3Job());
        // do work
        $q->push(new CheckForEndOfPhase2());

- Phase 3: Clean up
FinishAnalysisJob
    set a flag in the database of the successful analysis

