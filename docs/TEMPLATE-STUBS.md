# Template Stubs Enhancement Plan

**Status:** Planning Phase
**Created:** 2025-10-14
**Goal:** Extend sage-native-block package to support multiple block templates for faster development

---

## Problem Statement

Currently, the `sage-native-block` package generates a basic block stub that requires significant manual configuration for complex blocks. Complex blocks like `multi-column-content` require ~400 lines of template code, taking 2-3 hours to build from scratch.

### Current Development Time
- **Basic block:** 30 minutes
- **Two-column layout:** 1 hour
- **Complex multi-section block:** 2-3 hours

### Target Development Time (After Enhancement)
- **Any block type:** 15-30 minutes (80% reduction)

---

## Solution Overview

Add multiple block template types with interactive selection, allowing developers to start with pre-configured templates that match common patterns.

---

## Phase 1: Block Templates System (HIGH PRIORITY)

**Timeline:** Week 1-2
**Effort:** Medium
**Impact:** Massive time savings (2-3 hours → 30 minutes per block)

### Features

#### 1. Template Configuration System

Add template definitions to config file:

```php
// config/sage-native-block.php
return [
    'templates' => [
        'basic' => [
            'name' => 'Basic Block',
            'description' => 'Simple block with no InnerBlocks',
            'stub_path' => 'block',  // Current default
        ],
        'innerblocks' => [
            'name' => 'InnerBlocks Container',
            'description' => 'Container block that wraps other blocks',
            'stub_path' => 'innerblocks',
        ],
        'two-column' => [
            'name' => 'Two Column Layout',
            'description' => 'Two-column layout with headings and content',
            'stub_path' => 'two-column',
        ],
        'statistics' => [
            'name' => 'Statistics Section',
            'description' => 'Multi-column statistics with icons and CTAs',
            'stub_path' => 'statistics',
        ],
        'cta' => [
            'name' => 'Call-to-Action Section',
            'description' => 'CTA section with buttons and descriptions',
            'stub_path' => 'cta',
        ],
    ],

    'default_template' => 'basic',

    // Theme-specific typography presets (customizable per theme)
    'typography_presets' => [
        'main_heading' => [
            'fontFamily' => 'montserrat',
            'fontSize' => '3xl',
            'fontWeight' => '700',
            'textColor' => 'main',
        ],
        'sub_heading' => [
            'fontFamily' => 'montserrat',
            'fontSize' => '2xl',
            'fontWeight' => '600',
            'textColor' => 'main',
        ],
        'body_text' => [
            'fontFamily' => 'open-sans',
            'fontSize' => 'base',
            'fontWeight' => '400',
            'textColor' => 'secondary',
        ],
    ],

    'spacing_presets' => [
        'section_bottom' => '4rem',
        'paragraph_bottom' => '2rem',
        'column_gap_large' => '3.75rem',
        'column_gap_medium' => '1.875rem',
    ],
];
```

#### 2. Template-Specific Stub Directories

Create new stub directories for each template:

```
stubs/
├── block/              # Current basic template (default)
│   ├── block.json
│   ├── editor.css
│   ├── editor.jsx
│   ├── index.js
│   ├── save.jsx
│   ├── style.css
│   └── view.js
├── innerblocks/        # InnerBlocks container template
│   └── [same files with InnerBlocks setup]
├── two-column/         # Two-column layout template
│   └── [same files with 2-column pre-configured]
├── statistics/         # Statistics section template
│   └── [same files with multi-column stats layout]
└── cta/                # CTA section template
    └── [same files with CTA layout]
```

#### 3. Command Enhancement

Update command signature to support template selection:

```php
protected $signature = 'sage-native-block:add-setup
                        {blockName? : The name of the block (e.g., "my-block" or "vendor/my-block")}
                        {--template= : Block template type (basic, innerblocks, two-column, statistics, cta)}
                        {--force : Force the operation to run without confirmation}';
```

