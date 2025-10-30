# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [3.0.0] - 2025-10-30

### Changed - MAJOR BREAKING CHANGES

This is a complete rewrite focused exclusively on **WordPress block themes (FSE)**. The package has been renamed and restructured as a standalone WordPress plugin.

**Package Rename:**
- Old: `imagewize/sage-native-block` (Sage/Acorn-specific)
- New: `imagewize/wp-native-blocks` (Block theme plugin)
- Type changed from `package` to `wordpress-plugin`
- Repository renamed to `wp-native-blocks`

**Architecture Changes:**
- ❌ **Removed** all Acorn/Laravel dependencies
- ❌ **Removed** Sage theme integration
- ❌ **Removed** service providers, facades, and Acorn-specific code
- ✅ **Added** standalone WordPress plugin architecture
- ✅ **Added** direct WP-CLI command registration
- ✅ **Simplified** to ~300 lines vs thousands in v2.x

**Command Changes:**
- Old: `wp acorn sage-native-block:create`
- New: `wp block create`
- Command now registered directly via WP-CLI (no Acorn required)
- Much simpler, focused API

**Block Structure Changes:**
- Location: `blocks/` directory (not `resources/js/blocks/`)
- Build system: Per-block builds with `@wordpress/scripts`
- Structure: `blocks/{name}/src/` → `blocks/{name}/build/`
- Each block has its own `package.json` and independent build
- Registration: Auto-registers from `build/block.json` path

**File Changes:**
- ✅ React components use `.jsx` extension (was `.jsx` but now enforced)
- ✅ Source in `src/` directory, compiled to `build/`
- ✅ Uses `.scss` for styles (compiled by @wordpress/scripts)
- ✅ Standard WordPress build tooling only

### Added

- **Plugin Entry Point** - `wp-native-blocks.php`
  - Standard WordPress plugin header
  - WP-CLI command registration
  - No framework dependencies

- **Simplified Command Class** - `src/CLI/CreateCommand.php`
  - Single, focused command for creating blocks
  - Auto-updates theme's `functions.php` with registration code
  - Supports `--template` and `--blocks-dir` options

- **Base Template Stubs** - `stubs/base/src/`
  - `package.json.stub` - @wordpress/scripts setup
  - `block.json.stub` - Block metadata
  - `index.js.stub` - Registration entry point
  - `edit.jsx.stub` - Editor component (React with .jsx)
  - `save.jsx.stub` - Save component (React with .jsx)
  - `style.scss.stub` - Frontend styles
  - `editor.scss.stub` - Editor-only styles
  - `view.js.stub` - Optional frontend JavaScript
  - `.gitignore.stub` - Ignores node_modules/ and build/

- **Generic Templates** - `stubs/generic/`
  - `innerblocks` - Container block with InnerBlocks support

- **Moiraine Theme Templates** - `stubs/moiraine/`
  - `hero` - Full-featured hero section with background image
  - Uses Moiraine theme.json variables
  - Includes InspectorControls and MediaUpload
  - RangeControl for height adjustment

- **WordPress.org Ready** - `readme.txt`
  - Standard WordPress plugin readme format
  - Ready for WordPress.org submission
  - Tags: gutenberg, blocks, development, cli, scaffolding, fse

- **Complete Documentation Rewrite**
  - New README.md focused on block themes
  - Simple, clear usage examples
  - Per-block build workflow documentation
  - Custom template creation guide

### Removed

- ❌ Acorn/Laravel service providers (`src/Providers/`)
- ❌ Acorn facades (`src/Facades/`)
- ❌ Acorn console commands (`src/Console/`)
- ❌ Laravel/Acorn configuration system
- ❌ Theme detection logic
- ❌ Multiple build system support
- ❌ Sage-specific path handling
- ❌ Complex template category system
- ❌ Interactive prompts (simplified to CLI args)
- ❌ Theme.json preset configuration
- ❌ Vendor publishing system

### Migration from 2.x

**NOT BACKWARDS COMPATIBLE** - This is a complete rewrite for a different use case.

If you're using Sage themes, stay on v2.x:
```bash
composer require imagewize/sage-native-block:^2.0 --dev
```

If you're using block themes, upgrade to v3.x:
```bash
composer remove imagewize/sage-native-block
composer require imagewize/wp-native-blocks --dev
```

**Key Differences:**

| Feature | v2.x (Sage) | v3.x (Block Themes) |
|---------|-------------|---------------------|
| **Framework** | Acorn/Laravel | None (pure WP) |
| **Command** | `wp acorn sage-native-block:create` | `wp block create` |
| **Location** | `resources/js/blocks/` | `blocks/` |
| **Build** | Theme-level | Per-block |
| **Registration** | `app/setup.php` | `functions.php` |
| **Structure** | Flat in block folder | `src/` → `build/` |
| **Target** | Sage themes | Block themes (FSE) |

**New Workflow:**

```bash
# 1. Create block
wp block create imagewize/hero --template=moiraine-hero

# 2. Install dependencies
cd blocks/hero
npm install

# 3. Start development
npm run start

# 4. Build for production
npm run build
```

