# Developer Documentation

This document contains technical information for developers working on the sage-native-block package itself.

## Package Architecture

### Command Structure

The main command is `SageNativeBlockCommand` located in `src/Console/SageNativeBlockCommand.php`.

**Key methods:**
- `handle()` - Main command execution
- `copyBlockStubs()` - Copies and processes template files
- `updateJsFile()` - Manages editor.js imports
- `replaceBlockName()` - Processes block.json placeholders
- `replaceEditorClassName()` - Processes editor.jsx placeholders
- `replaceCssClassName()` - Processes CSS file placeholders

### Template System

Templates are stored in the `stubs/` directory:

```
stubs/
├── block/              # Basic template (original)
├── generic/            # Universal templates (no theme dependencies)
│   ├── innerblocks/
│   ├── two-column/
│   ├── statistics/
│   └── cta/
└── themes/             # Theme-specific templates
    └── nynaeve/
        ├── innerblocks/
        ├── two-column/
        ├── statistics/
        └── cta/
```

### Placeholder System

The command uses placeholders to customize templates:

| Placeholder | Location | Replaced With | Example |
|------------|----------|---------------|---------|
| `vendor/example-block` | block.json | Full block name | `imagewize/my-block` |
| `{{BLOCK_CLASS_NAME}}` | editor.jsx | CSS class name | `wp-block-imagewize-my-block` |
| `.wp-block-vendor-example-block` | CSS files | CSS class selector | `.wp-block-imagewize-my-block` |
| `'vendor'` | block.json textdomain | Vendor prefix | `'imagewize'` |

### Configuration

Configuration is managed in `config/sage-native-block.php`:

```php
return [
    'templates' => [
        'template-key' => [
            'name' => 'Display Name',
            'description' => 'Template description',
            'stub_path' => 'path/to/stub',
        ],
    ],
    'default_template' => 'basic',
    'typography_presets' => [...],
    'spacing_presets' => [...],
];
```

## Creating Custom Templates

### Template File Requirements

Each template must include these 7 files:

1. `block.json` - Block metadata
2. `index.js` - Block registration
3. `editor.jsx` - Edit component
4. `save.jsx` - Save component
5. `editor.css` - Editor styles
6. `style.css` - Frontend styles
7. `view.js` - Frontend JavaScript

### Template Best Practices

**1. Use Placeholders:**
```jsx
// editor.jsx
const blockProps = useBlockProps({
  className: '{{BLOCK_CLASS_NAME}}'
});
```

**2. Use Semantic Names in block.json:**
```json
{
  "name": "vendor/example-block",
  "textdomain": "vendor"
}
```

**3. Use Placeholder Classes in CSS:**
```css
.wp-block-vendor-example-block {
  /* styles */
}
```

## Adding a New Template Type

### Step 1: Create Template Files

```bash
mkdir -p stubs/generic/my-template
```

Add all 7 required files with proper placeholders.

### Step 2: Register in Config

Add to `config/sage-native-block.php`:

```php
'templates' => [
    // ... existing templates
    'my-template' => [
        'name' => 'My Template Name',
        'description' => 'What this template does',
        'stub_path' => 'generic/my-template',
    ],
],
```

### Step 3: Test

```bash
wp acorn sage-native-block:add-setup test-block --template=my-template
```

## Theme-Specific Templates

### Directory Structure

```
stubs/themes/{theme-name}/
├── README.md           # Theme requirements
├── innerblocks/
├── two-column/
├── statistics/
└── cta/
```

### Configuration Naming

Use pattern: `{theme-name}-{template-type}`

```php
'nynaeve-statistics' => [
    'name' => 'Statistics (Nynaeve Theme)',
    'description' => 'Full statistics section from Nynaeve theme',
    'stub_path' => 'themes/nynaeve/statistics',
],
```

### Documentation Requirements

Each theme directory must include a `README.md` with:

1. Theme name and author
2. Link to theme repository (if public)
3. Required theme.json settings:
   - Font families with slugs
   - Color palette with slugs
   - Font size scale
4. Usage examples
5. Troubleshooting common issues

## File Processing Flow

### 1. Command Execution
```
User runs command
  ↓
Parse block name & vendor
  ↓
Get template selection
  ↓
Validate template exists
  ↓
Show header & confirmation
```

### 2. Setup Phase
```
Check if setup.php needs update
  ↓
Create backup if needed
  ↓
Add registration code
  ↓
Update editor.js imports
```

### 3. File Copying Phase
```
For each template file:
  ↓
Read source file
  ↓
Apply replacements (block.json, editor.jsx, CSS)
  ↓
Write to target directory
  ↓
Track success/failure
```

