# Block Theme Only Migration Plan

**Goal:** Create a simplified version of the package focused exclusively on block themes (FSE) like Moiraine, with per-block builds using @wordpress/scripts.

## Overview

This is a **much simpler** alternative to the full universal migration. Instead of supporting all theme types, focus exclusively on modern block themes with a standardized structure.

## Why This Is Easier

### Complexity Removed:
- ❌ No Sage/Acorn dependency or detection
- ❌ No theme-level build system integration
- ❌ No multiple path detection and prompts
- ❌ No Laravel/Acorn service providers
- ❌ No editor.js file manipulation
- ❌ No complex theme structure detection
- ❌ No backward compatibility with old patterns

### What Remains:
- ✅ Simple WordPress plugin
- ✅ Per-block builds with @wordpress/scripts (always)
- ✅ Standard block location: `blocks/` (root-level)
- ✅ Standard build pattern: `{block}/build/block.json`
- ✅ React-only blocks (.jsx components)
- ✅ WP-CLI commands
- ✅ Block templates

---

## Simplified Architecture

### Package Structure
```
wp-native-blocks/
├── wp-native-blocks.php           # Plugin entry point (simple!)
├── readme.txt                      # WordPress.org readme
├── composer.json                   # Optional composer support
├── src/
│   └── CLI/
│       └── CreateCommand.php      # Single command class
├── stubs/
│   ├── base/                      # Default block template
│   │   ├── package.json.stub
│   │   ├── .gitignore.stub
│   │   └── src/
│   │       ├── block.json.stub
│   │       ├── index.js.stub
│   │       ├── edit.jsx.stub
│   │       ├── save.jsx.stub
│   │       ├── style.scss.stub
│   │       ├── editor.scss.stub
│   │       └── view.js.stub
│   ├── generic/                   # Universal templates
│   │   ├── innerblocks/
│   │   ├── two-column/
│   │   └── cta/
│   └── moiraine/                  # Moiraine-specific templates
│       ├── hero/
│       ├── feature-grid/
│       ├── testimonial/
│       ├── cta/
│       └── stats/
└── docs/
    ├── README.md
    └── CUSTOM-TEMPLATES.md
```

---

## Migration Steps

### Step 1: Simplify Plugin Entry Point

**Create:** `wp-native-blocks.php`

```php
<?php
/**
 * Plugin Name: WP Native Blocks
 * Description: Scaffold native Gutenberg blocks for block themes with per-block builds
 * Version: 3.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Imagewize
 * License: MIT
 */

if (!defined('ABSPATH')) {
    exit;
}

// Load Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Register WP-CLI command
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('block create', 'Imagewize\\WpNativeBlocks\\CLI\\CreateCommand');
}
```

**That's it!** No service providers, no Acorn detection, no complexity.

---

### Step 2: Simplified Command Class

**Create:** `src/CLI/CreateCommand.php`

