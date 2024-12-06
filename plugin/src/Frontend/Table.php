<?php
namespace Mgleis\DiskUsageInsights\Frontend;

class Table {

    private /** @var string */ $headline;
    private /** @var array */ $columnNames = [];
    private /** @var array */ $columnCss = [];
    private /** @var array */ $percentBar = null; // [$colDisplay, $colSource]
    private /** @var array */ $data = [];

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

    public function withData(array $data): self {
        $this->data = $data;

        return $this;
    }

    public function output() {
        $table = $this->data;
        include __DIR__ . '/../../views/blocks/table.php';
    }

    public function withPercentBar(int $colDisplay, int $colSource): self {
        $this->percentBar = [$colDisplay, $colSource];

        return $this;
    }

    public function hasPercentBar($col): bool {
        return $this->percentBar !== null
            && $this->percentBar[0] === $col;
    }

    public function getPercentBar(int $row): float {
        return floatval($this->data[$row][$this->percentBar[1]]);
    }

}
