<?php

namespace Imagewize\SageNativeBlockPackage\Console;

use Roots\Acorn\Filesystem\Filesystem as RootsFilesystem;

/**
 * Deprecated command - alias for backward compatibility.
 * Use sage-native-block:create instead.
 */
class SageNativeBlockAddSetupCommand extends SageNativeBlockCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sage-native-block:add-setup
                            {blockName? : The name of the block (e.g., "my-block" or "vendor/my-block")}
                            {--template= : Block template type (basic, innerblocks, two-column, statistics, cta)}
                            {--force : Skip all confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[DEPRECATED] Use sage-native-block:create instead';

    /**
     * Execute the console command.
     */
    public function handle(RootsFilesystem $rootsFiles): int
    {
        // Show deprecation warning
        $this->newLine();
        $this->warn('⚠️  DEPRECATION WARNING');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('The command <fg=red>sage-native-block:add-setup</> is deprecated.');
        $this->line('Please use <fg=green>sage-native-block:create</> instead.');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Call parent handle method
        return parent::handle($rootsFiles);
    }
}
