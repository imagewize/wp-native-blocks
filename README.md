# WP Native Blocks

WordPress plugin for scaffolding native Gutenberg blocks in block themes with per-block builds.

## Features

- 🎨 Per-block builds with @wordpress/scripts
- ⚛️ React-based blocks with JSX
- 📦 Standard block structure (src/ → build/)
- 🎯 Designed for block themes (FSE)
- 🚀 Moiraine theme templates included
- 💪 Simple WP-CLI commands

## Requirements

- WordPress 6.0+
- PHP 8.0+
- WP-CLI
- Node.js & npm (for building blocks)

## Installation

### Via Composer

```bash
composer require imagewize/wp-native-blocks --dev
```

### Via WordPress Plugin

1. Download and install the plugin
2. Activate in WordPress admin
3. Use via WP-CLI

## Usage

### Basic Block Creation

```bash
wp block create vendor/block-name
```

This creates a complete block structure:
```
blocks/block-name/
├── package.json          # @wordpress/scripts setup
├── .gitignore
└── src/                 # Source files
    ├── block.json       # Block metadata
    ├── index.js         # Registration
    ├── edit.jsx         # Editor component (React)
    ├── save.jsx         # Frontend component (React)
    ├── style.scss       # Frontend styles
    ├── editor.scss      # Editor styles
    └── view.js          # Optional frontend JS
```

### Using Templates

```bash
# Use Moiraine hero template
wp block create imagewize/hero --template=moiraine-hero

# Use generic innerblocks template
wp block create imagewize/container --template=innerblocks
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

The build outputs to `build/` directory:
```
build/
├── block.json
├── index.js
├── index.css          # Editor styles
└── style-index.css    # Frontend styles
```

## Available Templates

### Base Template
- `base` - Minimal starter with RichText example

### Generic Templates
- `innerblocks` - Container with InnerBlocks support

### Moiraine Theme Templates
- `moiraine-hero` - Hero section with background image

More templates coming soon!

## Block Registration

The plugin automatically adds this code to your theme's `functions.php`:

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

Use placeholders in your stubs:
- `{{BLOCK_NAME}}` - Full block name (e.g., `vendor/block-name`)
- `{{BLOCK_SLUG}}` - Slug version (e.g., `vendor-block-name`)

## Workflow

1. **Create block:** `wp block create vendor/name --template=moiraine-hero`
2. **Install dependencies:** `cd blocks/name && npm install`
3. **Start development:** `npm run start`
4. **Edit in WordPress:** Block appears in editor automatically
5. **Build for production:** `npm run build`

## Why Block Themes Only?

This plugin is specifically designed for modern block themes (FSE) because:

- ✅ Standardized block location (`blocks/`)
- ✅ Per-block builds (simple, independent)
- ✅ React-based architecture
- ✅ Clean, predictable structure
- ✅ Focused on modern WordPress

## File Structure

Each block follows this consistent pattern:

```
your-block/
├── package.json          # Dependencies and scripts
├── .gitignore           # Ignores node_modules/ and build/
├── src/                 # Your source code
│   ├── block.json       # Block configuration
│   ├── index.js         # Main entry point
│   ├── edit.jsx         # Editor component
│   ├── save.jsx         # Save component
│   ├── style.scss       # Frontend styles
│   ├── editor.scss      # Editor-only styles
│   └── view.js          # Optional interactivity
└── build/               # Compiled output (gitignored)
    ├── block.json       # Copied from src/
    ├── index.js         # Compiled JavaScript
    ├── index.css        # Compiled editor styles
    └── style-index.css  # Compiled frontend styles
```

## License

MIT

## Credits

Built by [Imagewize](https://imagewize.com)
