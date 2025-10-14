<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sage Native Block Package Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file is for the Sage Native Block package.
    | It provides configuration for block templates and presets.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Block Templates
    |--------------------------------------------------------------------------
    |
    | Define available block templates for scaffolding. Each template
    | includes a name, description, and stub path for generating blocks.
    |
    | GENERIC templates: Minimal styling, works with any theme
    | THEME templates: Real-world examples from specific themes
    |
    */
    'templates' => [
        'basic' => [
            'name' => 'Basic Block',
            'description' => 'Simple block with InnerBlocks support',
            'stub_path' => 'block',
        ],

        // Generic Templates - Minimal styling, universal compatibility
        'innerblocks' => [
            'name' => 'InnerBlocks Container (Generic)',
            'description' => 'Minimal container with heading and content',
            'stub_path' => 'generic/innerblocks',
        ],
        'two-column' => [
            'name' => 'Two Column Layout (Generic)',
            'description' => 'Basic two-column layout structure',
            'stub_path' => 'generic/two-column',
        ],
        'statistics' => [
            'name' => 'Statistics Section (Generic)',
            'description' => 'Simple statistics layout',
            'stub_path' => 'generic/statistics',
        ],
        'cta' => [
            'name' => 'Call-to-Action (Generic)',
            'description' => 'Basic CTA with button',
            'stub_path' => 'generic/cta',
        ],

        // Nynaeve Theme Templates (by Imagewize)
        // Based on production Nynaeve theme - requires specific theme.json setup
        // See: stubs/themes/nynaeve/README.md for requirements
        'nynaeve-innerblocks' => [
            'name' => 'InnerBlocks (Nynaeve Theme)',
            'description' => 'From Nynaeve theme - montserrat, open-sans fonts',
            'stub_path' => 'themes/nynaeve/innerblocks',
        ],
        'nynaeve-two-column' => [
            'name' => 'Two Column (Nynaeve Theme)',
            'description' => 'Card-style layout from Nynaeve theme',
            'stub_path' => 'themes/nynaeve/two-column',
        ],
        'nynaeve-statistics' => [
            'name' => 'Statistics (Nynaeve Theme)',
            'description' => 'Full statistics section from Nynaeve theme',
            'stub_path' => 'themes/nynaeve/statistics',
        ],
        'nynaeve-cta' => [
            'name' => 'CTA (Nynaeve Theme)',
            'description' => 'Styled call-to-action from Nynaeve theme',
            'stub_path' => 'themes/nynaeve/cta',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Template
    |--------------------------------------------------------------------------
    |
    | The default template to use when none is specified.
    |
    */
    'default_template' => 'basic',

    /*
    |--------------------------------------------------------------------------
    | Typography Presets
    |--------------------------------------------------------------------------
    |
    | Theme-specific typography presets (customizable per theme).
    | These values should align with your theme.json settings.
    |
    */
    'typography_presets' => [
        'main_heading' => [
            'fontFamily' => 'montserrat',
            'fontSize' => '3xl',
            'fontWeight' => '700',
            'textColor' => 'main',
        ],
        'sub_heading' => [
            'fontFamily' => 'montserrat',
            'fontSize' => '2xl',
            'fontWeight' => '600',
            'textColor' => 'main',
        ],
        'body_text' => [
            'fontFamily' => 'open-sans',
            'fontSize' => 'base',
            'fontWeight' => '400',
            'textColor' => 'secondary',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Spacing Presets
    |--------------------------------------------------------------------------
    |
    | Common spacing patterns used across block templates.
    |
    */
    'spacing_presets' => [
        'section_bottom' => '4rem',
        'paragraph_bottom' => '2rem',
        'column_gap_large' => '3.75rem',
        'column_gap_medium' => '1.875rem',
    ],

];
