<?php

namespace Database\Seeders;

use App\Models\InvoiceTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceTemplateSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $templates = [
            [
                'name'       => 'Classic',
                'slug'       => 'classic',
                'is_default' => true,
                'config'     => [
                    'layout'       => 'traditional',
                    'font_family'  => 'Helvetica Neue, Helvetica, Arial, sans-serif',
                    'header_style' => 'branded',
                    'table_style'  => 'striped-header',
                ],
            ],
            [
                'name'       => 'Modern',
                'slug'       => 'modern',
                'is_default' => false,
                'config'     => [
                    'layout'       => 'card-based',
                    'font_family'  => 'Figtree, sans-serif',
                    'header_style' => 'minimal',
                    'table_style'  => 'borderless',
                ],
            ],
            [
                'name'       => 'Minimal',
                'slug'       => 'minimal',
                'is_default' => false,
                'config'     => [
                    'layout'       => 'single-column',
                    'font_family'  => 'Inter, sans-serif',
                    'header_style' => 'text-only',
                    'table_style'  => 'simple-lines',
                ],
            ],
            [
                'name'       => 'Bold',
                'slug'       => 'bold',
                'is_default' => false,
                'config'     => [
                    'layout'       => 'full-width',
                    'font_family'  => 'Montserrat, sans-serif',
                    'header_style' => 'dark-banner',
                    'table_style'  => 'accent-rows',
                ],
            ],
            [
                'name'       => 'Elegant',
                'slug'       => 'elegant',
                'is_default' => false,
                'config'     => [
                    'layout'       => 'framed',
                    'font_family'  => 'Georgia, Times New Roman, serif',
                    'header_style' => 'centered-brand',
                    'table_style'  => 'thin-borders',
                ],
            ],
        ];

        foreach ($templates as $template) {
            InvoiceTemplate::firstOrCreate(
                ['slug' => $template['slug'], 'workspace_id' => null],
                $template
            );
        }
    }
}
