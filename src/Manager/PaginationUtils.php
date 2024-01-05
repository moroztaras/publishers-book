<?php

declare(strict_types=1);

namespace App\Manager;

class PaginationUtils
{
    public static function calcOffset(int $page, int $pageLimit): int
    {
        return max($page - 1, 0) * $pageLimit;
    }

    public static function calcPages(int $totalElements, int $pageLimit): int
    {
        return (int) ceil($totalElements / $pageLimit);
    }
}
