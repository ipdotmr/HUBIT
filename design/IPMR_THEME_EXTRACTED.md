# IPMR Theme - Extracted Design Tokens
**Extracted from:** https://my.ip.mr/  
**Date:** October 15, 2025  
**Status:** Complete

---

## Brand Colors

**Primary Blue:**
- RGB: `rgb(40, 49, 148)`
- Hex: `#283194`
- Usage: Primary buttons, links, accents

**Brand Green (Logo):**
- Hex: `#5ca85e`
- Usage: Logo, success states

---

## Color Palette

**Backgrounds:**
```css
--color-background: #f7f7f8;       /* Main background */
--color-surface: #ffffff;          /* Cards, panels */
```

**Text:**
```css
--color-text: #17191c;             /* Primary text */
--color-text-muted: #64748b;       /* Secondary text */
```

**Borders:**
```css
--color-border: #e5e7eb;           /* Default borders */
```

---

## Typography

**Font Family:**
```css
--font-sans: 'Inter', sans-serif;
```

**Font Sizes:**
```css
--text-display: 40px;              /* Hero headings */
--text-base: 16px;                 /* Body text */
```

**Font Weights:**
```css
--font-bold: 700;                  /* Headings */
--font-normal: 400;                /* Body */
```

---

## Spacing & Layout

**Border Radius:**
```css
--radius-button: 3px;              /* Buttons */
--radius-card: 8px;                /* Cards */
```

**Shadows:**
```css
--shadow-card: 0 2px 4px rgba(0, 0, 0, 0.1);
```

---

## Component Styles

### Buttons
```css
.btn-primary {
  background: #283194;
  color: #ffffff;
  border-radius: 3px;
  padding: 10px 20px;
  font-weight: 500;
}

.btn-primary:hover {
  background: #1f2775;
}
```

### Cards
```css
.card {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 24px;
}
```

### Navigation
```css
nav a {
  color: #17191c;
  font-size: 14px;
  font-weight: 400;
}

nav a:hover {
  color: #283194;
}
```

---

## Tailwind Config (IPMR Theme)

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#283194',
          50: '#eef0ff',
          100: '#e0e4ff',
          200: #c7ceff',
          300: '#a5b1fc',
          400: '#8890f8',
          500: '#6c74ef',
          600: '#5359e3',
          700: '#4349c8',
          800: '#283194',  // Brand primary
          900: '#1f2775',
        },
        brand: {
          green: '#5ca85e',
        },
        background: '#f7f7f8',
        surface: '#ffffff',
        text: {
          DEFAULT: '#17191c',
          muted: '#64748b',
        },
        border: '#e5e7eb',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
      fontSize: {
        'display': '40px',
      },
      borderRadius: {
        'button': '3px',
        'card': '8px',
      },
      boxShadow: {
        'card': '0 2px 4px rgba(0, 0, 0, 0.1)',
      },
    },
  },
};
```

---

## Visual Characteristics

**Overall Style:**
- Clean, professional
- Blue-dominant color scheme
- Subtle shadows
- Minimal border radius
- Inter typography throughout

**Key Differences from Other Themes:**
- Lower border radius (3px vs 6px)
- Specific blue tone (#283194)
- Light gray background (#f7f7f8)
- Green accent for branding

---

## Implementation Notes

1. Use `#283194` as primary brand color across all interactive elements
2. Maintain Inter font family (already in use)
3. Keep border radius minimal (3px for buttons, 8px for cards)
4. Use `#f7f7f8` background instead of pure white
5. Logo green (`#5ca85e`) can be used for success states

**Ready for implementation in Phase 2!**
