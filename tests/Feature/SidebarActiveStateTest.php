<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarActiveStateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Use existing factories / seeders in project conventions
        $this->artisan('migrate', ['--no-interaction' => true]);
        // Disable middleware to focus test on view rendering/markup, not auth gates
        $this->withoutMiddleware();
    }

    private function extractLinkClass(string $html, ?string $hrefPath, ?string $linkText = null): ?string
    {
        if ($hrefPath) {
            $hrefRel = preg_quote($hrefPath, '/');
            // Match absolute or relative, but ensure it ends at the end or before query/hash/quote
            $hrefAbs = '(?:https?:\\/\\/[^\"]+)?'.$hrefRel.'(?:[\"\?#]|$)';
            // Try class before href
            if (preg_match('/<a[^>]*class=\"([^\"]*)\"[^>]*href=\"'.$hrefAbs.'/i', $html, $m)) {
                return $m[1];
            }
            // Try href before class
            if (preg_match('/<a[^>]*href=\"'.$hrefAbs.'[^>]*class=\"([^\"]*)\"/i', $html, $m)) {
                return $m[1];
            }
        }
        // Fallback: find by link text (e.g., the inner <h3> text)
        if ($linkText) {
            $lt = preg_quote($linkText, '/');
            if (preg_match('/<a([^>]*)>.*?<h3[^>]*>\s*'.$lt.'\s*<\\/h3>.*?<\\/a>/is', $html, $m)) {
                // Extract class from attribute chunk
                if (preg_match('/class=\"([^\"]*)\"/i', $m[1], $mm)) {
                    return $mm[1];
                }
            }
        }

        return null;
    }

    private function classHasToken(string $class, string $token): bool
    {
        return preg_match('/(^|\s)'.preg_quote($token, '/').'($|\s)/', $class) === 1;
    }

    private function classByNearestAnchorText(string $html, string $text): ?string
    {
        $pos = stripos($html, $text);
        if ($pos === false) {
            return null;
        }
        $before = substr($html, 0, $pos);
        $aStart = strripos($before, '<a');
        if ($aStart === false) {
            return null;
        }
        $aEnd = strpos($html, '>', $aStart);
        if ($aEnd === false) {
            return null;
        }
        $chunk = substr($html, $aStart, $aEnd - $aStart + 1);
        if (preg_match('/class=\"([^\"]*)\"/i', $chunk, $m)) {
            return $m[1];
        }

        return '';
    }

    public function test_dashboard_link_has_no_static_bg_when_inactive(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        // Give a basic role if required by policies; assume developer bypasses
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('developer');
        }

        $this->actingAs($user, 'web');

        // Visit a route that is NOT the dashboard (use Borrower's Logs)
        $response = $this->get(route('borrowers.logs'));
        $response->assertStatus(200);

        // Ensure the Dashboard link itself doesn't have static bg classes when inactive
        $html = $response->getContent();
        $class = $this->classByNearestAnchorText($html, 'Dashboard');
        $this->assertNotNull($class, 'Dashboard link not found in sidebar');
        $this->assertFalse($this->classHasToken($class, 'bg-gray-300'));
        $this->assertFalse($this->classHasToken($class, 'bg-gray-100'));
        $this->assertFalse($this->classHasToken($class, 'dark:bg-gray-700'));
    }

    public function test_borrowers_logs_link_activates_on_borrowers_logs_route(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('developer');
        }

        $this->actingAs($user, 'web');

        $response = $this->get(route('borrowers.logs'));
        $response->assertStatus(200);
        // Active class applied by x-nav-link when route matches on the Borrower's Logs link only
        $html = $response->getContent();
        $class = $this->classByNearestAnchorText($html, "Borrower's Logs");
        $this->assertNotNull($class, "Borrower's Logs link not found");
        $this->assertTrue($this->classHasToken($class, 'bg-gray-300') || $this->classHasToken($class, 'bg-gray-50'));
        $this->assertTrue($this->classHasToken($class, 'dark:bg-gray-700'));
    }
}
