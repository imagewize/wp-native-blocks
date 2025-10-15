# Custom Template Stubs - User Guide & Improvement Plan

**Status:** Planning
**Created:** 2025-10-15
**Goal:** Allow users to add custom block templates without modifying vendor package

---

## Current Problem

When a user creates custom template stubs in their Sage theme at:
```
wp-content/themes/mytheme/stubs/themes/imw/innerblocks/block.json
```

The system **detects** the theme folder and shows it in the category selection:
```
[3] Imw Theme - Production templates from Imw theme
```

However, when selected, it **fails** with:
```
No templates found for category 'imw'. Using default.
```

### Why This Happens

1. **Auto-detection works**: `getAvailableCategories()` at [SageNativeBlockCommand.php:565-572](SageNativeBlockCommand.php#L565-L572) scans `stubs/themes/` and finds custom folders
2. **Template lookup fails**: When user selects the category, `promptForTemplate()` filters config templates by category, but custom themes aren't in the config
3. **Stub path resolution fails**: `getStubPath()` at [line 677](SageNativeBlockCommand.php#L677) returns fallback `'block'` instead of the actual path like `'themes/imw/innerblocks'`

### Core Issue

**The system requires config entries for templates, but auto-detects theme folders without config.**

This creates a broken user experience:
- ✅ Theme detected
- ✅ Shows in menu
- ❌ Fails when selected
- ❌ User must edit vendor package config (not acceptable)

---

## Proposed Solutions

### Option 1: Auto-detect Individual Templates (Recommended)

**Concept**: Scan theme's `block-templates/` directory for actual block templates.

**Structure Change**:
```
# Old attempt (broken):
wp-content/themes/mytheme/stubs/themes/imw/innerblocks/...
                                    ↑         ↑
                                  theme    template

# Implemented (working):
wp-content/themes/mytheme/block-templates/innerblocks/...
                               ↑
                           template name
```

**Benefits**:
- ✅ Simpler path structure
- ✅ No nested `themes/` directory needed
- ✅ Template name = folder name (straightforward)
- ✅ Works with existing auto-detection logic
- ✅ No config file needed for custom templates

**Example**:
```
# User creates:
wp-content/themes/mytheme/block-templates/my-custom-block/
  ├── block.json
  ├── editor.jsx
  ├── save.jsx
  ├── index.js
  ├── editor.css
  ├── style.css
  └── view.js

# System auto-detects:
Template: "my-custom-block"
Path: stubs/my-custom-block/
```

**Implementation Notes**:
- Scan theme's `stubs/` root for folders containing `block.json`
- Add to templates list with auto-generated name/description
- Fallback to package templates if theme doesn't have custom stubs
- Priority: theme stubs > package stubs

**Challenges**:
- How to categorize auto-detected templates? (put in "Custom" category?)
- How to provide descriptions? (parse from block.json title?)
- Need clear naming convention to avoid conflicts

---

### Option 2: Convention-Based Config File in Theme

**Concept**: Allow theme to define templates via local config file.

**Structure**:
```
wp-content/themes/mytheme/
  ├── block-templates/
  │   ├── innerblocks/...
  │   └── hero/...
  └── config/
      └── sage-native-block-templates.php  # New file
```

**Example Config**:
```php
<?php
// wp-content/themes/mytheme/config/sage-native-block-templates.php

return [
    'innerblocks' => [
        'name' => 'InnerBlocks (IMW Theme)',
        'description' => 'IMW theme innerblocks template',
        'stub_path' => 'innerblocks',
        'category' => 'imw',
    ],
    'hero' => [
        'name' => 'Hero Section (IMW Theme)',
        'description' => 'IMW theme hero section',
        'stub_path' => 'hero',
        'category' => 'imw',
    ],
];
```

**Benefits**:
- ✅ Explicit template definitions
- ✅ User controls names/descriptions
- ✅ Supports grouping by category
- ✅ Clear documentation point
- ✅ No vendor package modification needed

**Implementation Notes**:
- Check for theme config file in command
- Merge theme templates with package templates
- Theme templates take precedence over package

**Challenges**:
- Requires users to maintain config file
- More complex than fully automatic detection
- Duplicates config structure from package

---

### Option 3: Smart Auto-Detection with Metadata

**Concept**: Auto-detect templates and read metadata from `block.json` or special metadata file.

**Structure**:
```
wp-content/themes/mytheme/block-templates/
  └── innerblocks-imw/
      ├── block.json
      ├── template-meta.json  # Optional metadata
      └── ...other files
```

**Metadata File** (optional):
```json
{
  "name": "InnerBlocks (IMW Theme)",
  "description": "Production-ready innerblocks template from IMW theme",
  "category": "imw",
  "author": "IMW Team",
  "version": "1.0.0"
}
```

**Benefits**:
- ✅ Fully automatic detection
- ✅ Optional rich metadata
- ✅ Falls back to sensible defaults (folder name, block.json title)
- ✅ No config file required
- ✅ Supports future extensions (version, author, etc.)

**Implementation Notes**:
- Scan `stubs/` folders for `block.json`
- Check for optional `template-meta.json`
- Generate name from folder name if no metadata
- Generate description from block.json title/description
- Auto-assign to "Custom" category or parse from metadata

**Challenges**:
- Need to define metadata schema
- Parsing/validation complexity
- Folder naming conventions become important

---

### Option 4: Hybrid - Simplified Structure + Auto-detect

**Concept**: Simplify path structure AND auto-detect with minimal metadata.

**Structure**:
```
wp-content/themes/mytheme/block-templates/
  ├── innerblocks/           # Package will check theme first
  │   └── ...files
  ├── hero-imw/              # Custom template
  │   └── ...files
  └── cta-custom/            # Another custom
      └── ...files
```

**Auto-detection Logic**:
1. Scan package `stubs/` (basic, generic/*, themes/nynaeve/*)
2. Scan theme `stubs/` (any folder with block.json)
3. Merge lists, theme overrides package if same name
4. Generate display names from folder names (kebab-case → Title Case)

**Example**:
```
Folder: innerblocks          → "Innerblocks"
Folder: hero-imw             → "Hero Imw"
Folder: two-column-custom    → "Two Column Custom"
```

**Benefits**:
- ✅ Dead simple for users
- ✅ No config needed
- ✅ Override package templates by matching name
- ✅ Clear naming convention
- ✅ Works immediately

**Implementation**:
- Single scan function that checks both locations
- Template name = folder name (no prefixes)
- Display name = humanized folder name
- Category = auto-assigned based on location (package vs theme)

**Challenges**:
- Less control over display names/descriptions
- Naming collisions possible (mitigated by override behavior)
- No category grouping for custom templates

---

## Final Decision: Option 4 (Hybrid) with `block-templates/` Directory

**Status:** ✅ Approved for Implementation

### Directory Naming: `block-templates/`

After evaluation, we're using **`block-templates/`** for theme-level templates:

**Rationale:**
- ✅ Clear, self-documenting name
- ✅ Familiar to WordPress developers (similar to FSE convention)
- ✅ Distinguishes from package `stubs/` directory
- ✅ Short and purposeful
- ✅ Easy to document the difference from FSE block templates

**Note on FSE Confusion:**
While WordPress uses `block-templates/` for Full Site Editing (HTML template files), our `block-templates/` contains **native block scaffolding templates** (code generation templates). Documentation will clearly distinguish these:

> **Not FSE Block Templates!**
> This `block-templates/` directory contains scaffolding templates for generating new Gutenberg blocks via `wp acorn sage-native-block:create`. These are different from WordPress FSE block templates (HTML files).

### Implemented Solution

1. **Simplified path structure** - Flat directory structure in theme
2. **Auto-detect all templates** - Scan both package and theme directories
3. **Simple override behavior** - Theme templates override package templates by name
4. **Optional metadata** - Support `template-meta.json` for rich descriptions
5. **Smart defaults** - Generate sensible names from folder names

### Directory Structure

**Package (vendor):**
```
vendor/imagewize/sage-native-block/
└── stubs/                          # Package keeps "stubs"
    ├── block/
    ├── generic/
    └── themes/
        └── nynaeve/
```

**Theme (user):**
```
wp-content/themes/mytheme/
└── block-templates/                # Theme uses "block-templates"
    ├── my-hero/
    ├── my-stats/
    └── innerblocks/                # Can override package "innerblocks"
```

### User Experience

**Simple Case** (no metadata):
```bash
# User creates:
mytheme/block-templates/my-hero/
  ├── block.json
  ├── index.js
  ├── editor.jsx
  └── ...

# System auto-detects and shows:
[X] My Hero (Custom Templates)
```

**Advanced Case** (with metadata):
```bash
# User creates:
mytheme/block-templates/my-hero/
  ├── block.json
  ├── template-meta.json
  └── ...

# template-meta.json:
{
  "name": "Hero Section (IMW)",
  "description": "Full-featured hero with video background support",
  "category": "imw"
}

# System shows under "IMW Theme" category:
[X] Hero Section (IMW) - Full-featured hero with video background support
```

### Implementation Steps

1. **Update path resolution**:
   - Package templates: Keep existing `stubs/` structure (no changes)
   - Theme templates: Scan `block-templates/` directory (new)
   - Direct lookup: `block-templates/{template-name}/`

2. **Update auto-detection**:
   - Scan package `stubs/` for configured templates
   - Scan theme `block-templates/` for folders with `block.json`
   - Merge lists, theme templates override package templates by name

3. **Add metadata support**:
   - Check for optional `template-meta.json` in each template folder
   - Parse for: `name`, `description`, `category` fields
   - Fall back to auto-generated values from folder name

4. **Update categorization**:
   - Package templates: Use existing categories from config (basic, generic, nynaeve)
   - Theme templates: Use `category` from metadata or default to "Custom Templates"
   - Group all templates by category in hierarchical selection menu

5. **Update template lookup**:
   - Check theme `block-templates/` first (user templates)
   - Fall back to package `stubs/` (package templates)
   - Don't require config entry for theme templates
   - Build stub path directly from template name
   - Validate folder + block.json existence before showing in menu

6. **Template metadata schema** (`template-meta.json`):
   ```json
   {
     "name": "Hero Section",
     "description": "Full-featured hero with background options",
     "category": "custom",
     "author": "Your Name",
     "version": "1.0.0"
   }
   ```
   - Only `name` is recommended; all fields are optional
   - Without metadata: folder name becomes display name

---

## Migration Path

### For Existing Users

✅ **No breaking changes** - all current package templates continue to work exactly as before.

### For New Custom Templates

**Before** (didn't work):
```
mytheme/stubs/themes/imw/innerblocks/block.json
```

**After** (works automatically):
```
mytheme/block-templates/innerblocks/
  ├── block.json
  ├── index.js
  ├── editor.jsx
  ├── save.jsx
  ├── editor.css
  ├── style.css
  └── view.js
```

Optional metadata file for better display:
```
mytheme/block-templates/innerblocks/
  ├── template-meta.json
  └── ...other files
```

### For Package Templates (Nynaeve, etc.)

No changes needed - existing structure continues to work:
```
vendor/imagewize/sage-native-block/stubs/
  ├── block/
  ├── generic/
  └── themes/
      └── nynaeve/
```

The `themes/nynaeve/` prefix is part of the `stub_path` in config, which is fine for package-provided templates.

---

## Edge Cases & Considerations

### 1. Name Collisions

**Scenario**: Theme has `stubs/innerblocks/` and package has `generic/innerblocks/`

**Solution**: Theme version takes precedence (override behavior)

**User Feedback**: Show indicator that template is from theme
```
[X] Innerblocks (from your theme) ⭐
[Y] Innerblocks (generic)
```

### 2. Missing Files

**Scenario**: Template folder exists but missing required files

**Solution**:
- Validate during scan (must have `block.json`)
- Show warning if other expected files missing
- Allow partial templates (user can add missing files)

### 3. Invalid Metadata

**Scenario**: `template-meta.json` exists but has invalid JSON

**Solution**:
- Log warning
- Fall back to auto-generated values
- Don't block template usage

### 4. Category Organization

**Scenario**: Many custom templates, hard to navigate

**Solution**:
- Support `category` in metadata
- Group custom templates by category
- Default to "Custom Templates" category if not specified

---

## Alternative: Keep Current Structure, Fix Logic

If we want to keep the `themes/` subdirectory approach:

### Required Changes

1. **Fix template lookup**:
   - When category selected, scan theme's `stubs/themes/{category}/` for templates
   - Auto-register templates found in that directory
   - Don't require config entries

2. **Fix stub path resolution**:
   - Build path from category + template name
   - Example: category=`imw`, template=`innerblocks` → `themes/imw/innerblocks`

3. **Update user guidance**:
   - Document expected structure clearly
   - Provide template starter/example

**Benefits**:
- Minimal code changes
- Keeps theme templates separated
- Clear organization by theme

**Drawbacks**:
- More nested structure
- Less intuitive for simple use cases
- Requires understanding of category concept

---

## Questions for Decision

1. **Path structure**: Flat (`stubs/{name}/`) vs Nested (`stubs/themes/{theme}/{name}/`)?
2. **Metadata**: Required vs Optional vs Not supported?
3. **Override behavior**: Should theme templates override package templates?
4. **Categorization**: How to organize custom templates in the menu?
5. **Validation**: How strict should file validation be?

---

## Next Steps

1. Get feedback on proposed approaches
2. Choose primary solution (recommendation: Option 4 Hybrid)
3. Create implementation plan with specific code changes
4. Update documentation with examples
5. Add tests for auto-detection logic
6. Consider backwards compatibility

---

## Related Files

- [SageNativeBlockCommand.php](../src/Console/SageNativeBlockCommand.php) - Main command implementation
- [config/sage-native-block.php](../config/sage-native-block.php) - Template configuration
- [TEMPLATE-STUBS.md](./TEMPLATE-STUBS.md) - Original template system planning
- [stubs/themes/README.md](../stubs/themes/README.md) - Theme templates documentation