**Usage Examples:**

```bash
# Interactive mode (prompts for template)
wp acorn sage-native-block:add-setup imagewize/my-block

# Direct template selection
wp acorn sage-native-block:add-setup imagewize/my-stats --template=statistics

# Force with template
wp acorn sage-native-block:add-setup imagewize/my-cta --template=cta --force

# Default (basic) template
wp acorn sage-native-block:add-setup imagewize/simple-block
```

**Interactive Prompt:**

```
$ wp acorn sage-native-block:add-setup imagewize/my-block

Which template would you like to use?
  [1] Basic Block - Simple block with no InnerBlocks
  [2] InnerBlocks Container - Container block that wraps other blocks ⭐
  [3] Two Column Layout - Two-column layout with headings and content
  [4] Statistics Section - Multi-column statistics with icons and CTAs
  [5] Call-to-Action Section - CTA section with buttons and descriptions

Select template [1]:
```

### Implementation Tasks

#### Task 1: Create InnerBlocks Template
- [ ] Create `stubs/innerblocks/` directory
- [ ] Copy current stubs as base
- [ ] Update `editor.jsx` with basic InnerBlocks template
- [ ] Update `block.json` with proper supports (align, spacing, etc.)
- [ ] Update `style.css` with container styles
- [ ] Test generation and functionality

**Example `editor.jsx` output:**

```javascript
import { useBlockProps, InnerBlocks } from '@wordpress/block-editor';

const TEMPLATE = [
  ['core/heading', {
    level: 2,
    content: 'Section Heading',
    fontFamily: 'montserrat',
    fontSize: '3xl',
    textAlign: 'center',
    textColor: 'main',
    style: {
      typography: { fontWeight: '700', lineHeight: '1.2' },
      spacing: { margin: { bottom: '2rem' } }
    }
  }],
  ['core/paragraph', {
    content: 'Section description goes here.',
    fontFamily: 'open-sans',
    fontSize: 'base',
    textAlign: 'center',
    textColor: 'secondary',
    style: {
      typography: { lineHeight: '1.7' }
    }
  }]
];

export default function Edit() {
  const blockProps = useBlockProps({
    className: '{{BLOCK_CLASS_NAME}}'
  });

  return (
    <div {...blockProps}>
      <InnerBlocks
        template={TEMPLATE}
        templateLock={false}
      />
    </div>
  );
}
```

#### Task 2: Create Two-Column Template
- [ ] Create `stubs/two-column/` directory
- [ ] Based on existing `two-column-card` block pattern
- [ ] Pre-configure 2-column layout with proper spacing
- [ ] Add standard typography presets
- [ ] Include card/group wrapper pattern
- [ ] Test generation and functionality

**Template Structure:**
```
Main Heading (H2/H3)
├── Columns (2)
│   ├── Column 1
│   │   ├── Group (card wrapper)
│   │   │   ├── Heading
│   │   │   └── Paragraph
│   └── Column 2
│       ├── Group (card wrapper)
│       │   ├── Heading
│       │   └── Paragraph
```

#### Task 3: Create Statistics Template
- [ ] Create `stubs/statistics/` directory
- [ ] Based on existing `multi-column-content` block pattern
- [ ] Pre-configure multi-section layout:
  - Main heading
  - Statistics columns (2-3 columns)
  - Center heading
  - Benefits section
- [ ] Include icon placeholder pattern (CSS ::before)
- [ ] Test generation and functionality

**Template Structure:**
```
Main Heading (H2)
├── Columns (2) - Statistics
│   ├── Column 1
│   │   ├── Paragraph (tagline)
│   │   ├── Heading (stat)
│   │   └── Paragraph (subtext)
│   └── Column 2
│       ├── Paragraph (tagline)
│       ├── Heading (stat)
│       └── Paragraph (subtext)
├── Heading (H2) - Center heading
├── Paragraph - Subheading
└── Columns (3) - Benefits
    └── [Benefit items with icon support]
```