### 4. Output Display
```
Show Setup section
  ↓
Show Files section (grouped)
  ↓
Display success message
```

## Testing

### Manual Testing Checklist

- [ ] Basic template works
- [ ] Generic templates work
- [ ] Theme-specific templates work
- [ ] Vendor prefix handling
- [ ] Default vendor fallback
- [ ] Force flag skips confirmation
- [ ] Interactive selection works
- [ ] Invalid template shows error
- [ ] Setup.php already exists (doesn't duplicate)
- [ ] Editor.js already exists (doesn't duplicate)
- [ ] Overwriting existing block works
- [ ] Relative paths display correctly
- [ ] Check marks display correctly
- [ ] Color output works

### Test Commands

```bash
# Test basic
wp acorn sage-native-block:add-setup test-basic --template=basic

# Test generic
wp acorn sage-native-block:add-setup imagewize/test-generic --template=statistics

# Test theme-specific
wp acorn sage-native-block:add-setup imagewize/test-nynaeve --template=nynaeve-cta

# Test defaults
wp acorn sage-native-block:add-setup test-default

# Test force
wp acorn sage-native-block:add-setup test-force --template=cta --force

# Test overwrite
wp acorn sage-native-block:add-setup imagewize/test-generic --template=cta
```

## Code Style

This package uses Laravel Pint for code formatting:

```bash
composer run pint
```

Configuration is in `pint.json` (Laravel preset).

## Release Process

### 1. Update Version

- Update `CHANGELOG.md` with new version
- Follow Semantic Versioning (MAJOR.MINOR.PATCH)

### 2. Tag Release

```bash
git tag -a v1.x.x -m "Release v1.x.x"
git push origin v1.x.x
```

### 3. Create GitHub Release

- Go to GitHub releases
- Select the tag
- Copy relevant CHANGELOG section
- Publish release

## Technical Notes

### Block Registration

The command adds this code to `app/setup.php`:

```php
add_action('init', function () {
    $blocks_dir = get_template_directory() . '/resources/js/blocks';

    if (!is_dir($blocks_dir)) {
        return;
    }

    $block_folders = scandir($blocks_dir);

    foreach ($block_folders as $folder) {
        if ($folder === '.' || $folder === '..') {
            continue;
        }

        $block_json_path = $blocks_dir . '/' . $folder . '/block.json';

        if (file_exists($block_json_path)) {
            register_block_type($block_json_path);
        }
    }
}, 10);
```

This automatically registers all blocks in the `blocks/` directory.

### Editor Imports

The command ensures this code exists in `resources/js/editor.js`:

```js
/**
 * Import editor blocks
 */
import.meta.glob('./blocks/**/index.js', { eager: true });
```

This uses Vite's glob import to automatically load all block entry points.

### Directory Naming

Block directories use only the block name (without vendor):
- `imagewize/my-block` → `blocks/my-block/`
- `vendor/my-block` → `blocks/my-block/`
- `acme/my-block` → `blocks/my-block/`

This keeps the directory structure flat and clean.

## Troubleshooting Development Issues

### Command Not Found

Ensure the service provider is registered in Acorn:

```php
// config/app.php
'providers' => [
    Imagewize\SageNativeBlockPackage\Providers\SageNativeBlockServiceProvider::class,
],
```

### Template Not Found

Check:
1. Template exists in `stubs/` directory
2. Template is registered in `config/sage-native-block.php`
3. `stub_path` in config matches actual directory path

### Placeholders Not Replaced

Check the replacement methods are called in `copyBlockStubs()`:
- `block.json` → `replaceBlockName()`
- `editor.jsx` → `replaceEditorClassName()`
- `style.css`, `editor.css` → `replaceCssClassName()`
- `view.js` → `replaceJsClassName()`

### Colors Not Showing

Laravel Command class methods support tags:
- `<fg=cyan>text</>`
- `<fg=yellow>text</>`
- `<fg=green>text</>`
- `<fg=red>text</>`

If colors don't show, the terminal may not support them.

## Contributing

When contributing to this package:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run `composer run pint` to format code
5. Test thoroughly with the checklist above
6. Submit a pull request

### Pull Request Guidelines

- Clear description of changes
- Update CHANGELOG.md
- Add tests if applicable
- Update documentation as needed
- Screenshots for UI changes

## Resources

- [Roots Acorn Documentation](https://roots.io/acorn/)
- [WordPress Block Editor Handbook](https://developer.wordpress.org/block-editor/)
- [Laravel Command Documentation](https://laravel.com/docs/artisan)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)