```php
<?php

namespace Imagewize\WpNativeBlocks\CLI;

use WP_CLI;
use WP_CLI_Command;

class CreateCommand extends WP_CLI_Command
{
    /**
     * Create a new native block
     *
     * ## OPTIONS
     *
     * <name>
     * : Block name (e.g., vendor/block-name)
     *
     * [--template=<template>]
     * : Template to use (default: base)
     *
     * [--blocks-dir=<path>]
     * : Blocks directory (default: blocks)
     *
     * ## EXAMPLES
     *
     *     wp block create imagewize/hero
     *     wp block create imagewize/hero --template=moiraine-hero
     */
    public function __invoke($args, $assoc_args)
    {
        $blockName = $args[0] ?? null;
        $template = $assoc_args['template'] ?? 'base';
        $blocksDir = $assoc_args['blocks-dir'] ?? 'blocks';

        // Validate block name
        if (!$blockName || !str_contains($blockName, '/')) {
            WP_CLI::error('Block name must include vendor (e.g., vendor/block-name)');
            return;
        }

        [$vendor, $name] = explode('/', $blockName, 2);

        // Get theme directory
        $themeDir = get_template_directory();
        $blockPath = $themeDir . '/' . $blocksDir . '/' . $name;

        // Check if block already exists
        if (is_dir($blockPath)) {
            WP_CLI::error("Block already exists at: {$blockPath}");
            return;
        }

        // Create block from stub
        $this->createBlockFromStub($blockPath, $blockName, $template);

        // Update functions.php if needed
        $this->ensureBlockRegistration($themeDir, $blocksDir);

        WP_CLI::success("Block created at: {$blockPath}");
        WP_CLI::line('');
        WP_CLI::line('Next steps:');
        WP_CLI::line("  1. cd {$blocksDir}/{$name}");
        WP_CLI::line('  2. npm install');
        WP_CLI::line('  3. npm run start');
    }

    private function createBlockFromStub(string $blockPath, string $blockName, string $template): void
    {
        $stubsDir = dirname(__DIR__, 2) . '/stubs';

        // Determine stub path
        if ($template === 'base') {
            $stubPath = $stubsDir . '/base';
        } elseif (str_starts_with($template, 'moiraine-')) {
            $stubPath = $stubsDir . '/moiraine/' . str_replace('moiraine-', '', $template);
        } else {
            $stubPath = $stubsDir . '/generic/' . $template;
        }

        if (!is_dir($stubPath)) {
            WP_CLI::error("Template not found: {$template}");
            return;
        }

        // Copy stub files
        $this->recursiveCopy($stubPath, $blockPath, $blockName);

        WP_CLI::line("✓ Created block structure");
    }

    private function recursiveCopy(string $src, string $dst, string $blockName): void
    {
        $dir = opendir($src);
        @mkdir($dst, 0755, true);

        while (false !== ($file = readdir($dir))) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $srcPath = $src . '/' . $file;
            $dstPath = $dst . '/' . str_replace('.stub', '', $file);

            if (is_dir($srcPath)) {
                $this->recursiveCopy($srcPath, $dstPath, $blockName);
            } else {
                $content = file_get_contents($srcPath);

                // Replace placeholders
                $content = str_replace('{{BLOCK_NAME}}', $blockName, $content);
                $content = str_replace('{{BLOCK_SLUG}}', str_replace('/', '-', $blockName), $content);

                file_put_contents($dstPath, $content);
            }
        }

        closedir($dir);
    }

    private function ensureBlockRegistration(string $themeDir, string $blocksDir): void
    {
        $functionsFile = $themeDir . '/functions.php';

        if (!file_exists($functionsFile)) {
            WP_CLI::warning('functions.php not found. You\'ll need to register blocks manually.');
            return;
        }

        $content = file_get_contents($functionsFile);

        // Check if registration already exists
        if (str_contains($content, 'register_block_type($block_json_path)')) {
            WP_CLI::line('✓ Block registration already exists in functions.php');
            return;
        }

        // Add registration code
        $registrationCode = $this->getRegistrationCode($blocksDir);

        // Backup
        copy($functionsFile, $functionsFile . '.backup-' . date('Y-m-d-His'));

        // Append registration
        file_put_contents($functionsFile, $content . "\n" . $registrationCode);

        WP_CLI::line('✓ Added block registration to functions.php');
    }

    private function getRegistrationCode(string $blocksDir): string
    {
        return <<<PHP

/**
 * Register native blocks
 * Auto-generated by WP Native Blocks
 */
add_action('init', function () {
    \$blocks_dir = get_template_directory() . '/{$blocksDir}';

    if (!is_dir(\$blocks_dir)) {
        return;
    }

    \$block_folders = scandir(\$blocks_dir);

    foreach (\$block_folders as \$folder) {
        if (\$folder === '.' || \$folder === '..') {
            continue;
        }

        \$block_json_path = \$blocks_dir . '/' . \$folder . '/build/block.json';

        if (file_exists(\$block_json_path)) {
            register_block_type(\$block_json_path);
        }
    }
}, 10);

PHP;
    }
}
```

**That's the entire command class!** Much simpler than the universal version.

---

### Step 3: Standard Block Stub

**Create:** `stubs/base/package.json.stub`

```json
{
  "name": "{{BLOCK_SLUG}}",
  "version": "1.0.0",
  "description": "Native Gutenberg block",
  "main": "build/index.js",
  "scripts": {
    "build": "wp-scripts build --blocks-manifest --experimental-modules",
    "start": "wp-scripts start --blocks-manifest --experimental-modules",
    "format": "wp-scripts format",
    "lint:css": "wp-scripts lint-style",
    "lint:js": "wp-scripts lint-js"
  },
  "devDependencies": {
    "@wordpress/scripts": "^30.24.0"
  }
}
```

**Create:** `stubs/base/.gitignore.stub`

```
node_modules/
build/
*.log
.DS_Store
```

**Create:** `stubs/base/src/block.json.stub`

