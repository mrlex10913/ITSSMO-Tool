<div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
    <div  class="gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
        <div class="min-w-full">
            <h2 class="text-xl font-semibold text-black dark:text-white">Barcode Generator</h2>
            <div class="flex gap-4 mb-4">
                <div class="mt-4 flex flex-col">
                    <label for="quantity">Qty</label>
                    <input type="number" x-model="quantity" wire:model="qty"
                    id="quantity" autocomplete="off" class="px-3 mt-1 py-2.5 text-sm font-bold rounded-lg border focus:outline-hidden focus:outline-hidden focus:outline-hidden bg-[#342f2f] text-[#ffffff] focus:outline-[#ffffff] border-[#ffffff] w-24">
                </div>
                <div class="flex items-center mt-10">
                    <button @click="$wire.generateBarcodes(quantity)" class="uppercase font-bold rounded-lg text-sm  w-32 h-12 bg-[#063074] text-[#f9f4f4] justify-center" id="generate-btn">Generate</button>
                </div>
            </div>
            <div class="flex justify-between items-center mb-2">
                <p>Generated Code: {{$totalGeneratedCode}}</p>
                <button class="font-bold rounded-lg text-md w-24 h-8 bg-[#437fdf] text-[#ffffff] justify-center" wire:click="printModalClick">Print</button>
            </div>
            <div class="scrollable-table-container border border-gray-800 rounded">
                <table class="min-w-full devide-ydivide-gray-700">
                  <thead>
                    <tr class="bg-gray-800 text-left">
                      <th scope="col" class="px-6 py-3 text-left text-xs font-extrabold uppercase text-gray-300 tracking-wider">No.</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-extrabold uppercase text-gray-300 tracking-wider">BARCODE NUMBER</th>
                      <th scope="col" class="px-6 py-3 text-left text-xs font-extrabold uppercase text-gray-300 tracking-wider">BARCODE IMAGE</th>
                    </tr>
                  </thead>
                  <tbody id="barcode-table-body">
                    @foreach ($barcodeList as $barcode)
                    <tr class="border-t border-gray-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-medium">{{$loop->iteration}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-medium">{{$barcode->number}}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300 font-medium">{!! $barcode->barcode_html !!}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
        </div>
    </div>
    <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">

        <div class="pt-3 sm:pt-5">
            <h2 class="text-xl font-semibold text-black dark:text-white">Count of generated barcode</h2>

            <p class="mt-4 text-3xl/relaxed font-extrabold">
                {{$totalGeneratedCode}}
            </p>
        </div>
    </div>
    <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">


        <div class="pt-3 sm:pt-5">
            <h2 class="text-xl font-semibold text-black dark:text-white">Printed Barcode</h2>

            <p class="mt-4 text-3xl/relaxed font-extrabold">
                25
            </p>
        </div>
    </div>

    {{-- Modal --}}
    <x-dialog-modal wire:model="printingBarcodeModal">
        <x-slot name="title">
          Printing barcode...
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="" value="{{ __('Quantity') }}" />
                    <input type="number" wire:model="printQuantity" id="quantity" autocomplete="off" class="px-3 mt-1 py-2.5 text-sm font-bold rounded-lg border focus:outline-hidden focus:outline-hidden focus:outline-hidden bg-[#342f2f] text-[#ffffff] focus:outline-[#ffffff] border-[#ffffff] w-24">
                </div>
                <div class="grid grid-cols-2 place-content-center gap-2">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="printBarcodes">
                    {{ __('Print') }}
                </x-button>
                <x-secondary-button wire:click="$set('printingBarcodeModal', false)">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

