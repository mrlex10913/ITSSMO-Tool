<?php

namespace App\Http\Controllers;

use App\Models\Helpdesk\TicketAttachment;
use Illuminate\Support\Facades\Auth;

class TicketAttachmentController extends Controller
{
    private function authorizeAccess(TicketAttachment $attachment): array
    {
        $ticket = $attachment->ticket()->with('requester')->first();

        if (! Auth::check()) {
            abort(403);
        }
        $user = Auth::user();

        // Resolve role slug via Roles model to avoid attribute/relation name collision on 'role'
        $roleSlug = '';
        if ($user->role_id) {
            $roleSlug = strtolower((string) optional(\App\Models\Roles::find($user->role_id))->slug);
        }
        $isAgent = in_array($roleSlug, ['itss', 'administrator', 'developer']);
        $isOwner = $ticket && $ticket->requester_id === $user->id;
        $sameDepartment = false;
        if ($ticket && $ticket->department && $user->department) {
            $sameDepartment = strtolower(trim((string) $ticket->department)) === strtolower(trim((string) $user->department));
        }

        if (! $isAgent && ! $isOwner && ! $sameDepartment) {
            abort(403);
        }

        $disk = $attachment->disk ?: 'private';
        $path = $attachment->path;

        if (! \Illuminate\Support\Facades\Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $absolute = \Illuminate\Support\Facades\Storage::disk($disk)->path($path);

        return [$disk, $path, $absolute];
    }

    public function download(TicketAttachment $attachment)
    {
        [, , $absolute] = $this->authorizeAccess($attachment);

        return response()->download($absolute, $attachment->filename);
    }

    public function preview(TicketAttachment $attachment)
    {
        [, , $absolute] = $this->authorizeAccess($attachment);
        // Inline preview (images, pdf, etc.)
        $headers = [];
        if ($attachment->mime) {
            $headers['Content-Type'] = $attachment->mime;
        }
        $headers['Content-Disposition'] = 'inline; filename="'.($attachment->filename ?? basename($absolute)).'"';

        return response()->file($absolute, $headers);
    }
}
