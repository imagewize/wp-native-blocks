<p align="center">
  <img src="assets/icon.svg" alt="WP Native Blocks" width="120" height="120">
</p>

# WP Native Blocks

WordPress plugin for scaffolding native Gutenberg blocks in block themes with per-block builds.


## Features

- Per-block builds with @wordpress/scripts
- React-based blocks with JSX
- Standard block structure (src/ → build/)
- Designed for block themes (FSE)
- Moiraine theme templates included
- Simple WP-CLI commands

## Requirements

- WordPress 6.0+
- PHP 8.0+
- WP-CLI
- Node.js & npm (for building blocks)

## Installation

### Via Composer (Recommended)

This is a development tool for scaffolding blocks, so install it as a dev dependency:

```bash
composer require imagewize/wp-native-blocks --dev
```

**Note:** The `--dev` flag is recommended because this plugin is only used during block development, not in production. The version constraint (`:^3.0`) is required.

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

### WordPress Multisite Support

The plugin supports creating blocks for specific sites in a multisite installation. Use the `--url` parameter to target a specific site and its active theme (including child themes):

```bash
# Create block for main site
wp block create imagewize/hero --url=https://example.com --path=web/wp

# Create block for subsite (targets the subsite's active theme)
wp block create imagewize/stats --url=https://example.com/blog/ --path=web/wp

# Create block for child theme on subsite
wp block create imagewize/custom --url=https://example.com/shop/ --path=web/wp
```

**Important:** The plugin must be network-activated for multisite usage:

```bash
wp plugin activate wp-native-blocks --network --path=web/wp
```

**How it works:**
- Uses `get_stylesheet_directory()` to support both parent and child themes
- Respects the `--url` parameter to target specific sites in multisite
- Automatically creates blocks in the active theme of the specified site
- Updates the correct theme's `functions.php` file

### Bedrock/Trellis Development Environments

For Bedrock-based projects with Trellis VM:

```bash
# From host machine (recommended)
trellis vm shell --workdir /srv/www/example.com/current -- wp block create imagewize/hero --path=web/wp

# From inside VM shell
trellis vm shell
cd /srv/www/example.com/current
wp block create imagewize/hero --path=web/wp

# For multisite in Trellis VM
trellis vm shell --workdir /srv/www/example.com/current -- wp block create imagewize/stats --url=http://example.test/blog/ --path=web/wp
```

**Why `--path=web/wp` is needed:**
- WP-CLI needs to locate WordPress core to load plugins
- Without `--path`, WP-CLI can't find the plugin or WordPress installation
- The `web/wp` directory contains WordPress core in Bedrock structure

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
    $blocks_dir = get_stylesheet_directory() . '/blocks';

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

**Note:** The code uses `get_stylesheet_directory()` instead of `get_template_directory()` to support child themes. This ensures blocks are loaded from the active theme (child or parent).

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

- Standardized block location (`blocks/`)
- Per-block builds (simple, independent)
- React-based architecture
- Clean, predictable structure
- Focused on modern WordPress

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

Icon: [IconPark Block One](https://blade-ui-kit.com/blade-icons/iconpark-blockone-o) from [Blade UI Kit](https://blade-ui-kit.com/blade-icons)

## Foot Note

> **Version 3.0.0+** - This package evolved from [imagewize/sage-native-block](https://github.com/imagewize/sage-native-block) which was designed for Sage/Acorn themes. Version 3.0.0 represents a complete rewrite focused on standard WordPress block themes (FSE). See [CHANGELOG.md](CHANGELOG.md) for full migration details.