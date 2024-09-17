<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
                <div class="flex justify-between p-2">
                    <div class="flex gap-4 items-center">
                        <h1 class="text-gray-100">Code: <span class="font-bold italic">{{ $generatedCode ?? 'No code generated yet' }}</span></h1>
                        <x-button wire:click="generateCode">
                            Generate Code
                        </x-button>
                    </div>
                    <div>
                        {{-- Space --}}
                    </div>


                </div>
            </div>
        </div>
    </div>

</div>
