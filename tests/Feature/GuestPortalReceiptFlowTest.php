<?php

namespace Tests\Feature;

use App\Livewire\Tickets\GuestPortal;
use Livewire\Livewire;
use Tests\TestCase;

class GuestPortalReceiptFlowTest extends TestCase
{
    public function test_cannot_continue_without_download(): void
    {
        Livewire::test(GuestPortal::class)
            ->set('showReceiptModal', true)
            ->set('lastTicketNo', 'HD-2025-00001')
            ->set('lastTicketEmail', 'guest@example.com')
            ->set('downloadedReceipt', false)
            ->call('proceedToTrack')
            ->assertHasErrors([]) // no validation errors
            ->assertSet('showReceiptModal', true);
    }

    public function test_redirects_to_track_after_download(): void
    {
        Livewire::test(GuestPortal::class)
            ->set('showReceiptModal', true)
            ->set('lastTicketNo', 'HD-2025-00001')
            ->set('lastTicketEmail', 'guest@example.com')
            ->set('downloadedReceipt', true)
            ->call('proceedToTrack')
            ->assertRedirect(route('helpdesk.track'));
    }
}