#### Task 4: Create CTA Template
- [ ] Create `stubs/cta/` directory
- [ ] Pre-configure CTA layout:
  - Heading
  - Description
  - Button(s)
- [ ] Support single or multi-column CTAs
- [ ] Include button styling presets
- [ ] Test generation and functionality

#### Task 5: Update Command Logic
- [ ] Add `--template` option parsing
- [ ] Add interactive template selection
- [ ] Load template from config
- [ ] Copy correct stub directory based on template
- [ ] Validate template exists
- [ ] Add helpful error messages
- [ ] Update help text

**Pseudo-code:**

```php
public function handle(RootsFilesystem $rootsFiles): int
{
    // Get template from option or prompt
    $template = $this->option('template') ?? $this->promptForTemplate();

    // Validate template exists
    if (!$this->isValidTemplate($template)) {
        $this->error("Template '{$template}' not found.");
        return static::FAILURE;
    }

    // Get stub path from config
    $stubPath = $this->getStubPath($template);

    // Copy stubs from template-specific directory
    $this->copyBlockStubs($rootsFiles, $fullBlockName, $directoryBlockName, $stubPath);

    // ... rest of logic
}

protected function promptForTemplate(): string
{
    $templates = config('sage-native-block.templates', []);

    $choices = array_map(function($key, $template) {
        return "{$template['name']} - {$template['description']}";
    }, array_keys($templates), $templates);

    $selection = $this->choice('Which template would you like to use?', $choices, 0);

    return array_keys($templates)[$selection];
}
```

#### Task 6: Update Documentation
- [ ] Update main README.md with template examples
- [ ] Document each template's structure
- [ ] Add usage examples
- [ ] Add screenshots/examples
- [ ] Update CHANGELOG.md

### Testing Checklist

**Template Generation:**
- [ ] Basic template generates correctly (backwards compatible)
- [ ] InnerBlocks template generates with proper structure
- [ ] Two-column template generates with proper layout
- [ ] Statistics template generates with complete structure
- [ ] CTA template generates with button configuration

**Command Options:**
- [ ] `--template` flag works correctly
- [ ] Interactive mode shows all templates
- [ ] Invalid template shows helpful error
- [ ] Default template (basic) still works without flag

**Block Functionality:**
- [ ] Generated blocks register in WordPress
- [ ] InnerBlocks work correctly in editor
- [ ] Templates are editable (templateLock: false)
- [ ] Styles apply correctly in editor and frontend
- [ ] Block.json supports are correctly configured

**Backwards Compatibility:**
- [ ] Existing command usage still works
- [ ] No breaking changes to current API
- [ ] Default behavior unchanged (basic template)

### Benefits

✅ **80% faster development** - Start with pre-configured templates
✅ **Consistent patterns** - All blocks follow established structure
✅ **Theme integration** - Templates use theme.json values
✅ **Reduced errors** - Proper InnerBlocks setup from start
✅ **Learning tool** - Developers see best practices in generated code
✅ **Backwards compatible** - Existing usage still works

---

## Phase 2: Shared Utilities Generator (MEDIUM PRIORITY)

**Timeline:** Week 3
**Effort:** Low
**Impact:** Long-term maintainability and code reuse

### Overview

Generate shared utilities directory that provides reusable constants and helper functions for all blocks in a theme.

### Features

#### 1. Shared Utilities Stubs

Create shared utilities stub directory:

```
stubs/
├── shared/
│   ├── constants.js        # Typography, spacing, color presets
│   ├── template-builder.js # Helper functions for building templates
│   └── utils.js            # Common utility functions
```

**Example `constants.js`:**

