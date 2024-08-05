<?php
namespace Mgleis\DiskUsageInsights\Frontend;

class Table {

    private /** @var string */ $headline;
    private /** @var array */ $columnNames = [];
    private /** @var array */ $columnCss = [];

    public function __construct(string $headline, array $columnNames = null, array $columnCss = null)
    {
        $this->headline = $headline;
        if ($columnNames !== null) {
            $this->columnNames = $columnNames;
        }
        if ($columnCss !== null) {
            $this->columnCss = $columnCss;
        }
    }

    public function output(array $table) {
        include __DIR__ . '/../../views/blocks/table.php';
    }

}
