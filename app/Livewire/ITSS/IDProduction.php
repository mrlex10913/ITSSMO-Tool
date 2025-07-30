<?php

namespace App\Livewire\ITSS;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.enduser')]
class IDProduction extends Component
{
    use WithFileUploads;

    // ID Design Properties
    public $designMode = false;
    public $previewMode = 'front';

    // Front Side Design
    public $frontBgColor = '#1e40af';
    public $frontTextColor = '#ffffff';
    public $frontAccentColor = '#fbbf24';
    public $frontLayout = 'standard';
    public $showLogo = true;
    public $logoPosition = 'top-right';

    // Back Side Design
    public $backBgColor = '#ffffff';
    public $backTextColor = '#000000';
    public $backLayout = 'standard';

    // ID Data
    public $studentData = [
        'name' => 'John Doe',
        'middle_name' => 'M.',
        'last_name' => 'Smith',
        'student_id' => '23-6067-990',
        'course' => 'BS Computer Science',
        'year' => '2024',
        'address' => '123 Main St., City, Province',
        'emergency_contact' => 'Jane Doe',
        'emergency_phone' => '09123456789',
        'photo_url' => null
    ];

    // Design Templates
    public $templates = [
        'college' => [
            'name' => 'College Student',
            'front_bg' => '#1e40af',
            'front_text' => '#ffffff',
            'front_accent' => '#fbbf24',
            'layout' => 'standard'
        ],
        'employee' => [
            'name' => 'Employee',
            'front_bg' => '#059669',
            'front_text' => '#ffffff',
            'front_accent' => '#ffffff',
            'layout' => 'professional'
        ],
        'faculty' => [
            'name' => 'Faculty',
            'front_bg' => '#7c2d12',
            'front_text' => '#ffffff',
            'front_accent' => '#fbbf24',
            'layout' => 'academic'
        ]
    ];

    public function mount()
    {
        // Initialize with default template
        $this->applyTemplate('college');
    }

    public function toggleDesignMode()
    {
        $this->designMode = !$this->designMode;
    }

    public function setPreviewMode($mode)
    {
        $this->previewMode = $mode;
    }

    public function applyTemplate($templateKey)
    {
        if (isset($this->templates[$templateKey])) {
            $template = $this->templates[$templateKey];
            $this->frontBgColor = $template['front_bg'];
            $this->frontTextColor = $template['front_text'];
            $this->frontAccentColor = $template['front_accent'];
            $this->frontLayout = $template['layout'];
        }
    }

    public function exportForPrinting()
    {
        // Generate print-ready data for Smart ID Printer 51S
        $printData = [
            'front' => $this->generateFrontPrintData(),
            'back' => $this->generateBackPrintData(),
            'settings' => $this->getPrinterSettings()
        ];

        $this->dispatch('download-print-data', $printData);
    }

    private function generateFrontPrintData()
    {
        return [
            'layout' => $this->frontLayout,
            'background_color' => $this->frontBgColor,
            'text_color' => $this->frontTextColor,
            'accent_color' => $this->frontAccentColor,
            'student_data' => $this->studentData,
            'logo_settings' => [
                'show' => $this->showLogo,
                'position' => $this->logoPosition
            ]
        ];
    }

    private function generateBackPrintData()
    {
        return [
            'layout' => $this->backLayout,
            'background_color' => $this->backBgColor,
            'text_color' => $this->backTextColor,
            'student_data' => $this->studentData
        ];
    }

    private function getPrinterSettings()
    {
        return [
            'card_type' => 'CR80',
            'orientation' => 'portrait',
            'resolution' => '300dpi',
            'color_mode' => 'full_color',
            'duplex' => true,
            'ribbon_type' => 'YMCKO'
        ];
    }

    public function printPreview()
    {
        // Trigger the print preview event to the frontend
        $this->dispatch('print-preview');
    }

    public function directPrint()
    {
        // Generate HTML content for direct printing to Smart IDP 51S
        $frontHtml = view('components.id-layouts.standard-front', [
            'frontBgColor' => $this->frontBgColor,
            'frontTextColor' => $this->frontTextColor,
            'frontAccentColor' => $this->frontAccentColor,
            'studentData' => $this->studentData,
            'showLogo' => $this->showLogo,
            'logoPosition' => $this->logoPosition
        ])->render();

        $backHtml = view('components.id-layouts.standard-back', [
            'backBgColor' => $this->backBgColor,
            'backTextColor' => $this->backTextColor,
            'studentData' => $this->studentData
        ])->render();

        // Option 1: Use optimized print template
        $optimizedHtml = view('smart-idp-print', [
            'frontHtml' => $frontHtml,
            'backHtml' => $backHtml
        ])->render();

        // Create print-ready data for Smart IDP 51S
        $printData = [
            'front_html' => $frontHtml,
            'back_html' => $backHtml,
            'optimized_html' => $optimizedHtml,
            'settings' => $this->getPrinterSettings(),
            'student_info' => $this->studentData
        ];

        // Log for debugging
        Log::info('Direct print to Smart IDP 51S initiated', [
            'student_id' => $this->studentData['student_id'],
            'student_name' => $this->studentData['name']
        ]);

        // Dispatch event to frontend for direct printing
        $this->dispatch('direct-print-to-smart-idp', $printData);

        // Show success message
        $this->dispatch('notify', [
            'message' => 'Sending ID card to Smart IDP 51S printer...',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.i-t-s-s.i-d-production');
    }
}