```javascript
/**
 * Common typography styles from theme.json
 */
export const TYPOGRAPHY_PRESETS = {
  mainHeading: {
    fontFamily: 'montserrat',
    fontSize: '3xl',
    style: {
      typography: { fontWeight: '700', lineHeight: '1.2' }
    },
    textColor: 'main'
  },
  subHeading: {
    fontFamily: 'montserrat',
    fontSize: '2xl',
    style: {
      typography: { fontWeight: '600', lineHeight: '1.3' }
    },
    textColor: 'main'
  },
  bodyText: {
    fontFamily: 'open-sans',
    fontSize: 'base',
    style: {
      typography: { fontWeight: '400', lineHeight: '1.7' }
    },
    textColor: 'secondary'
  },
};

/**
 * Common spacing patterns
 */
export const SPACING_PRESETS = {
  sectionBottom: { margin: { bottom: '4rem' } },
  paragraphBottom: { margin: { bottom: '2rem' } },
  columnGapLarge: '3.75rem',
  columnGapMedium: '1.875rem',
};

/**
 * Button style preset
 */
export const BUTTON_PRESET = {
  style: {
    border: { radius: '0.375rem' },
    spacing: { padding: { top: '1rem', right: '2rem', bottom: '1rem', left: '2rem' } }
  },
  backgroundColor: 'main',
  textColor: 'base'
};
```

**Example `template-builder.js`:**

```javascript
import { TYPOGRAPHY_PRESETS, SPACING_PRESETS, BUTTON_PRESET } from './constants';

/**
 * Build a heading block with preset typography
 */
export const heading = (level, content, preset = 'main', options = {}) => {
  const presets = {
    main: TYPOGRAPHY_PRESETS.mainHeading,
    sub: TYPOGRAPHY_PRESETS.subHeading,
  };

  return ['core/heading', {
    level,
    content,
    ...presets[preset],
    textAlign: options.align || 'center',
    className: options.className,
    style: {
      ...presets[preset].style,
      ...options.style
    },
    ...options
  }];
};

/**
 * Build a paragraph block with preset typography
 */
export const paragraph = (content, options = {}) => {
  return ['core/paragraph', {
    content,
    ...TYPOGRAPHY_PRESETS.bodyText,
    align: options.align || 'center',
    className: options.className,
    style: {
      ...TYPOGRAPHY_PRESETS.bodyText.style,
      ...options.style
    }
  }];
};

/**
 * Build a two-column layout
 */
export const twoColumns = (className, leftContent, rightContent, gap = '3.75rem') => {
  return ['core/columns', {
    className,
    style: { spacing: { blockGap: gap } }
  }, [
    ['core/column', {}, leftContent],
    ['core/column', {}, rightContent]
  ]];
};

/**
 * Build a button
 */
export const button = (text, options = {}) => {
  return ['core/button', {
    text,
    ...BUTTON_PRESET,
    ...options
  }];
};
```

#### 2. Setup Command

Add new command to generate shared utilities:

```bash
# Generate shared utilities (one-time per theme)
wp acorn sage-native-block:setup-utilities

# Or auto-generate on first block creation
wp acorn sage-native-block:add-setup imagewize/my-block --with-utilities
```

**Command Implementation:**

```php
protected $signature = 'sage-native-block:setup-utilities
                        {--force : Force overwrite existing utilities}';

protected $description = 'Generate shared utilities for block development';

public function handle(RootsFilesystem $rootsFiles): int
{
    $utilitiesPath = $this->resolvePath($rootsFiles, 'resources/js/blocks/shared');

    // Check if utilities already exist
    if ($this->files->exists($utilitiesPath) && !$this->option('force')) {
        $this->warn('Shared utilities already exist.');

        if (!$this->confirm('Overwrite existing utilities?')) {
            return static::SUCCESS;
        }
    }

    // Create directory
    $this->files->makeDirectory($utilitiesPath, 0755, true, true);

    // Copy utility stubs
    $this->copyUtilityStubs($rootsFiles, $utilitiesPath);

    $this->info('Successfully created shared utilities!');

    return static::SUCCESS;
}
```

#### 3. Auto-detection in Block Generation

