<div class="relative" x-data="{ focused: false }">
    <label class="block text-sm font-medium text-gray-700 mb-1">Payee Name</label>
    <input type="text"
           wire:model.live="search"
           @focus="focused = true; $wire.showSuggestions = true"
           @blur="setTimeout(() => { focused = false; $wire.hideSuggestions() }, 200)"
           placeholder="Enter payee name"
           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

    <!-- Suggestions Dropdown -->
    <div x-show="$wire.showSuggestions && focused"
         wire:loading.remove
         class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">

        @if(count($suggestions) > 0)
            @foreach($suggestions as $suggestion)
                <div wire:click="selectPayee('{{ $suggestion }}')"
                     class="px-4 py-2 cursor-pointer hover:bg-blue-50 hover:text-blue-700 border-b border-gray-100 last:border-b-0">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-yellow-600 font-medium text-sm">{{ substr($suggestion, 0, 1) }}</span>
                        </div>
                        <span class="text-sm">{{ $suggestion }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="px-4 py-2 text-gray-500 text-sm">
                No payees found
            </div>
        @endif
    </div>

    <!-- Loading indicator -->
    <div wire:loading class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg">
        <div class="px-4 py-2 text-gray-500 text-sm flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Searching...
        </div>
    </div>
</div>
