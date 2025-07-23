<x-filament::page>
    <div class="space-y-4">
        <h2 class="text-xl font-bold">{{ $record->title }}</h2>

        <img src="{{ asset('storage/' . $record->images) }}" class="w-64 rounded-md">

        <p class="text-gray-700  whitespace-normal break-words" >{{ $record->description }}</p>

        <p>Status: <span class="px-2 py-1 rounded bg-gray-200">{{ $record->status }}</span></p>
    </div>
</x-filament::page>
