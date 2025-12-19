<?php

namespace App\Services\Helpdesk;

use App\Models\Helpdesk\AssignmentRule;
use App\Models\Helpdesk\Ticket;

class AssignmentResolver
{
    /**
     * Return a user id to assign this ticket to, or null if none.
     */
    public static function resolveAssigneeId(Ticket $ticket): ?int
    {
        $rules = AssignmentRule::query()->where('is_active', true)->orderByDesc('id')->get();
        foreach ($rules as $rule) {
            $criteria = (array) $rule->criteria;
            // Match type
            if (! empty($criteria['type']) && strtolower((string) $criteria['type']) !== strtolower((string) ($ticket->type ?? ''))) {
                continue;
            }
            // Match priority
            if (! empty($criteria['priority']) && strtolower((string) $criteria['priority']) !== strtolower((string) ($ticket->priority ?? ''))) {
                continue;
            }
            // Match category
            if (! empty($criteria['category_id']) && (int) $criteria['category_id'] !== (int) ($ticket->category_id ?? 0)) {
                continue;
            }
            // Keyword scan in subject/description
            if (! empty($criteria['keywords']) && is_array($criteria['keywords'])) {
                $hay = strtolower(($ticket->subject.' '.(string) $ticket->description));
                $ok = false;
                foreach ($criteria['keywords'] as $kw) {
                    $kw = trim((string) $kw);
                    if ($kw !== '' && str_contains($hay, strtolower($kw))) {
                        $ok = true;
                        break;
                    }
                }
                if (! $ok) {
                    continue;
                }
            }

            return $rule->assignee_id ?: null;
        }

        return null;
    }
}
