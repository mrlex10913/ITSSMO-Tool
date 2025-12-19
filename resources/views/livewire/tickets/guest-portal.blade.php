<div>
<div class="min-h-screen bg-gradient-to-b from-blue-50 to-white dark:from-gray-900 dark:to-gray-900">
    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <x-heroicon name="lifebuoy" class="w-8 h-8 text-blue-600" />
                <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-gray-100">ITSS Helpdesk — Guest Submission</h1>
            </div>
            <a href="{{ route('helpdesk.home') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <x-heroicon name="arrow-left" class="w-4 h-4" />
                Back to Helpdesk
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-lg bg-green-50 text-green-800 px-4 py-3 border border-green-200">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left: Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-800">
                        <p class="text-sm text-gray-600 dark:text-gray-300">Fill out the form below to submit a ticket. You’ll receive a reference number after submission.</p>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Identity -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                                <x-heroicon name="identification" class="w-5 h-5 text-blue-600" /> Your Information
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-label value="Full Name" />
                                    <x-input type="text" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="e.g. Juan Dela Cruz" wire:model.defer="full_name" />
                                    @error('full_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <x-label value="ID Number" />
                                    <x-input type="text" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Student/Employee ID" wire:model.defer="id_number" />
                                    @error('id_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <x-label value="School Email" />
                                    <x-input type="email" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="name@school.edu" wire:model.defer="school_email" />
                                    @error('school_email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ticket details -->
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                                <x-heroicon name="document-text" class="w-5 h-5 text-blue-600" /> Ticket Details
                            </h2>
                            <div class="space-y-4">
                                <div>
                                    <x-label value="Subject" />
                                    <x-input type="text" class="mt-1 w-full bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Short summary of your issue" wire:model.defer="subject" />
                                    @error('subject') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <x-label value="Description" />
                                    <textarea rows="5" class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300" placeholder="Describe the problem, any error messages, what you’ve tried..." wire:model.defer="description"></textarea>
                                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-label value="Category" />
                                        <select class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" wire:model.defer="category_choice">
                                            <option value="">Select category</option>
                                            <option value="account_access">Account Access</option>
                                            <option value="others">Others</option>
                                        </select>
                                        @error('category_choice') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <x-label value="Priority" />
                                        <select class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" wire:model.defer="priority_new">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="critical">Critical</option>
                                        </select>
                                        @error('priority_new') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <x-label value="Department" />
                                        <select class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" wire:model.defer="department">
                                            <option value="">Select department</option>
                                            @foreach($departments as $d)
                                                <option value="{{ $d->slug }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('department') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-yellow-300 bg-yellow-50 dark:bg-yellow-900/30 dark:border-yellow-700">
                            <div class="flex items-start gap-2 mb-3">
                                <x-heroicon name="shield-check" class="w-5 h-5 text-yellow-600" />
                                <p class="text-sm text-yellow-900 dark:text-yellow-100">You may verify your identity by uploading ID (front and back) or a clear photo of your Certificate of Registration (CoR). For Account Access requests, these uploads are required.</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="md:col-span-1">
                                        <x-label value="Verification Method" />
                                        <select class="mt-1 w-full border-gray-300 dark:border-gray-600 rounded-md focus:border-blue-500 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" wire:model.live="verification_option">
                                            <option value="">Select method</option>
                                            <option value="id_card">School/Valid ID (front & back)</option>
                                            <option value="cor">Certificate of Registration (CoR)</option>
                                        </select>
                                        @error('verification_option') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    @if($verification_option === 'id_card')
                                        <!-- ID Front Capture -->
                                        <div class="space-y-2">
                                            <x-label value="ID - Front" />
                                            <input id="id_front_input" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_front" />
                                            <label for="id_front_input" class="cursor-pointer w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 p-3 text-sm text-gray-700 dark:text-gray-200 flex items-center justify-center gap-2">
                                                <x-heroicon name="camera" class="w-5 h-5" />
                                                <span>Take Photo / Choose Image</span>
                                            </label>
                                            @if($id_front)
                                                <img src="{{ $id_front->temporaryUrl() }}" alt="ID Front Preview" class="w-full h-40 object-cover rounded-lg border border-gray-300 dark:border-gray-600" />
                                                <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_front', null)">Retake</button>
                                            @endif
                                            @error('id_front') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        <!-- ID Back Capture -->
                                        <div class="space-y-2">
                                            <x-label value="ID - Back" />
                                            <input id="id_back_input" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="id_back" />
                                            <label for="id_back_input" class="cursor-pointer w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 p-3 text-sm text-gray-700 dark:text-gray-200 flex items-center justify-center gap-2">
                                                <x-heroicon name="camera" class="w-5 h-5" />
                                                <span>Take Photo / Choose Image</span>
                                            </label>
                                            @if($id_back)
                                                <img src="{{ $id_back->temporaryUrl() }}" alt="ID Back Preview" class="w-full h-40 object-cover rounded-lg border border-gray-300 dark:border-gray-600" />
                                                <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('id_back', null)">Retake</button>
                                            @endif
                                            @error('id_back') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    @elseif($verification_option === 'cor')
                                        <!-- CoR Front Capture -->
                                        <div class="md:col-span-2 space-y-2">
                                            <x-label value="Certificate of Registration (Front Page)" />
                                            <input id="cor_input" type="file" accept="image/*" capture="environment" class="sr-only" wire:model="cor_file" />
                                            <label for="cor_input" class="cursor-pointer w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 p-3 text-sm text-gray-700 dark:text-gray-200 flex items-center justify-center gap-2">
                                                <x-heroicon name="camera" class="w-5 h-5" />
                                                <span>Take Photo / Choose Image</span>
                                            </label>
                                            @if($cor_file)
                                                <img src="{{ $cor_file->temporaryUrl() }}" alt="CoR Preview" class="w-full h-40 object-cover rounded-lg border border-gray-300 dark:border-gray-600" />
                                                <button type="button" class="text-xs text-red-600 hover:underline" wire:click="$set('cor_file', null)">Retake</button>
                                            @endif
                                            @error('cor_file') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                                        </div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-3">Tips: Use good lighting, ensure text is readable, and avoid glare. Max 4MB per ID image, 6MB for CoR.</p>
                            </div>

                        <!-- Spam check & Submit -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <x-label value="Spam Check" />
                                <div class="flex items-center gap-2">
                                    <x-input type="text" class="mt-1 w-32 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Type: ITSS" wire:model.defer="captcha" />
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Simple anti-spam check</span>
                                </div>
                                @error('captcha') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div class="text-right sm:mt-6">
                                <x-button wire:click="submit" class="w-full sm:w-auto">Create Ticket</x-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Help / Info -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 p-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-2">
                        <x-heroicon name="information-circle" class="w-5 h-5 text-blue-600" /> Helpful Tips
                    </h3>
                    <ul class="list-disc pl-5 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        <li>Provide a clear subject and detailed description.</li>
                        <li>For account issues, prepare your ID or CoR.</li>
                        <li>Use your official school email address.</li>
                        <li>We’ll email you if more information is needed.</li>
                    </ul>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black/5 dark:ring-white/10 p-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-2 flex items-center gap-2">
                        <x-heroicon name="lock-closed" class="w-5 h-5 text-blue-600" /> Privacy & Security
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300">Your uploads are stored securely and only authorized ITSS staff can access them. Don’t share your password in this form.</p>
                </div>
            </div>
        </div>
    </div>
<!-- Receipt Modal: require download then continue to tracker -->
<x-dialog-modal wire:model="showReceiptModal">
    <x-slot name="title">Ticket Created</x-slot>
    <x-slot name="content">
        <div class="space-y-3">
            <p class="text-sm text-gray-700 dark:text-gray-300">Your ticket was created successfully.</p>
            @if($lastTicketNo)
                <div class="p-3 rounded border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-sm">
                    <div><span class="font-semibold">Ticket Number:</span> <span class="font-mono">{{ $lastTicketNo }}</span></div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Keep this for your records. You’ll need it to track your ticket.</div>
                </div>
            @endif
            <div class="flex items-center gap-2">
                <x-button wire:click="markDownloaded" x-data @click="const data = new Blob([`Ticket Number: {{ $lastTicketNo }}\n`], { type: 'text/plain' }); const url = URL.createObjectURL(data); const a = document.createElement('a'); a.href = url; a.download = 'ticket-{{ $lastTicketNo }}.txt'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);">
                    Download Ticket Info
                </x-button>
                @if(!$downloadedReceipt)
                    <span class="text-xs text-gray-500 dark:text-gray-400">Please download before continuing.</span>
                @endif
            </div>
        </div>
    </x-slot>
    <x-slot name="footer">
        <x-secondary-button wire:click="$set('showReceiptModal', false)">Stay Here</x-secondary-button>
    <x-button class="ml-2" wire:click="proceedToTrack" :disabled="!$downloadedReceipt">Go to Track Ticket</x-button>
    </x-slot>
</x-dialog-modal>

</div>
