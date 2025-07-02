<?php
// Helper function to display a sort arrow (↑ or ↓) based on the current sort column and order.
// Usage: getSortArrow($currentSort, $column, $sortOrder)
//   - $currentSort: the column currently being sorted
//   - $column: the column to check
//   - $sortOrder: 'asc' or 'desc'
// Returns: '↑' if ascending, '↓' if descending, or '' if not the sorted column
if (!function_exists('getSortArrow')) {
    function getSortArrow($currentSort, $column, $sortOrder) {
        if ($currentSort !== $column) return '';
        return $sortOrder === 'asc' ? '↑' : '↓';
    }
}