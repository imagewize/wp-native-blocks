# Acorn Sage Native Block Package

This package helps you create and manage native Gutenberg blocks in your [Sage](https://roots.io/sage/) theme. It provides a convenient command to scaffold block files and automatically register them with WordPress.

## Features

- **Multiple Block Templates** - Choose from pre-configured templates for common block patterns
- Scaffolds a complete native block structure in your Sage theme
- Automatically adds block registration code to your theme's setup file
- Creates all necessary block files (JS, JSX, CSS) with proper configuration
- Handles proper block naming with vendor prefixes
- Creates editor and frontend styles for your blocks
- Ensures proper imports of block JS files
- **80% faster development** - Start with pre-configured templates instead of building from scratch

## Installation

You can install this package with Composer from your Sage 11+ theme root directory (not from the Bedrock root):

```bash
composer require imagewize/sage-native-block --dev
```

**NB** You can drop `--dev` but then it will be included in your production build.


## Configuration

You can publish the config file with:

```shell
wp acorn vendor:publish --provider="Imagewize\SageNativeBlockPackage\Providers\SageNativeBlockServiceProvider"
```

**NB**: This is recommended to customize template settings. The package includes default configuration with 5 block templates and typography/spacing presets.

## Usage

### Creating a new block

Run the sage-native-block command to create a block with default settings:

```shell
wp acorn sage-native-block:add-setup
```

This will:
1. Prompt you to select a block template (or use the default)
2. Create the block in your theme's `resources/js/blocks` directory
3. Add block registration code to your theme's `app/setup.php` if not already present
4. Update `resources/js/editor.js` to import the block files

### Block Templates

The package includes templates in two categories:

#### 🟢 Generic Templates (Recommended)
Universal templates that work with ANY theme - no dependencies required:

| Template | Description | Use Case |
|----------|-------------|----------|
| **basic** | Simple block with InnerBlocks support | General-purpose container blocks |
| **innerblocks** | Minimal heading and content template | Section blocks (add your own styles) |
| **two-column** | Basic two-column layout structure | Feature comparisons, benefits |
| **statistics** | Simple statistics layout | Impact metrics, key numbers |
| **cta** | Basic call-to-action with button | Lead generation, conversions |

#### 🎨 Theme-Specific Templates

Real-world examples from production themes. Currently featuring templates from the **[Nynaeve theme](https://github.com/imagewize/nynaeve)** by Imagewize.

⚠️ **Nynaeve theme requirements:**
- Font families: `montserrat`, `open-sans`
- Color slugs: `main`, `secondary`, `tertiary`, `base`
- Font sizes: `3xl`, `2xl`, `xl`, `lg`, `base`, `sm`

| Template | Description | Use Case |
|----------|-------------|----------|
| **nynaeve-innerblocks** | Pre-styled with Nynaeve typography | Production-ready container |
| **nynaeve-two-column** | Card-style layout from Nynaeve | Polished two-column sections |
| **nynaeve-statistics** | Complete statistics from Nynaeve | Production-ready stats display |
| **nynaeve-cta** | Styled CTA from Nynaeve theme | Ready-to-use call-to-action |

> 💡 **Tip:** Use generic templates for universal compatibility, or Nynaeve templates if your theme matches its setup. See [`stubs/themes/nynaeve/README.md`](stubs/themes/nynaeve/README.md) for detailed requirements.

### Interactive template selection

Simply run the command and choose your template:

```shell
wp acorn sage-native-block:add-setup my-block
```

You'll be prompted:
```
Which template would you like to use?
  [0] Basic Block
  [1] InnerBlocks Container (Generic)
  [2] Two Column Layout (Generic)
  [3] Statistics Section (Generic)
  [4] Call-to-Action (Generic)
  [5] InnerBlocks (Nynaeve Theme) - montserrat, open-sans fonts
  [6] Two Column (Nynaeve Theme) - montserrat, open-sans fonts
  [7] Statistics (Nynaeve Theme) - montserrat, open-sans fonts
  [8] CTA (Nynaeve Theme) - montserrat, open-sans fonts
```

### Direct template selection

Use the `--template` flag to specify a template:

```shell
# Generic templates (work everywhere)
wp acorn sage-native-block:add-setup my-stats --template=statistics
wp acorn sage-native-block:add-setup my-cta --template=cta
wp acorn sage-native-block:add-setup my-columns --template=two-column
wp acorn sage-native-block:add-setup my-container --template=innerblocks

# Nynaeve theme templates (requires Nynaeve theme.json setup)
wp acorn sage-native-block:add-setup my-stats --template=nynaeve-statistics
wp acorn sage-native-block:add-setup my-cta --template=nynaeve-cta
wp acorn sage-native-block:add-setup my-columns --template=nynaeve-two-column
wp acorn sage-native-block:add-setup my-container --template=nynaeve-innerblocks
```

### Creating a block with custom vendor prefix

Add your own vendor namespace:

```shell
wp acorn sage-native-block:add-setup imagewize/my-cool-block
```

This creates a block with proper namespace `imagewize/my-cool-block`.

### Combining all options

```shell
# Generic template with vendor prefix
wp acorn sage-native-block:add-setup imagewize/my-stats --template=statistics --force

# Nynaeve theme template with vendor prefix
wp acorn sage-native-block:add-setup imagewize/my-stats --template=nynaeve-statistics --force
```

### Skipping confirmation

Use the `--force` flag to skip the confirmation prompt:

```shell
wp acorn sage-native-block:add-setup --force
wp acorn sage-native-block:add-setup my-block-name --template=cta --force
```

## Block Structure

The command creates the following files in your `resources/js/blocks/<block-name>/` directory:

- `block.json` - Block metadata and configuration
- `index.js` - Main block entry point
- `editor.jsx` - React component for the editor
- `save.jsx` - React component for the frontend
- `editor.css` - Styles for the block in the editor
- `style.css` - Styles for the block on the frontend
- `view.js` - Frontend JavaScript for the block

## Notes

- The block.json's `name` will always include a vendor prefix (e.g., `vendor/cool-block` or `imagewize/my-cool-block`)
- The block.json's `textdomain` will match the vendor prefix
- The default CSS class will be based on the full block name (e.g., `wp-block-vendor-cool-block`)
- Block styles and scripts are automatically registered via the `block.json` file
- The command will also ensure your theme's `editor.js` file imports all block index.js files

## File Structure Example

For a block named `imagewize/testimonial`, the command will create:

```
resources/js/blocks/testimonial/
├── block.json
├── editor.css
├── editor.jsx
├── index.js
├── save.jsx
├── style.css
└── view.js
```

## Template Customization

### Customizing Typography and Spacing

After publishing the config file, you can customize typography and spacing presets in `config/sage-native-block.php`:

```php
'typography_presets' => [
    'main_heading' => [
        'fontFamily' => 'your-font',
        'fontSize' => '4xl',
        'fontWeight' => '800',
        'textColor' => 'primary',
    ],
    // ... customize other presets
],

'spacing_presets' => [
    'section_bottom' => '5rem',
    'column_gap_large' => '4rem',
    // ... customize spacing
],
```

These presets are used in the InnerBlocks templates to ensure consistency across your theme.

### Creating Custom Templates

You can add your own custom templates by:

1. Creating a new stub directory: `stubs/your-template/`
2. Adding all required block files (block.json, editor.jsx, save.jsx, etc.)
3. Registering it in the config:

```php
'templates' => [
    // ... existing templates
    'your-template' => [
        'name' => 'Your Template Name',
        'description' => 'Description of your template',
        'stub_path' => 'your-template',
    ],
],
```

## Benefits of Using Templates

- **80% faster development** - Start with pre-configured templates instead of building from scratch
- **Consistent patterns** - All blocks follow established structure and best practices
- **Theme integration** - Templates use theme.json values for typography and colors
- **Proper InnerBlocks setup** - Avoid common mistakes with InnerBlocks configuration
- **Reduced errors** - Well-tested templates reduce debugging time
- **Learning tool** - See WordPress block best practices in generated code

## Changelog

### Version 2.0.0 (Phase 1 Implementation)

- Added multiple block templates (basic, innerblocks, two-column, statistics, cta)
- Added interactive template selection
- Added `--template` option for direct template selection
- Added configuration file with typography and spacing presets
- Updated documentation with template examples
- Backwards compatible - existing usage still works
