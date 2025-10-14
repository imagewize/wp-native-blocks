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
    | EXAMPLES templates: Opinionated styling with theme.json integration
    |   (requires specific font families and color slugs defined in theme.json)
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

        // Example Templates - Pre-styled with theme.json values (opinionated)
        // WARNING: These require specific font families (montserrat, open-sans)
        // and color slugs (main, secondary, tertiary) in your theme.json
        'innerblocks-styled' => [
            'name' => 'InnerBlocks Container (Styled Example)',
            'description' => 'Pre-styled with typography and spacing ⚠️ Requires theme.json setup',
            'stub_path' => 'examples/innerblocks',
        ],
        'two-column-styled' => [
            'name' => 'Two Column Layout (Styled Example)',
            'description' => 'Card-style columns with typography ⚠️ Requires theme.json setup',
            'stub_path' => 'examples/two-column',
        ],
        'statistics-styled' => [
            'name' => 'Statistics Section (Styled Example)',
            'description' => 'Full statistics layout with styling ⚠️ Requires theme.json setup',
            'stub_path' => 'examples/statistics',
        ],
        'cta-styled' => [
            'name' => 'Call-to-Action (Styled Example)',
            'description' => 'Styled CTA with button presets ⚠️ Requires theme.json setup',
            'stub_path' => 'examples/cta',
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
