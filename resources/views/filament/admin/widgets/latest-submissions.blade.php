<x-filament-widgets::widget>
    <x-filament::section heading="Latest submissions">
        @if ($submissions->isEmpty())
            <p class="text-sm text-gray-500">No submissions yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-start text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-3 py-2 font-medium">Type</th>
                            <th class="px-3 py-2 font-medium">Name</th>
                            <th class="px-3 py-2 font-medium">Subject / Product</th>
                            <th class="px-3 py-2 font-medium">Received</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($submissions as $submission)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-3 py-2">{{ $submission['type'] }}</td>
                                <td class="px-3 py-2">
                                    <a href="{{ $submission['url'] }}" class="text-primary-600 hover:underline dark:text-primary-400">{{ $submission['name'] }}</a>
                                </td>
                                <td class="px-3 py-2">{{ $submission['detail'] }}</td>
                                <td class="px-3 py-2">{{ $submission['created_at']->format('M j, Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
