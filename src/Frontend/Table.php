<?php
namespace Mgleis\DiskUsageInsights\Frontend;

class Table {

    private string $headline;
    private array $columnNames = [];
    private array $columnCss = [];

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

    function output(array $table) {
        include __DIR__ . '/../../views/blocks/table.php';
    }

}
