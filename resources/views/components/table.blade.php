@props(['headers' => []])

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left">
        <thead class="text-xs uppercase bg-gray-100 dark:bg-gray-700">
            <tr>
                @foreach($headers as $header)
                    <th scope="col" class="text-center px-5 py-3">
                        {{ $header }}
                    </th>
                @endforeach
                <th scope="col" class="text-center px-5 py-3">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>