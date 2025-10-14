# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/imagewize/sage-native-block/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.2...v1.1.0
[1.0.2]: https://github.com/imagewize/sage-native-block/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/imagewize/sage-native-block/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/imagewize/sage-native-block/compare/v1.0.0-beta.1...v1.0.0
[1.0.0-beta.1]: https://github.com/imagewize/sage-native-block/releases/tag/v1.0.0-beta.1