When generating blocks, detect if shared utilities exist and update imports:

```php
// In copyBlockStubs method
$hasSharedUtilities = $this->files->exists(
    $this->resolvePath($rootsFiles, 'resources/js/blocks/shared')
);

// If utilities exist, use advanced template with imports
if ($hasSharedUtilities) {
    $editorTemplate = $this->getEditorTemplateWithUtilities();
} else {
    $editorTemplate = $this->getEditorTemplateBasic();
}
```

### Implementation Tasks

- [ ] Create `stubs/shared/` directory with utility files
- [ ] Create `sage-native-block:setup-utilities` command
- [ ] Add `--with-utilities` flag to main command
- [ ] Add auto-detection logic for existing utilities
- [ ] Update block templates to optionally use utilities
- [ ] Document utility usage
- [ ] Add examples to README

### Benefits

✅ **Reusable code** - One set of utilities for all blocks
✅ **Consistency** - All blocks use same presets
✅ **Maintainable** - Update presets in one place
✅ **Optional** - Only generate if needed
✅ **Cleaner templates** - Reduce boilerplate in block files

---

## Phase 3: Interactive Block Builder (LOW PRIORITY)

**Timeline:** Week 4-5
**Effort:** High
**Impact:** Quality of life improvement

### Overview

Add interactive prompts for common block configurations, allowing dynamic template generation based on user input.

### Features

#### Interactive Configuration Prompts

```bash
$ wp acorn sage-native-block:add-setup imagewize/my-block --interactive

Block name: imagewize/my-block
Template: [statistics]

Configuration options:
  How many columns for statistics? [2]
  Include icons? (yes/no) [yes]
  Include CTA section? (yes/no) [yes]
  Number of CTA columns? [2]
  Background color? (none/tertiary/primary) [tertiary]
  Include buttons? (yes/no) [yes]

Generating block with configuration...
✓ Block created successfully!
```

### Implementation

```php
protected $signature = 'sage-native-block:add-setup
                        {blockName? : The name of the block}
                        {--template= : Block template type}
                        {--interactive : Enable interactive configuration}
                        {--force : Force the operation to run without confirmation}';

public function handle(RootsFilesystem $rootsFiles): int
{
    // ... existing logic

    if ($this->option('interactive')) {
        $config = $this->gatherConfiguration($template);
        $this->generateFromConfig($fullBlockName, $template, $config);
    } else {
        $this->generateFromTemplate($fullBlockName, $template);
    }
}

protected function gatherConfiguration(string $template): array
{
    $config = [];

    switch ($template) {
        case 'statistics':
            $config['columns'] = $this->choice('How many columns?', [2, 3, 4], 0);
            $config['has_icons'] = $this->confirm('Include icons?', true);
            $config['has_cta'] = $this->confirm('Include CTA section?', true);
            $config['background'] = $this->choice('Background?', ['none', 'tertiary', 'primary'], 1);
            break;

        case 'two-column':
            $config['has_cards'] = $this->confirm('Wrap columns in cards?', true);
            $config['items_per_column'] = $this->ask('Items per column?', 2);
            break;
    }

    return $config;
}
```

### Benefits

✅ **Customized output** - Generate exactly what's needed
✅ **No editing required** - Block ready to use immediately
✅ **Flexible** - Different configurations from same template
❌ **Complex** - Significant development effort
❌ **Maintenance** - Need to update for each template

**Recommendation:** Consider only if Phase 1 & 2 prove highly successful.

---

## Phase 4: Documentation Generator (BONUS)

**Timeline:** Future
**Effort:** Medium
**Impact:** Documentation consistency

### Overview

Auto-generate documentation from block structure, keeping docs in sync with code.

### Features

```bash
# Generate documentation for a block
wp acorn sage-native-block:generate-docs imagewize/my-block

# Generate documentation for all blocks
wp acorn sage-native-block:generate-docs --all
```

**Output Example:**

