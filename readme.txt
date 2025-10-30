=== WP Native Blocks ===
Contributors: imagewize
Tags: gutenberg, blocks, development, cli, scaffolding, fse, block-theme
Requires at least: 6.0
Tested up to: 6.7
Requires PHP: 8.0
Stable tag: 3.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Scaffold native Gutenberg blocks for block themes via WP-CLI with per-block builds using @wordpress/scripts.

== Description ==

WP Native Blocks is a powerful WP-CLI tool that helps WordPress theme developers quickly scaffold native Gutenberg blocks with best practices for block themes (FSE).

**Features:**

* Per-block build system with @wordpress/scripts
* React-based blocks with JSX components
* Standard block structure (src/ → build/)
* Block templates (base, generic, Moiraine theme)
* Automatic block registration in functions.php
* Simple WP-CLI commands

**Designed for Block Themes:**

This plugin is specifically built for modern WordPress block themes (Full Site Editing). Each block gets its own package.json and builds independently using @wordpress/scripts.

**Usage:**

```
wp block create vendor/block-name
wp block create imagewize/hero --template=moiraine-hero
```

Requires WP-CLI to be installed on your development environment.

== Installation ==

1. Install and activate the plugin
2. Ensure WP-CLI is installed on your system
3. Run `wp block create` to scaffold your first block
4. Follow the prompts or use command options

**Via WP-CLI:**

```
wp plugin install wp-native-blocks --activate
```

**Via Composer:**

```
composer require imagewize/wp-native-blocks --dev
```

== Frequently Asked Questions ==

= Do I need WP-CLI? =

Yes, this is a WP-CLI plugin that requires WP-CLI to be installed on your development environment.

= Does this work with block themes? =

Yes! This plugin is specifically designed for block themes (FSE). Each block gets its own per-block build system using @wordpress/scripts.

= Does this work with classic themes? =

This version is optimized for block themes. For classic or Sage themes, you may need a different approach.

= Can I use custom block templates? =

Yes! Create templates in your theme's `block-templates/` directory and they'll be automatically discovered.

= What is the block structure? =

Each block has:
- `package.json` - Dependencies and build scripts
- `src/` - Source files (block.json, edit.jsx, save.jsx, styles)
- `build/` - Compiled output (auto-generated)

= How do I build blocks? =

After creating a block:
```
cd blocks/your-block
npm install
npm run start  # Development
npm run build  # Production
```

== Screenshots ==

1. WP-CLI command creating a new block
2. Generated block structure
3. Block appearing in WordPress editor
4. Per-block package.json setup

== Changelog ==

= 3.0.0 =
* Complete rewrite as block-theme-focused plugin
* Removed Acorn/Sage dependencies
* Added per-block build support with @wordpress/scripts
* Added Moiraine theme templates
* Simplified to WP-CLI only (no Laravel/Acorn required)
* Standard block location: `blocks/` directory
* React-based blocks with .jsx components
* Clean, predictable structure

= 2.x =
* Legacy Sage/Acorn-specific version

== Upgrade Notice ==

= 3.0.0 =
Major version focused on block themes. This is a breaking change from 2.x which was Sage-specific.

== Additional Info ==

**Support:** https://github.com/imagewize/wp-native-blocks/issues
**Documentation:** https://github.com/imagewize/wp-native-blocks

Built by Imagewize - https://imagewize.com