```json
{
  "$schema": "https://schemas.wp.org/trunk/block.json",
  "apiVersion": 3,
  "name": "{{BLOCK_NAME}}",
  "version": "1.0.0",
  "title": "{{BLOCK_SLUG}}",
  "category": "common",
  "icon": "block-default",
  "description": "A custom native block",
  "supports": {
    "html": false,
    "align": true,
    "spacing": {
      "padding": true,
      "margin": true
    },
    "color": {
      "background": true,
      "text": true
    }
  },
  "textdomain": "{{BLOCK_SLUG}}",
  "editorScript": "file:./index.js",
  "editorStyle": "file:./index.css",
  "style": "file:./style-index.css"
}
```

**Create:** `stubs/base/src/index.js.stub`

```js
import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import save from './save';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType(metadata.name, {
    edit: Edit,
    save,
});
```

**Create:** `stubs/base/src/edit.jsx.stub`

```jsx
import { useBlockProps } from '@wordpress/block-editor';
import { RichText } from '@wordpress/block-editor';

export default function Edit({ attributes, setAttributes }) {
    const blockProps = useBlockProps();

    return (
        <div {...blockProps}>
            <RichText
                tagName="h2"
                value={attributes.content}
                onChange={(content) => setAttributes({ content })}
                placeholder="Enter content..."
            />
        </div>
    );
}
```

**Create:** `stubs/base/src/save.jsx.stub`

```jsx
import { useBlockProps } from '@wordpress/block-editor';
import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const blockProps = useBlockProps.save();

    return (
        <div {...blockProps}>
            <RichText.Content tagName="h2" value={attributes.content} />
        </div>
    );
}
```

**Create:** `stubs/base/src/style.scss.stub`

```scss
/**
 * Frontend styles
 */
.wp-block-{{BLOCK_SLUG}} {
    padding: 2rem;

    h2 {
        margin: 0;
    }
}
```

**Create:** `stubs/base/src/editor.scss.stub`

```scss
/**
 * Editor-only styles
 */
.wp-block-{{BLOCK_SLUG}} {
    // Editor-specific styles here
}
```

**Create:** `stubs/base/src/view.js.stub`

```js
/**
 * Frontend JavaScript (optional)
 */
document.addEventListener('DOMContentLoaded', () => {
    const blocks = document.querySelectorAll('.wp-block-{{BLOCK_SLUG}}');

    blocks.forEach((block) => {
        // Add frontend interactivity here
    });
});
```

---

### Step 4: Moiraine Templates

Each Moiraine template follows the exact same structure as base, just with:
- Moiraine-specific colors, typography, spacing
- More complex markup in edit.jsx and save.jsx
- Moiraine design patterns

**Example:** `stubs/moiraine/hero/src/edit.jsx.stub`

```jsx
import { useBlockProps, InspectorControls, MediaUpload, RichText } from '@wordpress/block-editor';
import { PanelBody, Button, RangeControl, SelectControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { heading, text, backgroundImage, overlayOpacity, minHeight } = attributes;
    const blockProps = useBlockProps({
        style: {
            backgroundImage: backgroundImage ? `url(${backgroundImage})` : 'none',
            minHeight: `${minHeight}px`,
        },
    });

    return (
        <>
            <InspectorControls>
                <PanelBody title="Hero Settings">
                    <MediaUpload
                        onSelect={(media) => setAttributes({ backgroundImage: media.url })}
                        allowedTypes={['image']}
                        render={({ open }) => (
                            <Button onClick={open} variant="secondary">
                                {backgroundImage ? 'Change Image' : 'Select Image'}
                            </Button>
                        )}
                    />

                    <RangeControl
                        label="Overlay Opacity"
                        value={overlayOpacity}
                        onChange={(value) => setAttributes({ overlayOpacity: value })}
                        min={0}
                        max={100}
                    />

                    <RangeControl
                        label="Minimum Height (px)"
                        value={minHeight}
                        onChange={(value) => setAttributes({ minHeight: value })}
                        min={300}
                        max={1000}
                        step={50}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <div
                    className="hero-overlay"
                    style={{ opacity: overlayOpacity / 100 }}
                />
                <div className="hero-content">
                    <RichText
                        tagName="h1"
                        value={heading}
                        onChange={(value) => setAttributes({ heading: value })}
                        placeholder="Hero Heading"
                        className="hero-heading"
                    />
                    <RichText
                        tagName="p"
                        value={text}
                        onChange={(value) => setAttributes({ text: value })}
                        placeholder="Hero text..."
                        className="hero-text"
                    />
                </div>
            </div>
        </>
    );
}
```

---

