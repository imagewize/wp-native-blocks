# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
- **Dynamic Theme Detection** - Template categories now automatically detect themes
  - System scans `stubs/themes/` directory for available theme folders
  - Any new theme added to `stubs/themes/` automatically appears as a category option
  - No code changes needed to add new theme template categories
  - Theme names are automatically formatted for display (e.g., "Nynaeve Theme - Production templates from Nynaeve theme")
- **Template Configuration** - Added `category` field to all template definitions
  - Templates now organized by category: `basic`, `generic`, or theme name
  - Simplified template names as they're now grouped by category
  - Example: "InnerBlocks Container (Generic)" is now just "InnerBlocks Container" under Generic Templates
- **Interactive Flow Enhancement** - Basic block selection is now streamlined
  - Selecting "Basic Block" category immediately uses the basic template without additional prompts
  - Generic and theme categories show template options for user selection
- **Developer Notes** - To add a new theme's templates: Create folder `stubs/themes/your-theme-name/` and add templates to config with `'category' => 'your-theme-name'`
  - The system will automatically detect and display the new theme category
  - Non-interactive mode with `--template` flag continues to work as before for automation

### Migration Guide

The old command still works but shows a deprecation warning:
```shell
# Old command (deprecated but still works)
wp acorn sage-native-block:add-setup my-block --template=cta --force
# Shows: ⚠️ DEPRECATION WARNING - Please use sage-native-block:create instead

# New command (recommended)
wp acorn sage-native-block:create my-block --template=cta --force
```

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

[Unreleased]: https://github.com/imagewize/sage-native-block/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/imagewize/sage-native-block/compare/v1.1.0...v2.0.0
[1.1.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/imagewize/sage-native-block/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/imagewize/sage-native-block/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.0-beta.1...v1.0.0
[1.0.0-beta.1]: https://github.com/imagewize/sage-native-block/releases/tag/v1.0.0-beta.1