```markdown
# Block: My Block

**Category:** imagewize
**Namespace:** imagewize/my-block
**Supports:** align (wide, full), anchor, spacing, color

## Structure

├── Main Heading (H2)
│   └── Font: Montserrat 3xl, Bold, Main color
├── Columns (2)
│   ├── Column 1
│   │   ├── Heading (H3)
│   │   └── Paragraph
│   └── Column 2
│       ├── Heading (H3)
│       └── Paragraph
└── Button
    └── Style: Fill, Main background, White text

## Attributes

- **align**: string (default: "wide")
- **className**: string

## Usage

Add block in editor:
1. Click "+" button
2. Search for "My Block"
3. Configure content via toolbar

## Styling

Block uses theme.json values for all typography and colors.
```

### Benefits

✅ **Consistent docs** - Auto-generated from source
✅ **Always up-to-date** - Regenerate after changes
✅ **Time saver** - No manual documentation

**Recommendation:** Nice-to-have feature for future releases.

---

## Success Metrics

### Development Time Reduction
- **Before:** 2-3 hours per complex block
- **After Phase 1:** 30-45 minutes per complex block
- **Target:** 70-80% time reduction

### Code Quality
- Consistent InnerBlocks patterns across all blocks
- Proper theme.json integration from start
- Reduced copy-paste errors

### Adoption
- 100% of new blocks use template system
- Developers prefer template generation over manual setup
- Positive feedback from theme developers

---

## Implementation Strategy

### Week 1: Foundation
- Create `innerblocks` template (simplest, most useful)
- Add `--template` flag to command
- Test with real block generation
- Document new usage

### Week 2: Expand Templates
- Create `two-column` template
- Create `statistics` template
- Add interactive template selection
- Comprehensive testing

### Week 3: Shared Utilities (Phase 2)
- Create shared utilities stubs
- Add setup command
- Update templates to use utilities
- Document utility usage

### Week 4-5: Polish & Release
- Fix bugs from testing
- Complete documentation
- Update README with examples
- Release v2.0.0 with template system

---

## Breaking Changes & Backwards Compatibility

### No Breaking Changes
- Existing command usage continues to work
- Default behavior unchanged (basic template)
- All new features are opt-in via flags

### Deprecation Path (Future)
If we later want to change defaults:
1. Add deprecation notice in v2.0
2. Change default in v3.0
3. Provide migration guide

---

## Future Enhancements

### Beyond Phase 4
- Block preview generation (screenshot automation)
- Block variations support
- Pattern generation from blocks
- Theme export/import of custom templates
- AI-assisted block generation (GPT integration)

---

## Questions & Considerations

### Open Questions
1. Should templates be theme-customizable? (Allow themes to override package templates)
2. Should we support custom user templates in config?
3. How to handle theme.json differences between themes?
4. Should we version templates separately from package?

### Technical Decisions
- Use config file for template definitions? ✅ Yes
- Support custom stub paths? ✅ Yes (via config)
- Auto-detect theme.json values? ❌ No (too complex for v1)
- Support template inheritance? ❌ No (YAGNI for v1)

---

## Resources

### Reference Implementations
- **multi-column-content block** - Complex statistics template example
- **two-column-card block** - Two-column layout template example
- **page-heading-blue block** - Simple InnerBlocks template example

### Related Documentation
- `CLAUDE.md` in Nynaeve theme - InnerBlocks best practices
- `PATTERN-TO-NATIVE-BLOCK.md` - Pattern conversion strategies
- WordPress Block Editor Handbook - InnerBlocks API

---

## Conclusion

Phase 1 (Block Templates) provides the highest ROI with moderate effort. It directly addresses the main pain point (setup time) and creates foundation for future enhancements.

**Recommended Action:** Start with Phase 1, gather feedback, then evaluate Phase 2 & beyond.

---

## Changelog

- **2025-10-14** - Initial planning document created
- _Future updates will be tracked here_
