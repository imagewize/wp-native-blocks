# Nynaeve Theme Block Templates

Production-ready block templates from the [Nynaeve theme](https://github.com/imagewize/nynaeve) by Imagewize.

## About Nynaeve

Nynaeve is a modern WordPress theme built with Sage 11, featuring:
- Clean, professional design
- Full theme.json integration
- Optimized for performance
- Accessibility-first approach

## Available Templates

| Template | Description | Command |
|----------|-------------|---------|
| **innerblocks** | Container with heading and content | `--template=nynaeve-innerblocks` |
| **two-column** | Card-style two-column layout | `--template=nynaeve-two-column` |
| **statistics** | Multi-section statistics display | `--template=nynaeve-statistics` |
| **cta** | Call-to-action with buttons | `--template=nynaeve-cta` |

## Requirements

### Font Families

Your theme.json must define these font families:

```json
{
  "settings": {
    "typography": {
      "fontFamilies": [
        {
          "fontFamily": "Montserrat, sans-serif",
          "name": "Montserrat",
          "slug": "montserrat"
        },
        {
          "fontFamily": "'Open Sans', sans-serif",
          "name": "Open Sans",
          "slug": "open-sans"
        }
      ]
    }
  }
}
```

### Font Sizes

Required font size scale:

```json
{
  "settings": {
    "typography": {
      "fontSizes": [
        { "slug": "sm", "size": "0.875rem", "name": "Small" },
        { "slug": "base", "size": "1rem", "name": "Base" },
        { "slug": "lg", "size": "1.125rem", "name": "Large" },
        { "slug": "xl", "size": "1.25rem", "name": "Extra Large" },
        { "slug": "2xl", "size": "1.5rem", "name": "2X Large" },
        { "slug": "3xl", "size": "1.875rem", "name": "3X Large" },
        { "slug": "4xl", "size": "2.25rem", "name": "4X Large" }
      ]
    }
  }
}
```

### Color Palette

Required color slugs:

```json
{
  "settings": {
    "color": {
      "palette": [
        { "slug": "base", "color": "#ffffff", "name": "Base" },
        { "slug": "main", "color": "#1a202c", "name": "Main" },
        { "slug": "secondary", "color": "#4a5568", "name": "Secondary" },
        { "slug": "tertiary", "color": "#f7fafc", "name": "Tertiary" }
      ]
    }
  }
}
```

> **Note:** Adjust the actual color values to match your theme's design system.

## Usage

### Quick Start

```bash
# Create a statistics block with Nynaeve styling
wp acorn sage-native-block:add-setup imagewize/my-stats --template=nynaeve-statistics

# Create a CTA block
wp acorn sage-native-block:add-setup imagewize/my-cta --template=nynaeve-cta
```

### Customizing for Your Theme

If your theme has similar but not identical settings:

1. **Generate with Nynaeve template:**
   ```bash
   wp acorn sage-native-block:add-setup my-block --template=nynaeve-innerblocks
   ```

2. **Update the generated `editor.jsx`:**
   - Change font family slugs to match your theme
   - Update color slugs to match your palette
   - Adjust font size slugs if needed

3. **Test in the editor:**
   - Verify fonts render correctly
   - Check colors display properly
   - Adjust spacing if needed

## Template Features

### InnerBlocks
- Pre-configured heading (H2) with Montserrat
- Paragraph with Open Sans
- Center-aligned by default
- Flexible, unlocked template

### Two-Column
- Card-style groups with padding
- Responsive layout (stacks on mobile)
- Equal-height columns
- Background color support

### Statistics
- Statistics row with 2 columns
- Center heading section
- Benefits section with 3 columns
- Icon support via CSS `::before`
- Full typography styling

### CTA
- Heading and description
- Primary and outline button styles
- Flexible button layout
- Center-aligned content
- Responsive button stacking

## Differences from Generic Templates

| Feature | Generic | Nynaeve |
|---------|---------|---------|
| Font families | None | Montserrat, Open Sans |
| Font sizes | None | 3xl, 2xl, xl, lg, base, sm |
| Colors | None | main, secondary, tertiary, base |
| Spacing | Minimal | Theme-specific presets |
| Alignment | Basic | Center-aligned, styled |
| Ready to use | Needs styling | Production-ready |

## Troubleshooting

### Fonts not displaying correctly

**Problem:** Blocks show wrong fonts or fallback to system fonts.

**Solution:** Ensure your theme.json includes the required font families with correct slugs:
```json
{ "slug": "montserrat", ... }
{ "slug": "open-sans", ... }
```

### Colors not working

**Problem:** Text/background colors don't apply.

**Solution:** Add the required color palette to your theme.json:
```json
{ "slug": "main", ... }
{ "slug": "secondary", ... }
{ "slug": "tertiary", ... }
```

### Font sizes incorrect

**Problem:** Text is too large or too small.

**Solution:** Define the font size scale in your theme.json or adjust the generated templates to use your theme's size slugs.

## Learn More

- [Nynaeve Theme Documentation](https://github.com/imagewize/nynaeve)
- [WordPress theme.json Reference](https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-json/)
- [InnerBlocks Best Practices](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-templates/)

## Credits

**Theme:** Nynaeve
**Author:** Imagewize
**License:** MIT

---

💡 **Tip:** If these templates don't match your theme setup, use the `generic/` templates instead and add your own styling.
