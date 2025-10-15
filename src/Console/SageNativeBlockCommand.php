<?php

namespace Imagewize\SageNativeBlockPackage\Console;

use Illuminate\Filesystem\Filesystem;
use Roots\Acorn\Console\Commands\Command;
use Roots\Acorn\Filesystem\Filesystem as RootsFilesystem;

/**
 * Interactive command to create native Gutenberg blocks for your Sage theme.
 *
 * Interactive mode (recommended):
 * $ wp acorn sage-native-block:create
 *   - Prompts for block name with vendor prefix
 *   - Prompts to select from available templates
 *   - Shows summary and asks for confirmation
 *
 * Non-interactive mode (for automation):
 * $ wp acorn sage-native-block:create my-block --template=statistics --force
 *
 * This will create block files in resources/js/blocks/<block-name>/
 * The block.json 'name' will always include a vendor prefix (e.g., 'vendor/cool-block' or 'imagewize/my-cool-block').
 * The block.json 'textdomain' will be set to the vendor ('vendor' or 'imagewize').
 * The default CSS class will be 'wp-block-vendor-cool-block' or 'wp-block-imagewize-my-cool-block'.
 */
class SageNativeBlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sage-native-block:create
                            {blockName? : The name of the block (e.g., "my-block" or "vendor/my-block")}
                            {--template= : Block template type (basic, innerblocks, two-column, statistics, cta)}
                            {--force : Skip all confirmation prompts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactively create a new native Gutenberg block for your Sage theme.';

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
     * Resolve the theme path using RootsFilesystem or fallback to WordPress methods
     *
     * @param RootsFilesystem $rootsFiles
     * @param string $relativePath
     * @return string
     */
    protected function resolvePath(RootsFilesystem $rootsFiles, string $relativePath = ''): string
    {
        // Try Acorn-specific method if available
        try {
            if (method_exists($rootsFiles, 'path')) {
                return $rootsFiles->path($relativePath);
            }
        } catch (\Exception $e) {
            // Fallback to WordPress methods
        }
        
        // First fallback: WordPress functions
        if (function_exists('get_template_directory')) {
            $themePath = get_template_directory();
            
            if (!empty($relativePath)) {
                return $themePath . '/' . $relativePath;
            }
            
            return $themePath;
        }
        
        // Last resort: Try to detect the theme path from the command location
        $commandPath = dirname(__DIR__, 5); // Adjust if needed
        $this->warn("Using detected theme path: {$commandPath}");
        
        if (!empty($relativePath)) {
            return $commandPath . '/' . $relativePath;
        }
        
        return $commandPath;
    }

    /**
     * Execute the console command.
     */
    public function handle(RootsFilesystem $rootsFiles): int
    {
        // Interactive mode: prompt for block name if not provided
        $blockNameInput = $this->argument('blockName');

        if (!$blockNameInput) {
            $this->newLine();
            $this->line('<fg=cyan>Welcome to Sage Native Block Creator!</>');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();

            $blockNameInput = $this->ask('What is the block name? (e.g., "my-block" or "vendor/my-block")');

            if (empty($blockNameInput)) {
                $this->error('Block name is required.');
                return static::FAILURE;
            }
        }

        // Ensure block name always has a vendor prefix
        if (!str_contains($blockNameInput, '/')) {
            $defaultVendor = 'vendor';

            // Only prompt for vendor if in interactive mode
            if (!$this->argument('blockName')) {
                $vendor = $this->ask("Enter vendor prefix (leave empty for '{$defaultVendor}')");
                $vendor = !empty($vendor) ? $vendor : $defaultVendor;
            } else {
                $vendor = $defaultVendor;
                $this->comment("No vendor prefix provided. Using default: '{$vendor}'");
            }

            $fullBlockName = $vendor . '/' . $blockNameInput;
        } else {
            $fullBlockName = $blockNameInput;
        }

        // Get template from option or prompt user with two-step selection
        if ($this->option('template')) {
            $template = $this->option('template');
        } else {
            // Step 1: Select category
            $category = $this->promptForTemplateCategory();

            // Step 2: Select template within category
            $template = $this->promptForTemplate($category);
        }

        // Validate template exists
        if (!$this->isValidTemplate($template)) {
            $this->error("Template '{$template}' not found in configuration.");
            return static::FAILURE;
        }

        // Get template display name
        $templateConfig = config('sage-native-block.templates')[$template] ?? [];
        $templateName = $templateConfig['name'] ?? $template;

        // Extract the base name for directory structure
        $directoryBlockName = $this->getDirectoryBlockName($fullBlockName);

        // Display header
        $this->newLine();
        $this->line("🔨 Creating block: <fg=cyan>{$fullBlockName}</>");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();
        $this->line("  Template:  <fg=yellow>{$templateName}</>");
        $this->line("  Location:  <fg=green>resources/js/blocks/{$directoryBlockName}</>");
        $this->newLine();

        $setupPath = $this->resolvePath($rootsFiles, 'app/setup.php');

        if (! $this->files->exists($setupPath)) {
            $this->error("Error: Theme setup file not found at app/setup.php");

            return static::FAILURE;
        }

        $currentContent = $this->files->get($setupPath);

        // Check if the code is already present
        $setupExists = str_contains($currentContent, 'register_block_type($block_json_path);');

        // Confirm action unless forced
        if (! $this->option('force') && ! $this->confirm('Continue?')) {
            $this->line('Operation cancelled.');

            return static::SUCCESS;
        }

        $this->newLine();
        $this->line('<fg=yellow>Setup:</>');

        // Create a backup of the setup file only if we are modifying it
        $backupPath = null;
        $codeToAdd = $this->getBlockRegistrationCode();

        try {
            // Append the code to the file only if it's not already there
            if (!$setupExists) {
                $backupPath = $setupPath.'.backup-'.date('Y-m-d-His');
                $this->files->copy($setupPath, $backupPath);

                if ($this->files->append($setupPath, $codeToAdd)) {
                    $this->line('  <fg=green>✓</> Block registration added');
                } else {
                    $this->error('  ✗ Failed to update app/setup.php');
                    if ($backupPath) {
                        $this->files->copy($backupPath, $setupPath);
                    }
                    return static::FAILURE;
                }
            } else {
                $this->line('  <fg=green>✓</> Block registration configured');
            }

            // Update JS file to include block assets
            $editorJsUpdated = $this->updateJsFile($rootsFiles);
            if ($editorJsUpdated === true) {
                $this->line('  <fg=green>✓</> Editor imports added');
            } elseif ($editorJsUpdated === false) {
                $this->line('  <fg=green>✓</> Editor imports configured');
            }

            $this->newLine();
            $this->line('<fg=yellow>Files:</>');

            // Copy block template files using the full name for replacements and directory name for path
            $filesCopied = $this->copyBlockStubs($rootsFiles, $fullBlockName, $directoryBlockName, $template);

            if (!$filesCopied) {
                 // If copying fails, potentially revert setup.php if it was modified in this run
                 if ($backupPath && $this->files->exists($backupPath)) {
                    $this->error('  ✗ Failed to copy files. Reverting changes...');
                    $this->files->copy($backupPath, $setupPath);
                }
                return static::FAILURE;
            }

            // Final success message
            $this->newLine();
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line("<fg=green>✓ Success!</> Block ready at <fg=green>resources/js/blocks/{$directoryBlockName}</>");
            $this->newLine();

            return static::SUCCESS;

        } catch (\Exception $e) {
            $this->error('An error occurred: '.$e->getMessage());

            if ($backupPath && $this->files->exists($backupPath)) {
                $this->warn('Restoring setup.php from backup...');
                $this->files->copy($backupPath, $setupPath);
                $this->info('Backup restored.');
            }

            return static::FAILURE;
        }
    }

    /**
     * Get the base block name for directory structure.
     * Example: 'vendor/my-block' -> 'my-block', 'my-block' -> 'my-block'
     */
    protected function getDirectoryBlockName(string $fullBlockName): string
    {
        if (str_contains($fullBlockName, '/')) {
            $parts = explode('/', $fullBlockName);
            return end($parts);
        }
        return $fullBlockName;
    }

    /**
     * Get the generic block registration code to be added.
     */
    protected function getBlockRegistrationCode(): string
    {
        return <<<PHP

/**
 * Register block types using block.json metadata from the theme's blocks directory.
 * This function will scan the 'resources/js/blocks' directory for block.json files.
 */
add_action('init', function () {
    \$blocks_dir = get_template_directory() . '/resources/js/blocks';
    if (!is_dir(\$blocks_dir)) {
        return;
    }

    \$block_folders = scandir(\$blocks_dir);

    foreach (\$block_folders as \$folder) {
        if (\$folder === '.' || \$folder === '..') {
            continue;
        }

        \$block_json_path = \$blocks_dir . '/' . \$folder . '/block.json';

        if (file_exists(\$block_json_path)) {
            register_block_type(\$block_json_path);
        }
    }
}, 10);

PHP;
    }

    /**
     * Copy block stub files to the theme's resources directory.
     * Uses directoryBlockName for the target path and fullBlockName for replacements.
     */
    protected function copyBlockStubs(RootsFilesystem $rootsFiles, string $fullBlockName, string $directoryBlockName, string $template): bool
    {
        try {
            // Get stub path from config
            $stubPath = $this->getStubPath($template);

            // Source stub directory
            $stubsDir = dirname(__DIR__, 2).'/stubs/'.$stubPath;

            // Target directory in the theme using the directoryBlockName
            $targetDir = $this->resolvePath($rootsFiles, "resources/js/blocks/{$directoryBlockName}");

            // Create target directory if it doesn't exist
            if (! $this->files->isDirectory($targetDir)) {
                $this->files->makeDirectory($targetDir, 0755, true);
            }

            // Files to copy
            $files = [
                'block.json',
                'index.js',
                'editor.jsx',
                'save.jsx',
                'editor.css',
                'style.css',
                'view.js',
            ];

            $copiedFiles = [];
            $failedFiles = [];

            // Copy each file
            foreach ($files as $file) {
                $source = "{$stubsDir}/{$file}";
                $target = "{$targetDir}/{$file}";

                if ($this->files->exists($source)) {
                    $content = $this->files->get($source);

                    // Use the full block name for replacements
                    if ($file === 'block.json') {
                        $content = $this->replaceBlockName($content, $fullBlockName);
                    }
                    // Replace CSS class references in CSS files
                    elseif ($file === 'style.css' || $file === 'editor.css') {
                        $content = $this->replaceCssClassName($content, $fullBlockName);
                    }
                    // Replace CSS class references in view.js
                    elseif ($file === 'view.js') {
                        $content = $this->replaceJsClassName($content, $fullBlockName);
                    }
                    // Replace class name placeholder in editor.jsx
                    elseif ($file === 'editor.jsx') {
                        $content = $this->replaceEditorClassName($content, $fullBlockName);
                    }

                    $this->files->put($target, $content);
                    $copiedFiles[] = $file;
                } else {
                    $failedFiles[] = $file;
                }
            }

            // Display grouped output
            if (count($copiedFiles) > 0) {
                $this->line('  <fg=green>✓</> block.json, index.js');
                $this->line('  <fg=green>✓</> editor.jsx, save.jsx');
                $this->line('  <fg=green>✓</> editor.css, style.css');
                $this->line('  <fg=green>✓</> view.js');
            }

            if (count($failedFiles) > 0) {
                foreach ($failedFiles as $file) {
                    $this->line("  <fg=red>✗</> {$file} (not found)");
                }
            }

            return true;
        } catch (\Exception $e) {
            $this->error('Failed to copy block stubs: '.$e->getMessage());

            return false;
        }
    }
    
    /**
     * Replace placeholders in block.json content with the provided full block name.
     */
    protected function replaceBlockName(string $content, string $fullBlockName): string
    {
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->warn('Could not parse block.json stub, using it as is.');
            return $content;
        }

        // Parse the full block name (guaranteed to have a vendor by handle())
        $vendor = 'vendor'; // Default if parsing fails unexpectedly
        $actualBlockName = $fullBlockName;
        if (str_contains($fullBlockName, '/')) {
            $parts = explode('/', $fullBlockName, 2);
            $vendor = $parts[0];
            $actualBlockName = $parts[1];
        } else {
            // This case should ideally not be reached due to logic in handle()
             $this->warn("Block name '{$fullBlockName}' unexpectedly lacks a vendor prefix during replacement. Defaulting vendor to 'vendor'.");
             $actualBlockName = $fullBlockName; // Use the full name as the actual name
        }

        // Update the name field
        $data['name'] = $fullBlockName;

        // Update the title field based on the actual block name part
        if (isset($data['title'])) {
            $formattedName = str_replace('-', ' ', $actualBlockName);
            $formattedName = ucwords($formattedName);
            $data['title'] = $formattedName;
        }

        // Update textdomain to match the determined vendor
        if (isset($data['textdomain'])) { // Check if textdomain key exists in stub
             $data['textdomain'] = $vendor;
        }

        // Update the className default
        if (isset($data['attributes']['className']['default'])) {
            $className = 'wp-block-' . str_replace('/', '-', $fullBlockName);
            $data['attributes']['className']['default'] = $className;
        }

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Replace example CSS class name with the generated class name based on the full block name.
     */
    protected function replaceCssClassName(string $content, string $fullBlockName): string
    {
        // Generate class name like wp-block-vendor-block-name or wp-block-block-name
        $className = 'wp-block-' . str_replace('/', '-', $fullBlockName);
        
        // Replace the placeholder class. No need to escape the replacement string.
        return preg_replace('/\.wp-block-vendor-example-block/', ".{$className}", $content);
    }
    
    /**
     * Replace example CSS class name with the generated class name in JS files.
     */
    protected function replaceJsClassName(string $content, string $fullBlockName): string
    {
        $className = 'wp-block-' . str_replace('/', '-', $fullBlockName);
        // Replace the placeholder class literal.
        return str_replace('.wp-block-vendor-example-block', ".{$className}", $content);
    }

    /**
     * Replace class name placeholder in editor.jsx files.
     */
    protected function replaceEditorClassName(string $content, string $fullBlockName): string
    {
        $className = 'wp-block-' . str_replace('/', '-', $fullBlockName);
        // Replace the {{BLOCK_CLASS_NAME}} placeholder
        return str_replace('{{BLOCK_CLASS_NAME}}', $className, $content);
    }

    /**
     * Update editor.js to import block index.js files for the editor.
     * Returns: true if added, false if already exists, null if created new file
     */
    protected function updateJsFile(RootsFilesystem $rootsFiles): ?bool
    {
        $jsPath = $this->resolvePath($rootsFiles, 'resources/js/editor.js');

        if (! $this->files->exists($jsPath)) {
            // Use NOWDOC for initial content to avoid issues with special characters
            $initialContent = <<<'JS'
import domReady from '@wordpress/dom-ready';

/**
 * Import editor blocks
 */
import.meta.glob('./blocks/**/index.js', { eager: true });

domReady(() => {
  // Initialize editor scripts here
});
JS; // Ensure this is at the start of the line with no preceding whitespace
            $this->files->put($jsPath, $initialContent);
            return null; // Created new file
        } else {
            $jsContent = $this->files->get($jsPath);

            $globPattern = './blocks/**/index.js';
            // Use regex for a more robust check of the import line existence
            if (! preg_match('/import\.meta\.glob\(\s*[\'"]' . preg_quote($globPattern, '/') . '[\'"]/', $jsContent)) {
                // Use NOWDOC for the code to add
                $jsToAdd = <<<'JS'

/**
 * Import editor blocks
 */
import.meta.glob('./blocks/**/index.js', { eager: true });

JS; // Ensure this is at the start of the line with no preceding whitespace
                // Prepend the import code to the existing content
                $this->files->put($jsPath, $jsToAdd . $jsContent);
                return true; // Added import
            } else {
                return false; // Already exists
            }
        }
    }

    /**
     * Get available template categories dynamically.
     * Returns categories from config templates plus any theme folders found in stubs/themes/
     */
    protected function getAvailableCategories(): array
    {
        $categories = [
            'basic' => 'Basic Block - Default simple block',
            'generic' => 'Generic Templates - Universal, theme-agnostic templates',
        ];

        // Scan stubs/themes/ directory for theme folders
        $themesDir = dirname(__DIR__, 2) . '/stubs/themes';

        if ($this->files->isDirectory($themesDir)) {
            $themeFolders = $this->files->directories($themesDir);

            foreach ($themeFolders as $themeFolder) {
                $themeName = basename($themeFolder);
                // Capitalize theme name for display
                $displayName = ucfirst($themeName);
                $categories[$themeName] = "{$displayName} Theme - Production templates from {$displayName} theme";
            }
        }

        return $categories;
    }

    /**
     * Prompt user to select a template category interactively.
     */
    protected function promptForTemplateCategory(): string
    {
        $categories = $this->getAvailableCategories();

        if (empty($categories)) {
            $this->warn('No template categories found. Using basic.');
            return 'basic';
        }

        $choices = array_values($categories);
        $keys = array_keys($categories);

        $selection = $this->choice('Which template category would you like to use?', $choices, 0);

        // Find the index of the selected choice
        $selectedIndex = array_search($selection, $choices);

        return $keys[$selectedIndex];
    }

    /**
     * Prompt user to select a template interactively.
     * Optionally filter by category.
     */
    protected function promptForTemplate(?string $category = null): string
    {
        $templates = config('sage-native-block.templates', []);

        if (empty($templates)) {
            $this->warn('No templates found in configuration. Using default.');
            return config('sage-native-block.default_template', 'basic');
        }

        // Filter templates by category if provided
        if ($category !== null) {
            $templates = array_filter($templates, function ($template) use ($category) {
                return isset($template['category']) && $template['category'] === $category;
            });
        }

        if (empty($templates)) {
            $this->warn("No templates found for category '{$category}'. Using default.");
            return config('sage-native-block.default_template', 'basic');
        }

        // For 'basic' category, just return the basic template directly
        if ($category === 'basic') {
            return 'basic';
        }

        $choices = [];
        $keys = [];
        foreach ($templates as $key => $template) {
            $choices[] = "{$template['name']} - {$template['description']}";
            $keys[] = $key;
        }

        $defaultIndex = array_search(config('sage-native-block.default_template', 'basic'), $keys);
        if ($defaultIndex === false) {
            $defaultIndex = 0;
        }

        $selection = $this->choice('Which template would you like to use?', $choices, $defaultIndex);

        // Find the index of the selected choice
        $selectedIndex = array_search($selection, $choices);

        return $keys[$selectedIndex];
    }

    /**
     * Validate if a template exists in the configuration.
     */
    protected function isValidTemplate(string $template): bool
    {
        $templates = config('sage-native-block.templates', []);
        return isset($templates[$template]);
    }

    /**
     * Get the stub path for a given template.
     */
    protected function getStubPath(string $template): string
    {
        $templates = config('sage-native-block.templates', []);

        if (!isset($templates[$template])) {
            return 'block'; // Fallback to default
        }

        return $templates[$template]['stub_path'] ?? 'block';
    }
}