### Technical Details

**Namespace Change:**
- Old: `Imagewize\SageNativeBlockPackage\`
- New: `Imagewize\WpNativeBlocks\`

**PHP Requirements:**
- Minimum PHP: 8.0 (was 8.1)
- WordPress: 6.0+

**File Organization:**
```
wp-native-blocks/
├── wp-native-blocks.php      # Plugin entry point
├── readme.txt                 # WordPress.org readme
├── composer.json              # Package definition
├── src/
│   └── CLI/
│       └── CreateCommand.php  # Single command class
└── stubs/
    ├── base/                  # Default template
    ├── generic/               # Generic templates
    └── moiraine/              # Moiraine theme templates
```

### Why This Change?

**Problems with v2.x:**
- Too complex for simple block scaffolding
- Locked to Sage/Acorn ecosystem
- Per-theme builds added complexity
- Not suitable for standard block themes

**Benefits of v3.x:**
- 90% simpler codebase
- Works with any block theme
- Standard WordPress tooling
- Independent block builds
- Easier to maintain and extend
- Clear, predictable structure

### Notes for Contributors

This is essentially a new package with a new purpose. v2.x will remain available for Sage users, while v3.x targets the broader block theme ecosystem.

If you need Sage support, use v2.x. If you're building block themes, use v3.x.

## [2.0.1] - 2025-10-16

### Fixed

- **Config Loading Issue** - Fixed template configuration not loading in production environments
  - Added fallback config loading directly from package config file
  - Resolves "No templates found" error when running `wp acorn sage-native-block:create`
  - Templates now load correctly even if Laravel config() helper fails
  - Improved resilience for production WordPress/Acorn environments

## [2.0.0] - 2025-10-14

### Changed - BREAKING CHANGES

- **Command renamed** - `sage-native-block:add-setup` is now `sage-native-block:create`
  - More intuitive naming that better reflects the command's purpose
  - Old command still works with deprecation warning for backward compatibility
  - Please update scripts to use the new command name

### Added

- **Fully Interactive Mode** - Command now prompts for all inputs when run without arguments
  - Interactive block name prompt with examples
  - Interactive vendor prefix prompt with default option
  - Visual welcome message and guided flow
  - Much improved user experience for developers creating blocks
- **Smart Input Handling** - Command adapts based on provided arguments
  - No arguments: Fully interactive mode
  - Block name only: Prompts for template selection
  - All arguments: Non-interactive automation mode

### Changed

- **Improved Command Output** - Redesigned terminal UI for better readability and user experience
  - Added visual separators (━) and section headers (Setup/Files) for clear organization
  - Replaced verbose messages with clean check marks (✓) and error marks (✗)
  - Grouped related file operations (e.g., "block.json, index.js") instead of per-file output
  - Changed from absolute paths to relative paths for cleaner, more readable output
  - Added color-coded output: cyan for block names, yellow for sections, green for success
  - Consolidated "already exists" warnings into simple status indicators
  - Simplified confirmation prompt from verbose paragraph to concise "Continue?"
  - Added emoji header (🔨) for visual identification
  - Final success message clearly shows block location with relative path

### Improved

- Better onboarding experience for new users
- Clearer command description emphasizing interactive workflow
- Updated documentation showcasing interactive-first approach
- Maintained backward compatibility for automation via CLI arguments
- Command output is now ~80% less verbose while maintaining all necessary information
- Better visual hierarchy makes it easier to scan command progress at a glance
- Consistent styling whether setup already exists or is being created fresh
- Professional, modern terminal UI that matches industry standards for CLI tools
- **Hierarchical Template Selection** - Two-step template selection process for better organization
  - Step 1: Choose template category (Basic Block, Generic Templates, or Theme-specific templates)
  - Step 2: Select specific template within chosen category
  - Improved user experience with logical grouping of related templates
  - Categories are presented with clear descriptions to guide selection
- **Dynamic Theme Detection** - Template categories intelligently detect themes
  - **Auto-detection for users**: Scans your Sage theme's `stubs/themes/` directory automatically
  - **Config-based for package**: Package templates (like Nynaeve) must be explicitly defined in config
  - Any new theme folder added to your Sage theme automatically appears as a category option
  - Package developers must register themes in config to prevent accidental exposure of incomplete templates
  - Theme stub files are checked in theme first, then package (allows overriding)
  - Theme names are automatically formatted for display (e.g., "Nynaeve Theme - Production templates from Nynaeve theme")
- **Template Configuration** - Added `category` field to all template definitions
  - Templates now organized by category: `basic`, `generic`, or theme name
  - Simplified template names as they're now grouped by category
  - Example: "InnerBlocks Container (Generic)" is now just "InnerBlocks Container" under Generic Templates
- **Interactive Flow Enhancement** - Basic block selection is now streamlined
  - Selecting "Basic Block" category immediately uses the basic template without additional prompts
  - Generic and theme categories show template options for user selection
- **Custom Template Support** - Users can now add custom block templates without modifying vendor package
  - Create templates in your Sage theme's `block-templates/` directory
  - Templates are automatically detected and available in the template selection menu
  - No configuration file needed - just drop your template files and they appear
  - Override package templates by creating a template with the same name in your theme
- **Template Metadata** - Optional `template-meta.json` for rich template descriptions
  - Define custom name, description, and category for your templates
  - Falls back to auto-generated values from folder name if metadata not provided
  - Schema: `{ "name": "...", "description": "...", "category": "..." }`
  - Metadata file is completely optional - templates work without it
- **Smart Template Discovery** - Automatic template detection from both package and theme
  - Scans theme's `block-templates/` directory for any folder containing `block.json`
  - Merges theme templates with package templates
  - Theme templates take precedence over package templates with same name
  - Templates organized by category in selection menu
  - Custom templates show "(Custom)" indicator in selection menu for clear differentiation
- **Developer Notes**:
  - **For theme developers**: Use `block-templates/` directory in your Sage theme root
  - **For package developers**: Continue using `stubs/` with config definitions
  - Theme templates can override package templates (checked first)
  - Template name = folder name (use kebab-case for consistency)
  - Folder names automatically converted to human-readable format (e.g., "my-hero" → "My Hero")
  - Non-interactive mode with `--template` flag continues to work as before for automation

### Migration Guide

**Command Name Change:**

The old command still works but shows a deprecation warning:
```shell
# Old command (deprecated but still works)
wp acorn sage-native-block:add-setup my-block --template=cta --force
# Shows: ⚠️ DEPRECATION WARNING - Please use sage-native-block:create instead

