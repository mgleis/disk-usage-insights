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
        $s = '';
        $s .= '<div class="DUI-panel DUI-panel--TOP10">';
        $s .= sprintf('<div class=DUI-panel__headline>%s</div>', esc_html($this->headline));
        $s .= '<div class=DUI-panel__content>';
        $s .= "<table class=DUI-table>\n";
        $s .= "<thead>";
        $s .= "<tr class=DUI-table__header>\n";
        foreach ($this->columnNames as $col) {
            $s .= "<th>";
            $s .= esc_html($col);
            $s .= "</th>";
        }
        $s .= "</tr>\n";
        $s .= "</thead>";
        $s .= "<tbody>";
        $i = 0;
        foreach ($table as $row) {
            $s .= "<tr>\n";
            foreach ($row as $idx => $column) {
                if (!empty($this->columnCss[$idx])) {
                    $s .= '<td class="'. esc_attr($this->columnCss[$idx]) .'">';
                } else {
                    $s .= "<td>";
                }
                $s .= esc_html($column);
                $s .= "</td>\n";
            }
            $s .= "</tr>\n";
        }
        $s .= "</tbody>";
        $s .= '</table>';
        $s .= '</div>';
        $s .= '</div>';

        return $s;
    }

}
