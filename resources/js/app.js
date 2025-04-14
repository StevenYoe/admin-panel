import './bootstrap';
// resources/js/app.js
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.sortable-header').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.dataset.sort;
            const currentSortOrder = new URLSearchParams(window.location.search).get('sort_order');
            const newSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
            
            const url = new URL(window.location.href);
            url.searchParams.set('sort_by', sortBy);
            url.searchParams.set('sort_order', newSortOrder);
            window.location.href = url.toString();
        });
    });
});