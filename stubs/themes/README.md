# Theme-Specific Block Templates

This directory contains block templates from real-world themes. These are production-ready examples that demonstrate best practices for theme.json integration.

## Available Themes

### Nynaeve Theme (by Imagewize)
Located in: `nynaeve/`

Pre-styled block templates from the Nynaeve theme, featuring:
- Typography using Montserrat and Open Sans fonts
- Color system with main, secondary, tertiary color slugs
- Font size scale: 3xl, 2xl, xl, lg, base, sm
- Production-ready styling and spacing

See [`nynaeve/README.md`](nynaeve/README.md) for detailed requirements and usage.

## Using Theme Templates

### Command Usage

```bash
# Use Nynaeve theme templates
wp acorn sage-native-block:add-setup my-block --template=nynaeve-innerblocks
wp acorn sage-native-block:add-setup my-stats --template=nynaeve-statistics
wp acorn sage-native-block:add-setup my-cta --template=nynaeve-cta
```

### When to Use Theme Templates

✅ **Use theme templates if:**
- Your theme.json matches the template requirements
- You want production-ready, fully-styled blocks
- You're looking for best-practice examples
- You want to learn advanced theme.json integration

⚠️ **Use generic templates instead if:**
- You have a different theme setup
- You want to add your own styles
- You need universal compatibility
- You're not sure about theme.json requirements

## Contributing Your Theme Templates

Want to add templates from your theme? Great! Here's how:

### 1. Create Your Theme Directory

```bash
stubs/themes/your-theme-name/
├── README.md              # Requirements and usage
├── innerblocks/           # Your templates
├── two-column/
├── statistics/
└── cta/
```

### 2. Document Requirements

Create a `README.md` in your theme directory that includes:
- Theme name and author
- Link to theme repository (if public)
- Required theme.json settings (fonts, colors, sizes)
- Any special configuration needed
- Example screenshots (optional)

### 3. Update Configuration

Add your templates to `config/sage-native-block.php`:

```php
'templates' => [
    // ... existing templates

    // Your theme templates
    'your-theme-innerblocks' => [
        'name' => 'InnerBlocks (Your Theme)',
        'description' => 'From Your Theme - describe requirements',
        'stub_path' => 'themes/your-theme/innerblocks',
    ],
    // ... more templates
],
```

### 4. Template Naming Convention

Use this pattern for template keys:
```
{theme-name}-{template-type}
```

Examples:
- `nynaeve-innerblocks`
- `sage-starter-cta`
- `acme-corp-statistics`

### 5. Submit a Pull Request

Once your templates are ready:
1. Test them thoroughly
2. Document all requirements
3. Submit a PR to the sage-native-block repository
4. Include screenshots if possible

## Quality Guidelines

Theme templates should:
- ✅ Be based on real, production themes
- ✅ Include complete documentation of requirements
- ✅ Follow WordPress block best practices
- ✅ Use theme.json values (not hardcoded styles)
- ✅ Be fully tested and working
- ✅ Include all 7 required files per template
- ✅ Have clear attribution to the source theme

## Questions?

- Check existing theme templates for examples
- Review the main package documentation
- Open an issue on GitHub for questions

---

**Remember:** Theme templates are opinionated and require specific setup. For universal compatibility, use the `generic/` templates instead.