### Step 5: Simplified Documentation

**Update:** `README.md`

```markdown
# WP Native Blocks

WordPress plugin for scaffolding native Gutenberg blocks in block themes.

## Features

- 🎨 Per-block builds with @wordpress/scripts
- ⚛️ React-based blocks with JSX
- 📦 Standard block structure
- 🎯 Designed for block themes (FSE)
- 🎨 Moiraine theme templates included

## Requirements

- WordPress 6.0+
- PHP 8.0+
- WP-CLI
- Node.js & npm (for building blocks)

## Installation

### Via WordPress Plugin

1. Download and install the plugin
2. Activate in WordPress admin
3. Use via WP-CLI

### Via Composer

```bash
composer require imagewize/wp-native-blocks --dev
```

## Usage

### Basic Block Creation

```bash
wp block create vendor/block-name
```

This creates:
```
blocks/block-name/
├── package.json
├── .gitignore
└── src/
    ├── block.json
    ├── index.js
    ├── edit.jsx
    ├── save.jsx
    ├── style.scss
    ├── editor.scss
    └── view.js
```

### Using Templates

```bash
# Use Moiraine hero template
wp block create imagewize/hero --template=moiraine-hero

# Use generic two-column template
wp block create imagewize/columns --template=two-column
```

### Custom Blocks Directory

```bash
# Create in custom directory (default: blocks)
wp block create imagewize/custom --blocks-dir=custom-blocks
```

## Building Blocks

After creating a block:

```bash
cd blocks/your-block
npm install
npm run start    # Development with hot reload
npm run build    # Production build
```

## Available Templates

### Generic Templates
- `base` - Minimal starter
- `innerblocks` - Container with InnerBlocks
- `two-column` - Two-column layout
- `cta` - Call-to-action

### Moiraine Theme Templates
- `moiraine-hero` - Hero section
- `moiraine-feature-grid` - Feature grid
- `moiraine-testimonial` - Testimonial block
- `moiraine-cta` - Styled CTA
- `moiraine-stats` - Statistics display

## Block Structure

Every block follows this structure:

```
your-block/
├── package.json          # @wordpress/scripts setup
├── .gitignore
├── src/                 # Source files
│   ├── block.json       # Block metadata
│   ├── index.js         # Registration
│   ├── edit.jsx         # Editor component
│   ├── save.jsx         # Frontend component
│   ├── style.scss       # Frontend styles
│   ├── editor.scss      # Editor styles
│   └── view.js          # Optional JS
└── build/               # Compiled output (auto-generated)
    ├── block.json
    ├── index.js
    ├── style-index.css
    └── index.css
```

## Block Registration

The plugin automatically adds this to `functions.php`:

```php
add_action('init', function () {
    $blocks_dir = get_template_directory() . '/blocks';

    if (!is_dir($blocks_dir)) {
        return;
    }

    $block_folders = scandir($blocks_dir);

    foreach ($block_folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $block_json_path = $blocks_dir . '/' . $folder . '/build/block.json';

        if (file_exists($block_json_path)) {
            register_block_type($block_json_path);
        }
    }
}, 10);
```

## Creating Custom Templates

Create custom templates in your theme:

```
your-theme/block-templates/
└── my-template/
    ├── package.json.stub
    ├── .gitignore.stub
    └── src/
        ├── block.json.stub
        ├── index.js.stub
        ├── edit.jsx.stub
        ├── save.jsx.stub
        ├── style.scss.stub
        ├── editor.scss.stub
        └── view.js.stub
```

Use `{{BLOCK_NAME}}` and `{{BLOCK_SLUG}}` as placeholders.

## Workflow

1. **Create block:** `wp block create vendor/name --template=moiraine-hero`
2. **Install dependencies:** `cd blocks/name && npm install`
3. **Start development:** `npm run start`
4. **Edit in WordPress:** Block appears in editor automatically
5. **Build for production:** `npm run build`

## Why Block Themes Only?

This plugin is specifically designed for modern block themes (FSE) because:

- ✅ Standardized block location (`blocks/`)
- ✅ Per-block builds (no theme build complexity)
- ✅ React-based architecture
- ✅ No legacy PHP rendering
- ✅ Simple, predictable structure

For classic or Sage themes, use the full version of this package.

## License

MIT

## Credits

Built by [Imagewize](https://imagewize.com)
```

---

## Comparison: Universal vs Block Theme Only

