# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

WP Native Blocks is a WordPress plugin that scaffolds native Gutenberg blocks for block themes (FSE). It's a complete rewrite (v3.0.0+) of the previous `imagewize/sage-native-block` package, which was designed for Sage/Acorn themes. The v3.0.0 rewrite removed all Laravel/Acorn dependencies and simplified the codebase by ~90%.

**Key architectural decision:** This plugin generates blocks in the user's active theme, not within the plugin itself. It's a scaffolding tool.

## Core Architecture

### Plugin Entry Point
- [wp-native-blocks.php](wp-native-blocks.php) - Standard WordPress plugin header
- Registers WP-CLI command: `wp block create`
- Constants: `WP_NATIVE_BLOCKS_VERSION`, `WP_NATIVE_BLOCKS_PATH`, `WP_NATIVE_BLOCKS_URL`

### Single Command Pattern
The plugin has ONE command class: [src/CLI/CreateCommand.php](src/CLI/CreateCommand.php)

This command:
1. Creates block structure in theme's `blocks/` directory (or custom via `--blocks-dir`)
2. Copies stub files from `stubs/` directory
3. Replaces placeholders: `{{BLOCK_NAME}}`, `{{BLOCK_SLUG}}`
4. Auto-updates theme's `functions.php` with block registration code (creates backup first)

### Template/Stub System

**Location:** `stubs/` directory

**Structure:**
```
stubs/
├── base/              # Default template (minimal starter)
├── generic/           # Generic templates (innerblocks, etc.)
└── moiraine/          # Moiraine theme-specific templates
```

**Template resolution logic** (in [CreateCommand.php:72-84](src/CLI/CreateCommand.php#L72-L84)):
- `base` template → `stubs/base/`
- `moiraine-*` template → `stubs/moiraine/{name}` (strips `moiraine-` prefix)
- Everything else → `stubs/generic/{name}`

**Stub files:**
All stub files use `.stub` extension and contain placeholders. The command strips `.stub` when copying.

Example: `stubs/base/src/block.json.stub` → `blocks/hero/src/block.json`

### Block Output Structure
Generated blocks follow this pattern:
```
blocks/{name}/
├── package.json          # @wordpress/scripts
├── .gitignore
└── src/
    ├── block.json        # Block metadata
    ├── index.js          # Registration
    ├── edit.jsx          # Editor component (React)
    ├── save.jsx          # Save component (React)
    ├── style.scss        # Frontend styles
    ├── editor.scss       # Editor styles
    └── view.js           # Optional frontend JS
```

After `npm run build`:
```
build/
├── block.json
├── index.js
├── index.css          # Compiled editor styles
└── style-index.css    # Compiled frontend styles
```

### Block Registration Pattern
The plugin auto-injects this code into the theme's `functions.php`:

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

**Important:** This registration code scans `build/` subdirectories, not `src/`. Blocks must be built before they work.

## Development Commands

### Code Quality
```bash
# Format PHP code with Laravel Pint
composer format
# or
vendor/bin/pint
```

### Testing the Plugin
```bash
# Install in a WordPress site
composer require imagewize/wp-native-blocks --dev

# Test the command
wp block create test/example
wp block create test/hero --template=moiraine-hero
wp block create test/container --template=innerblocks
wp block create test/custom --blocks-dir=custom-blocks
```

### Building Generated Blocks
```bash
# After creating a block, navigate to it
cd blocks/your-block

# Install dependencies
npm install

# Development (hot reload)
npm run start

# Production build
npm run build
```

## Key Implementation Details

### Placeholder Replacement
Only 2 placeholders exist:
- `{{BLOCK_NAME}}` - Full block name (e.g., `imagewize/hero`)
- `{{BLOCK_SLUG}}` - Slugified version (e.g., `imagewize-hero`)

Applied in [CreateCommand.php:111-112](src/CLI/CreateCommand.php#L111-L112)

### Functions.php Safety
Before modifying `functions.php`, the command:
1. Checks if registration already exists (line 133)
2. Creates timestamped backup: `functions.php.backup-{date}-{time}` (line 142)
3. Appends registration code to end of file (line 145)

### Template Validation
The command checks if template directory exists before proceeding (line 81-84). If not found, shows error with template name.

### Namespace
- Old (v2.x): `Imagewize\SageNativeBlockPackage\`
- New (v3.x): `Imagewize\WpNativeBlocks\`

PSR-4 autoloading configured in [composer.json](composer.json#L12-L15)

## Adding New Templates

To add a new template called `my-template`:

1. Create directory: `stubs/generic/my-template/` (or `stubs/moiraine/my-template/`)
2. Add stub files with `.stub` extension:
   - `package.json.stub`
   - `src/block.json.stub`
   - `src/index.js.stub`
   - `src/edit.jsx.stub`
   - `src/save.jsx.stub`
   - `src/style.scss.stub`
   - `src/editor.scss.stub`
   - `src/view.js.stub`
3. Use placeholders `{{BLOCK_NAME}}` and `{{BLOCK_SLUG}}` where needed
4. Test: `wp block create test/example --template=my-template`

**Note:** Moiraine templates should be prefixed with `moiraine-` in the command but stored without prefix in directory name.

## Legacy Files

These files are remnants from v2.x (Sage/Acorn) and can be ignored or removed:
- [config/sage-native-block.php](config/sage-native-block.php) - Old config system
- [resources/views/sage-native-block.blade.php](resources/views/sage-native-block.blade.php) - Old Blade templates
- `stubs/block/` - Old flat structure (v2.x blocks)
- `stubs/themes/` - Old theme-specific stubs (Nynaeve, etc.)

The current structure uses `stubs/base/`, `stubs/generic/`, and `stubs/moiraine/` only.

## WordPress Context

- Minimum WordPress: 6.0+
- Minimum PHP: 8.0+
- Target: Block themes (FSE) only
- Build tool: `@wordpress/scripts` (standard WordPress tooling)
- Block API version: 3 (see `block.json.stub` files)

## Common Workflows

**Creating a basic block:**
```bash
wp block create vendor/blockname
cd blocks/blockname
npm install
npm run start
```

**Creating from template:**
```bash
wp block create vendor/hero --template=moiraine-hero
cd blocks/hero
npm install
npm run start
```

**Custom blocks directory:**
```bash
wp block create vendor/example --blocks-dir=custom-blocks
cd custom-blocks/example
npm install
```

**Build for production:**
```bash
cd blocks/blockname
npm run build
```

## Version Management

When releasing a new version, update version numbers in these files:

1. **[wp-native-blocks.php](wp-native-blocks.php)** - Two locations:
   - Plugin header: `Version: X.X.X` (line 5)
   - PHP constant: `WP_NATIVE_BLOCKS_VERSION` (line 19)

2. **[CHANGELOG.md](CHANGELOG.md)** - Add new version section:
   - Create new `## [X.X.X] - YYYY-MM-DD` section under `[Unreleased]`
   - Document changes under `### Added`, `### Changed`, `### Removed`, etc.
   - Update comparison links at bottom of file

3. **[readme.txt](readme.txt)** (if publishing to WordPress.org):
   - Update `Stable tag:` in header
   - Add version notes to changelog section

**Note:** [composer.json](composer.json) does NOT need version updates - Composer manages versions via git tags.
