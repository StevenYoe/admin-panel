@props([
    'headers' => [], // Format: [['name' => 'ID', 'key' => 'id'], ...]
    'sortBy' => null,
    'sortOrder' => 'asc'
])

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left">
        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
            <tr>
            @foreach($headers as $header)
    @php
        if (is_array($header)) {
            $headerName = $header['name'];
            $headerKey = $header['key'];
            $isSorted = $sortBy === $headerKey;
        } else {
            $headerName = $header;
            $headerKey = strtolower(str_replace(' ', '_', $header));
            $isSorted = $sortBy === $headerKey;
        }
        $newSortOrder = $isSorted && $sortOrder === 'asc' ? 'desc' : 'asc';
        $sortUrl = request()->fullUrlWithQuery([
            'sort_by' => $headerKey,
            'sort_order' => $newSortOrder,
            'page' => 1 // Reset to first page when sorting
        ]);
    @endphp
    
    <th 
        scope="col" 
        class="text-center px-5 py-3 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600"
        wire:click="updateSort('{{ $headerKey }}')"
        onclick="window.location.href='{{ $sortUrl }}'"
    >
        <div class="flex items-center justify-center gap-1">
            <span>{{ $headerName }}</span>
            @if($isSorted)
                <span class="sort-indicator">
                    @if($sortOrder === 'asc')
                        ↑
                    @else
                        ↓
                    @endif
                </span>
            @endif
        </div>
    </th>
@endforeach
                <th scope="col" class="text-center px-5 py-3">
                    Aksi
                </th>
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

<style>
    .sort-indicator {
        transition: transform 0.2s;
        font-size: 0.8em;
    }
    th:hover .sort-indicator {
        opacity: 0.7;
    }
</style>