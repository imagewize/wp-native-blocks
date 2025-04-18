<?php

namespace Imagewize\SageNativeBlockPackage\Console;

use Illuminate\Filesystem\Filesystem;
use Roots\Acorn\Console\Commands\Command;
use Roots\Acorn\Filesystem\Filesystem as RootsFilesystem;

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
     * @param  \Illuminate\Filesystem\Filesystem  $files
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
        if (!$this->option('force') && !$this->confirm("This will modify {$setupPath}. Do you wish to continue?")) {
            $this->line('Operation cancelled.');
            return static::SUCCESS;
        }

        // Create a backup of the file
        $backupPath = $setupPath . '.backup-' . date('Y-m-d-His');
        $this->files->copy($setupPath, $backupPath);
        $this->line("Created backup at {$backupPath}");

        $codeToAdd = $this->getBlockRegistrationCode();

        try {
            // Append the code to the file
            if ($this->files->append($setupPath, $codeToAdd)) {
                $this->info("Successfully added block registration code to {$setupPath}.");
                $this->comment("Remember to replace 'your-block' with your actual block name.");
                return static::SUCCESS;
            }

            $this->error("Failed to write to {$setupPath}. Reverting to backup...");
            $this->files->copy($backupPath, $setupPath);
            return static::FAILURE;
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            
            if ($this->files->exists($backupPath)) {
                $this->warn("Restoring from backup...");
                $this->files->copy($backupPath, $setupPath);
                $this->info("Backup restored.");
            }

            return static::FAILURE;
        }
    }

    /**
     * Get the block registration code to be added.
     *
     * @return string
     */
    protected function getBlockRegistrationCode(): string
    {
        return <<<'PHP'

/**
 * Register block type using block.json metadata.
 */
add_action('init', function () {
    $block_json_path = get_template_directory().'/resources/js/blocks/your-block/block.json';

    if (file_exists($block_json_path)) {
        register_block_type($block_json_path);
    }
});

PHP;
    }
}