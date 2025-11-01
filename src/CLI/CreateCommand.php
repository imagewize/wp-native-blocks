<?php
/**
 * WP-CLI command for creating native blocks.
 *
 * @package WP_Native_Blocks
 */

namespace Imagewize\WpNativeBlocks\CLI;

use WP_CLI;
use WP_CLI_Command;

/**
 * Command class for creating native Gutenberg blocks.
 */
class CreateCommand extends WP_CLI_Command {

	/**
	 * Create a new native block.
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
	 *     wp block create imagewize/custom --blocks-dir=custom-blocks
	 *
	 * @param array $args Positional arguments.
	 * @param array $assoc_args Associative arguments.
	 */
	public function __invoke( $args, $assoc_args ) {
		$blockName = $args[0] ?? null;
		$template  = $assoc_args['template'] ?? 'base';
		$blocksDir = $assoc_args['blocks-dir'] ?? 'blocks';

		// Validate block name
		if ( ! $blockName || ! str_contains( $blockName, '/' ) ) {
			WP_CLI::error( 'Block name must include vendor (e.g., vendor/block-name)' );
			return;
		}

		[$vendor, $name] = explode( '/', $blockName, 2 );

		// Get theme directory (uses get_stylesheet_directory for child theme support)
		$themeDir  = get_stylesheet_directory();
		$blockPath = $themeDir . '/' . $blocksDir . '/' . $name;

		// Check if block already exists
		if ( is_dir( $blockPath ) ) {
			WP_CLI::error( "Block already exists at: {$blockPath}" );
			return;
		}

		// Create block from stub
		$this->createBlockFromStub( $blockPath, $blockName, $template );

		// Update functions.php if needed
		$this->ensureBlockRegistration( $themeDir, $blocksDir );

		WP_CLI::success( "Block created at: {$blockPath}" );
		WP_CLI::line( '' );
		WP_CLI::line( 'Next steps:' );
		WP_CLI::line( "  1. cd {$blocksDir}/{$name}" );
		WP_CLI::line( '  2. npm install' );
		WP_CLI::line( '  3. npm run start' );
	}

	/**
	 * Create a block from a stub template.
	 *
	 * @param string $blockPath Path where the block will be created.
	 * @param string $blockName Full block name (vendor/name).
	 * @param string $template Template name to use.
	 */
	private function createBlockFromStub( string $blockPath, string $blockName, string $template ): void {
		$stubsDir = WP_NATIVE_BLOCKS_PATH . 'stubs';

		// Determine stub path
		if ( 'base' === $template ) {
			$stubPath = $stubsDir . '/base';
		} elseif ( str_starts_with( $template, 'moiraine-' ) ) {
			$stubPath = $stubsDir . '/moiraine/' . str_replace( 'moiraine-', '', $template );
		} else {
			$stubPath = $stubsDir . '/generic/' . $template;
		}

		if ( ! is_dir( $stubPath ) ) {
			WP_CLI::error( "Template not found: {$template}" );
			return;
		}

		// Copy stub files
		$this->recursiveCopy( $stubPath, $blockPath, $blockName );

		WP_CLI::line( '✓ Created block structure' );
	}

	/**
	 * Recursively copy files from source to destination.
	 *
	 * @param string $src Source directory.
	 * @param string $dst Destination directory.
	 * @param string $blockName Block name for placeholder replacement.
	 */
	private function recursiveCopy( string $src, string $dst, string $blockName ): void {
		$dir = opendir( $src );

		if ( ! file_exists( $dst ) ) {
			mkdir( $dst, 0755, true );
		}

		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( '.' === $file || '..' === $file ) {
				continue;
			}

			$srcPath = $src . '/' . $file;
			$dstPath = $dst . '/' . str_replace( '.stub', '', $file );

			if ( is_dir( $srcPath ) ) {
				$this->recursiveCopy( $srcPath, $dstPath, $blockName );
			} else {
				$content = file_get_contents( $srcPath );

				// Replace placeholders
				$content = str_replace( '{{BLOCK_NAME}}', $blockName, $content );
				$content = str_replace( '{{BLOCK_SLUG}}', str_replace( '/', '-', $blockName ), $content );

				file_put_contents( $dstPath, $content );
			}
		}

		closedir( $dir );
	}

	/**
	 * Ensure block registration code exists in theme's functions.php.
	 *
	 * @param string $themeDir Theme directory path.
	 * @param string $blocksDir Blocks directory name.
	 */
	private function ensureBlockRegistration( string $themeDir, string $blocksDir ): void {
		$functionsFile = $themeDir . '/functions.php';

		if ( ! file_exists( $functionsFile ) ) {
			WP_CLI::warning( 'functions.php not found. You\'ll need to register blocks manually.' );
			return;
		}

		$content = file_get_contents( $functionsFile );

		// Check if registration already exists
		if ( str_contains( $content, 'register_block_type($block_json_path)' ) ) {
			WP_CLI::line( '✓ Block registration already exists in functions.php' );
			return;
		}

		// Add registration code
		$registrationCode = $this->getRegistrationCode( $blocksDir );

		// Backup
		copy( $functionsFile, $functionsFile . '.backup-' . date( 'Y-m-d-His' ) );

		// Append registration
		file_put_contents( $functionsFile, $content . "\n" . $registrationCode );

		WP_CLI::line( '✓ Added block registration to functions.php' );
	}

	/**
	 * Get the block registration code to add to functions.php.
	 *
	 * @param string $blocksDir Blocks directory name (unused, kept for compatibility).
	 * @return string Block registration code.
	 */
	private function getRegistrationCode( string $blocksDir ): string {
		return <<<'PHP'

/**
 * Register native blocks
 * Auto-generated by WP Native Blocks
 *
 * Registers blocks from both parent and child themes:
 * - Parent theme blocks are registered first (available to all child themes)
 * - Child theme blocks are registered second (can override parent blocks)
 */
add_action(
	'init',
	function () {
		$directories = array();

		// Add parent theme blocks directory (if exists and different from child).
		if ( get_template_directory() !== get_stylesheet_directory() ) {
			$directories[] = get_template_directory() . '/blocks';
		}

		// Add child/active theme blocks directory.
		$directories[] = get_stylesheet_directory() . '/blocks';

		foreach ( $directories as $blocks_dir ) {
			if ( ! is_dir( $blocks_dir ) ) {
				continue;
			}

			$block_folders = scandir( $blocks_dir );

			foreach ( $block_folders as $folder ) {
				if ( $folder === '.' || $folder === '..' ) {
					continue;
				}

				$block_json_path = $blocks_dir . '/' . $folder . '/build/block.json';

				if ( file_exists( $block_json_path ) ) {
					register_block_type( $block_json_path );
				}
			}
		}
	},
	10
);

PHP;
	}
}
