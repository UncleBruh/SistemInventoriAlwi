@props(['headers' => [], 'rows' => [], 'mobileKey' => 'id'])

<!-- Desktop View (Table) - Hidden on mobile -->
<div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider">
                @foreach($headers as $header)
                    <th class="p-3 border border-gray-200 font-semibold">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{ $slot }}
        </tbody>
    </table>
</div>

<!-- Mobile View (Cards) - Visible only on mobile -->
<div class="sm:hidden space-y-4">
    {{ $slot }}
</div>