# New command (recommended)
wp acorn sage-native-block:create my-block --template=cta --force
```

**Custom Templates:**

Users can now create custom templates in their theme:
```bash
# Create template folder in your theme
mkdir -p block-templates/my-hero
# Add your template files (block.json, editor.jsx, save.jsx, etc.)

# Optional: Add metadata for better display
echo '{"name":"Hero Section","description":"Custom hero template","category":"custom"}' > block-templates/my-hero/template-meta.json
```

Templates automatically appear in the category selection menu on next run.

## [1.1.0] - 2025-10-14

### Added

- **Template System** - Multiple block templates for faster development
  - Generic templates (universal, no theme dependencies):
    - `basic` - Simple block with InnerBlocks support
    - `innerblocks` - Minimal heading and content template
    - `two-column` - Basic two-column layout structure
    - `statistics` - Simple statistics layout
    - `cta` - Basic call-to-action with button
  - Theme-specific templates:
    - Nynaeve theme templates (production-ready examples)
    - `nynaeve-innerblocks` - Pre-styled container
    - `nynaeve-two-column` - Card-style layout
    - `nynaeve-statistics` - Complete statistics display
    - `nynaeve-cta` - Styled call-to-action
- **Interactive Template Selection** - CLI prompts to choose templates during block creation
- **Template Documentation** - Comprehensive guide in `docs/TEMPLATE-STUBS.md`
- **Theme Templates Framework** - Structure for community-contributed theme templates
- Example block implementations showcasing best practices
- Template configuration in `config/sage-native-block.php`

### Changed

- Enhanced `SageNativeBlockCommand` to support template selection
- Improved README with template usage examples and comparison table
- Restructured stub organization: generic vs. theme-specific templates
- Updated configuration to include template definitions and presets

### Developer Notes

- Templates organized in `stubs/generic/` (universal) and `stubs/examples/` (theme-specific)
- Generic templates work with any theme out-of-the-box
- Theme templates require specific theme.json configurations
- This release enables 80% faster block development through pre-configured templates

## [1.0.2] - 2025-05-02

### Changed

- README improvements for command usage clarity
- Enhanced documentation for better developer experience

## [1.0.1] - 2025-05-02

### Changed

- Updated configuration documentation and notes
- General README improvements

## [1.0.0] - 2025-05-02

### Added

- Installation notes and documentation
- LICENSE.md file with MIT license

### Changed

- General clarifications and improvements to documentation

## [1.0.0-beta.1] - 2025-04-20

### Added

- Core package functionality
- `sage-native-block:add-setup` command to scaffold native Gutenberg blocks
- Automatic block registration in theme's `app/setup.php`
- Block file structure generation (JS, JSX, CSS)
- Editor and frontend style scaffolding
- Proper block naming with vendor prefixes
- Import management for `resources/js/editor.js`
- Basic block template
- Configuration publishing via Acorn
- Service provider for Acorn integration
- Laravel Pint for code formatting
- GitHub workflow for automated testing

### Documentation

- Comprehensive README with installation and usage instructions
- Configuration documentation
- Feature overview and examples

[Unreleased]: https://github.com/imagewize/wp-native-blocks/compare/v3.0.0...HEAD
[3.0.0]: https://github.com/imagewize/wp-native-blocks/releases/tag/v3.0.0
[2.0.1]: https://github.com/imagewize/sage-native-block/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/imagewize/sage-native-block/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/imagewize/sage-native-block/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/imagewize/sage-native-block/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.0-beta.1...v1.0.0
[1.0.0-beta.1]: https://github.com/imagewize/sage-native-block/releases/tag/v1.0.0-beta.1