| Feature | Universal Version | Block Theme Only |
|---------|------------------|------------------|
| **Lines of code** | ~2000+ | ~500 |
| **Theme detection** | Complex multi-type | None needed |
| **Build systems** | Vite, Webpack, Mix, per-block | Per-block only |
| **Service providers** | Acorn + WordPress | None |
| **Path configuration** | Multiple prompts | Fixed: `blocks/` |
| **Dependencies** | Laravel, Symfony, etc. | Minimal |
| **Setup file detection** | app/setup.php, functions.php, etc. | functions.php only |
| **Build pattern** | Multiple patterns | One: `{block}/build/block.json` |
| **Complexity** | High | Low |
| **Maintenance** | Ongoing | Minimal |
| **User confusion** | Possible | Very low |

---

## Migration Steps Summary

### From sage-native-block to block-theme version:

1. **Remove all Acorn/Laravel code** (90% of complexity gone)
2. **Remove theme detection logic** (no need)
3. **Remove path configuration** (always `blocks/`)
4. **Remove build system detection** (always per-block)
5. **Simplify to single command class** (~300 lines)
6. **Create standard stubs** (one structure)
7. **Add Moiraine templates** (same structure, different content)
8. **Write simple README** (focused docs)
9. **Test with Moiraine** (one theme type)
10. **Release as v3.0.0**

---

## File Checklist

### Must Create (15 files):

1. ✅ `wp-native-blocks.php` - Plugin entry
2. ✅ `src/CLI/CreateCommand.php` - Single command
3. ✅ `stubs/base/package.json.stub`
4. ✅ `stubs/base/.gitignore.stub`
5. ✅ `stubs/base/src/block.json.stub`
6. ✅ `stubs/base/src/index.js.stub`
7. ✅ `stubs/base/src/edit.jsx.stub`
8. ✅ `stubs/base/src/save.jsx.stub`
9. ✅ `stubs/base/src/style.scss.stub`
10. ✅ `stubs/base/src/editor.scss.stub`
11. ✅ `stubs/base/src/view.js.stub`
12. ✅ `composer.json` - Update
13. ✅ `README.md` - Rewrite
14. ✅ `readme.txt` - WordPress.org
15. ✅ Moiraine templates (45 files)

### Can Delete (remove complexity):

- ❌ `src/Console/SageNativeBlockAddSetupCommand.php`
- ❌ `src/Providers/SageNativeBlockServiceProvider.php`
- ❌ `src/Facades/SageNativeBlock.php`
- ❌ `config/sage-native-block.php`
- ❌ All Acorn-specific code

---

## Advantages of Block Theme Only Version

### For Users:
1. **Simpler to understand** - one way to do things
2. **Faster to use** - no prompts or detection
3. **Predictable** - same structure every time
4. **Modern** - focuses on current WordPress direction
5. **Less bugs** - less code = fewer issues

### For Maintainers:
1. **90% less code** - much easier to maintain
2. **No theme compatibility matrix** - test one theme type
3. **No detection edge cases** - fixed paths
4. **Clearer docs** - focused on one use case
5. **Faster releases** - less to test

### Trade-offs:
- ❌ Doesn't work with Sage themes
- ❌ Doesn't work with classic themes
- ❌ Not "universal"

But for block theme users (your target audience), it's **perfect**.

---

## Recommended Path Forward

### Option 1: Block Theme Only (Recommended)
- Build this simpler version
- Target Moiraine and similar FSE themes
- Ship in 2-3 weeks
- Much easier to maintain

### Option 2: Universal Version
- Full complexity
- Supports all themes
- Ship in 7+ weeks
- Ongoing maintenance burden

### Option 3: Both
- Release block-theme version as v3.0
- Add universal support as v4.0 later
- Iterate based on user feedback

**My recommendation:** Start with Option 1 (block theme only). If users request Sage support later, you can add it. But most WordPress themes are moving toward FSE anyway.

---

## Next Steps

If you choose the block-theme-only approach:

1. ✅ Create plugin entry point (`wp-native-blocks.php`)
2. ✅ Create single command class (`src/CLI/CreateCommand.php`)
3. ✅ Create base stubs (11 files)
4. ✅ Create Moiraine templates (5 blocks × 9 files)
5. ✅ Update composer.json
6. ✅ Write README
7. ✅ Test with Moiraine
8. ✅ Release!

**Estimated time:** 2-3 weeks vs 7+ weeks for universal version.

---

**Last Updated:** 2025-10-30
**Status:** Planning - Simplified Approach
**Target:** Block Themes (FSE) Only
**Estimated Complexity:** 10% of universal version
