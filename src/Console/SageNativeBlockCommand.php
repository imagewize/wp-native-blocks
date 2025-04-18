<?php

namespace Imagewize\SageNativeBlockPackage\Console;

use Illuminate\Filesystem\Filesystem;
use Roots\Acorn\Console\Commands\Command;
use Roots\Acorn\Filesystem\Filesystem as RootsFilesystem;

/**
 * Command to add native block registration code to the Sage theme setup file.
 *
 * Run this command from your WordPress site root or theme directory:
 * $ wp acorn sage-native-block:add-setup
 *
 * To skip confirmation prompt:
 * $ wp acorn sage-native-block:add-setup --force
 */
class SageNativeBlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sage-native-block:add-setup 
                            {--force : Force the operation to run without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add native block registration code to app/setup.php.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     */
    public function handle(RootsFilesystem $rootsFiles): int
    {
        $setupPath = $rootsFiles->path('app/setup.php');

        if (! $this->files->exists($setupPath)) {
            $this->error("Error: Theme setup file not found at {$setupPath}.");

            return static::FAILURE;
        }

        $currentContent = $this->files->get($setupPath);

        // Check if the code is already present
        if (str_contains($currentContent, 'register_block_type($block_json_path);')) {
            $this->info("Block registration code already exists in {$setupPath}.");

            return static::SUCCESS;
        }

        // Confirm action unless forced
        if (! $this->option('force') && ! $this->confirm("This will modify {$setupPath} and copy block templates. Do you wish to continue?")) {
            $this->line('Operation cancelled.');

            return static::SUCCESS;
        }

        // Create a backup of the file
        $backupPath = $setupPath.'.backup-'.date('Y-m-d-His');
        $this->files->copy($setupPath, $backupPath);
        $this->line("Created backup at {$backupPath}");

        $codeToAdd = $this->getBlockRegistrationCode();

        try {
            // Append the code to the file
            if ($this->files->append($setupPath, $codeToAdd)) {
                $this->info("Successfully added block registration code to {$setupPath}.");

                // Copy block template files
                if ($this->copyBlockStubs($rootsFiles)) {
                    $this->info('Successfully copied block template files to theme resources directory.');
                }

                $this->comment("Remember to replace 'example-block' with your actual block name.");

                return static::SUCCESS;
            }

            $this->error("Failed to write to {$setupPath}. Reverting to backup...");
            $this->files->copy($backupPath, $setupPath);

            return static::FAILURE;
        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());

            if ($this->files->exists($backupPath)) {
                $this->warn('Restoring from backup...');
                $this->files->copy($backupPath, $setupPath);
                $this->info('Backup restored.');
            }

            return static::FAILURE;
        }
    }

    /**
     * Get the block registration code to be added.
     */
    protected function getBlockRegistrationCode(): string
    {
        return <<<'PHP'

/**
 * Register block type using block.json metadata.
 */
add_action('init', function () {
    $block_json_path = get_template_directory().'/resources/js/blocks/example-block/block.json';

    if (file_exists($block_json_path)) {
        register_block_type($block_json_path);
    }
});

PHP;
    }

    /**
     * Copy block stub files to the theme's resources directory.
     */
    protected function copyBlockStubs(RootsFilesystem $rootsFiles): bool
    {
        try {
            // Source stub directory
            $stubsDir = dirname(__DIR__, 2).'/stubs/block';

            // Target directory in the theme - resolves to theme_directory/resources/js/blocks/example-block
            $targetDir = $rootsFiles->path('resources/js/blocks/example-block');

            // Verify the target path is within the theme
            $this->line("Target directory will be: {$targetDir}");

            // Create target directory if it doesn't exist
            if (! $this->files->isDirectory($targetDir)) {
                $this->files->makeDirectory($targetDir, 0755, true);
                $this->line("Created directory: {$targetDir}");
            }

            // Files to copy
            $files = [
                'block.json',
                'index.js',
                'editor.css',
                'style.css',
                'view.js',
            ];

            // Copy each file
            foreach ($files as $file) {
                $source = "{$stubsDir}/{$file}";
                $target = "{$targetDir}/{$file}";

                if ($this->files->exists($source)) {
                    $this->files->copy($source, $target);
                    $this->line("Copied: {$file}");
                } else {
                    $this->warn("Source file not found: {$source}");
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->error('Failed to copy block stubs: '.$e->getMessage());

            return false;
        }
    }
}
