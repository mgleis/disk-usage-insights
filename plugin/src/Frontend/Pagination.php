<?php
namespace Mgleis\DiskUsageInsights\Frontend;

class Pagination {

    private string $urlPattern;
    private int $totalItemCount;
    private int $itemsPerPage;
    private int $page;

    public function __construct(string $urlPattern, $totalItemCount = 0, $page = 0, $itemsPerPage = 10) {
        $this->urlPattern = $urlPattern;
        $this->totalItemCount = $totalItemCount;
        $this->itemsPerPage = $itemsPerPage;
        $this->page = $page;
    }

    public static function parseFromString(string $url): Pagination {
        $result = [];
        list($path, $query) = explode('?', $url);

        parse_str($query, $result);

        $page = $result['p'] ?? 0;
        $itemsPerPage = $result['ipc'] ?? 10;
        $totalItemCount = $result['tic'] ?? 10;

        unset($result['p']);
        unset($result['ipc']);
        unset($result['tic']);

        $urlPattern = $path . '?1=1';
        foreach ($result as $key => $value) {
            $urlPattern .= sprintf('&%s=%s', $key, urlencode($value));
        }
        return new Pagination($urlPattern, $totalItemCount, $page, $itemsPerPage);
    }

    public function hasNextPage(): bool {
        return $this->page+1 < $this->calcTotalPages();
    }

    public function hasPreviousPage(): bool {
        return $this->page != 0;
    }

    private function buildPageUrl(int $page): string {
        return sprintf('%s%sp=%s&ipp=%s&tic=%s', 
            $this->urlPattern,
            str_contains($this->urlPattern, '?') ? '&' : '?',
            $page,
            $this->itemsPerPage,
            $this->totalItemCount
        );
    }

    public function buildNextPageUrl(): string {
        return $this->buildPageUrl(min($this->page + 1, $this->calcTotalPages()));
    }

    public function buildPreviousPageUrl(): string {
        return $this->buildPageUrl(max(0, $this->page - 1));
    }

    public function calcTotalPages(): int {
        return ceil($this->totalItemCount / $this->itemsPerPage);
    }
    public function calcOffset(): int {
        return $this->page * $this->itemsPerPage;
    }

    public function getPage(): int { return $this->page; }
    public function getTotalItemCount(): int { return $this->totalItemCount; }
    public function getItemsPerPage(): int { return $this->itemsPerPage; }

}
