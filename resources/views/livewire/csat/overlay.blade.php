<div x-data="{ showThankYou: false }" x-init="window.addEventListener('csat-thankyou', () => { showThankYou = true; setTimeout(() => showThankYou = false, 2000); })">
    @if($show)
    <div class="fixed inset-0 z-[1000] bg-black/60 backdrop-blur-sm flex items-center justify-center">
        <template x-if="!showThankYou">
            <div class="w-full max-w-md mx-4 rounded-xl bg-white dark:bg-gray-900 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6" role="dialog" aria-modal="true" aria-labelledby="csat-title">
                <h2 id="csat-title" class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">We value your feedback</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Please rate your overall experience with our system.</p>

                <div class="flex items-center gap-2 mb-4" x-data="{ value: @entangle('rating').live }">
                    <template x-for="i in 5" :key="i">
                        <button type="button" class="p-1" @click="$wire.setRating(i)" @mouseover="value = i" @mouseleave="value = $wire.rating">
                            <svg x-bind:class="i <= value ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600'" class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.347l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.482 20.54a.562.562 0 01-.84-.61l1.285-5.386a.563.563 0 00-.182-.557L2.54 10.387a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.347L10.98 3.5z" />
                            </svg>
                        </button>
                    </template>
                </div>

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Additional comments (optional)</label>
                    <textarea wire:model.defer="comment" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm" placeholder="Tell us what went well or what could improve..."></textarea>
                    @error('rating') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="flex justify-end">
                    <button wire:click="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 px-4 py-2 rounded-md text-white bg-blue-600 hover:bg-blue-700 text-sm disabled:opacity-50">
                        <span class="material-symbols-sharp text-sm">send</span>
                        Submit
                    </button>
                </div>
            </div>
        </template>
        <template x-if="showThankYou">
            <div class="w-full max-w-md mx-4 rounded-xl bg-white dark:bg-gray-900 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-8 flex flex-col items-center justify-center" role="dialog" aria-modal="true">
                <span class="material-symbols-sharp text-4xl text-amber-500 mb-2">star_rate</span>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-1">Thank you for your feedback!</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Your response has been recorded.</p>
            </div>
        </template>
    </div>
    @endif

    <script>
    document.addEventListener('livewire:init', function(){
        // Subscribe to public CSAT channel and show overlay in realtime when enabled
        try {
            if (window.Echo) {
                window.Echo.channel('csat').listen('CsatEnforcementChanged', (e) => {
                    console.debug('CsatEnforcementChanged received (overlay)', e);
                    // Only end users will actually show; component will re-evaluate
                    window.Livewire.dispatch('csat:check');
                });
            }
        } catch(_) {}
    });
    window.addEventListener('csat-thankyou', () => {
        // Optionally, could add sound or analytics here
    });
    </script>
</div>
