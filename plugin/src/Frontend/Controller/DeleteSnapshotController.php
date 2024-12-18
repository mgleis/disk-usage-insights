<?php
namespace Mgleis\DiskUsageInsights\Frontend\Controller;

use Mgleis\DiskUsageInsights\Domain\DatabaseRepository;
use Mgleis\DiskUsageInsights\Plugin;

class DeleteSnapshotController {

    public function delete() {
        check_ajax_referer(Plugin::NONCE);

        // TODO validate parameter
        $snapshot = $_POST['snapshot'];

        (new DatabaseRepository())->deleteDatabase($snapshot);

        (new ShowSnapshotsController())->execute();
        wp_die();
    }

}