<?php
if (!function_exists('getSortArrow')) {
    function getSortArrow($currentSort, $column, $sortOrder) {
        if ($currentSort !== $column) return '';
        return $sortOrder === 'asc' ? '↑' : '↓';
    }
}