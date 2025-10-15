# HUBIT Design Token System
**Version:** 1.0  
**Date:** October 15, 2025  
**Purpose:** Complete design token specification for 5 themes

---

## Design Philosophy

**Core Principles:**
- Enterprise-grade professionalism
- Accessibility-first (WCAG 2.1 AA)
- Performance-optimized (no layout shifts)
- RTL-ready for Arabic
- Dark mode support

**References:**
- **Stripe** - Clean, trustworthy, gradient accents
- **Linear** - Dark elegance, crisp typography
- **Radix UI** - Accessible primitives, refined tokens
- **shadcn/ui** - Modern component patterns
- **Tailwind UI** - Professional layouts

---

## Token Structure

All themes share the same token structure for consistency:

```typescript
interface DesignTokens {
  colors: ColorScale;
  typography: Typography;
  spacing: Spacing;
  borderRadius: BorderRadius;
  shadows: Shadows;
  transitions: Transitions;
}
```

---

## Theme 1: HUBIT Classic (Default)

### Color Palette

**Primary (Indigo):**
```css
--color-primary-50: #eef2ff;
--color-primary-100: #e0e7ff;
--color-primary-200: #c7d2fe;
--color-primary-300: #a5b4fc;
--color-primary-400: #818cf8;
--color-primary-500: #6366f1;  /* Main brand */
--color-primary-600: #4f46e5;  /* Interactive */
--color-primary-700: #4338ca;
--color-primary-800: #3730a3;
--color-primary-900: #312e81;
--color-primary-950: #1e1b4b;
```

**Secondary (Slate):**
```css
--color-secondary-50: #f8fafc;
--color-secondary-100: #f1f5f9;
--color-secondary-200: #e2e8f0;
--color-secondary-300: #cbd5e1;
--color-secondary-400: #94a3b8;
--color-secondary-500: #64748b;
--color-secondary-600: #475569;
--color-secondary-700: #334155;
--color-secondary-800: #1e293b;
--color-secondary-900: #0f172a;
--color-secondary-950: #020617;
```

**Success (Emerald):**
```css
--color-success-50: #ecfdf5;
--color-success-500: #10b981;
--color-success-700: #047857;
```

**Warning (Amber):**
```css
--color-warning-50: #fffbeb;
--color-warning-500: #f59e0b;
--color-warning-700: #b45309;
```

**Error (Red):**
```css
--color-error-50: #fef2f2;
--color-error-500: #ef4444;
--color-error-700: #b91c1c;
```

**Semantic Colors:**
```css
--color-background: #ffffff;
--color-surface: #f8fafc;
--color-surface-hover: #f1f5f9;
--color-border: #e2e8f0;
--color-border-strong: #cbd5e1;
--color-text: #0f172a;
--color-text-muted: #64748b;
--color-text-disabled: #cbd5e1;
```

### Typography

**Font Families:**
```css
--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
--font-mono: 'JetBrains Mono', 'Fira Code', monospace;
```

**Font Sizes:**
```css
--text-xs: 0.75rem;     /* 12px */
--text-sm: 0.875rem;    /* 14px */
--text-base: 1rem;      /* 16px */
--text-lg: 1.125rem;    /* 18px */
--text-xl: 1.25rem;     /* 20px */
--text-2xl: 1.5rem;     /* 24px */
--text-3xl: 1.875rem;   /* 30px */
--text-4xl: 2.25rem;    /* 36px */
```

**Font Weights:**
```css
--font-normal: 400;
--font-medium: 500;
--font-semibold: 600;
--font-bold: 700;
```

**Line Heights:**
```css
--leading-tight: 1.25;
--leading-snug: 1.375;
--leading-normal: 1.5;
--leading-relaxed: 1.625;
```

### Spacing

```css
--space-1: 0.25rem;   /* 4px */
--space-2: 0.5rem;    /* 8px */
--space-3: 0.75rem;   /* 12px */
--space-4: 1rem;      /* 16px */
--space-5: 1.25rem;   /* 20px */
--space-6: 1.5rem;    /* 24px */
--space-8: 2rem;      /* 32px */
--space-10: 2.5rem;   /* 40px */
--space-12: 3rem;     /* 48px */
--space-16: 4rem;     /* 64px */
```

### Border Radius

```css
--radius-sm: 0.25rem;   /* 4px */
--radius-md: 0.375rem;  /* 6px */
--radius-lg: 0.5rem;    /* 8px */
--radius-xl: 0.75rem;   /* 12px */
--radius-2xl: 1rem;     /* 16px */
--radius-full: 9999px;
```

### Shadows

```css
--shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
--shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
--shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
--shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
```

### Transitions

```css
--transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-base: 200ms cubic-bezier(0.4, 0, 0.2, 1);
--transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
```

---

## Theme 2: HUBIT Midnight (Dark)

### Color Palette

**Primary (Purple):**
```css
--color-primary-50: #faf5ff;
--color-primary-500: #a855f7;   /* Main brand */
--color-primary-600: #9333ea;   /* Interactive */
--color-primary-700: #7e22ce;
```

**Semantic Colors (Dark Mode):**
```css
--color-background: #0a0a0a;
--color-surface: #141414;
--color-surface-hover: #1f1f1f;
--color-border: #2a2a2a;
--color-border-strong: #3f3f3f;
--color-text: #ededed;
--color-text-muted: #a1a1a1;
--color-text-disabled: #525252;
```

**Special Effects:**
```css
--glow-primary: 0 0 20px rgba(168, 85, 247, 0.5);
--backdrop-blur: blur(12px);
```

**Inspiration:** Linear app - high contrast, subtle animations, elegant dark UI

---

## Theme 3: HUBIT Neon (Vibrant)

### Color Palette

**Primary (Cyan):**
```css
--color-primary-50: #ecfeff;
--color-primary-500: #06b6d4;   /* Main brand */
--color-primary-600: #0891b2;   /* Interactive */
--color-primary-700: #0e7490;
```

**Gradients:**
```css
--gradient-primary: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
--gradient-surface: linear-gradient(135deg, #f0fdfa 0%, #f0f9ff 100%);
```

**Special Effects:**
```css
--glass-bg: rgba(255, 255, 255, 0.7);
--glass-border: rgba(255, 255, 255, 0.18);
--glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
--backdrop-blur: blur(10px);
```

**Inspiration:** Stripe gradient headers, glassmorphism, vibrant accents

---

## Theme 4: HUBIT Minimal (Ultra-Light)

### Color Palette

**Primary (Slate):**
```css
--color-primary-50: #f8fafc;
--color-primary-500: #64748b;   /* Main brand */
--color-primary-600: #475569;   /* Interactive */
--color-primary-700: #334155;
```

**Semantic Colors:**
```css
--color-background: #ffffff;
--color-surface: #fafafa;
--color-surface-hover: #f5f5f5;
--color-border: #f0f0f0;
--color-border-strong: #e5e5e5;
--color-text: #171717;
--color-text-muted: #737373;
--color-text-disabled: #d4d4d4;
```

**Typography Adjustments:**
```css
--font-sans: -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;
--space-unit: 8px;  /* 8px grid system */
```

**Inspiration:** Apple HIG - spacious, clean, minimal chrome

---

## Theme 5: HUBIT IPMR (Brand Match)

**Extracted from my.ip.mr:**

### Color Palette (To be extracted)

```css
/* Primary brand colors from my.ip.mr */
--color-primary-500: #[TBD];
--color-primary-600: #[TBD];

/* Accent colors */
--color-accent: #[TBD];

/* Backgrounds */
--color-background: #[TBD];
--color-surface: #[TBD];
```

**Extraction Strategy:**
1. Visit https://my.ip.mr/
2. Use DevTools to inspect CSS variables
3. Extract color palette, typography, spacing
4. Recreate in Tailwind config
5. Match visual hierarchy exactly

**Note:** Will crawl and extract exact tokens after approval

---

## Component Patterns

### Buttons

**Primary Button:**
```css
background: var(--color-primary-600);
color: white;
padding: var(--space-2) var(--space-4);
border-radius: var(--radius-md);
font-weight: var(--font-medium);
transition: var(--transition-fast);

&:hover {
  background: var(--color-primary-700);
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}
```

**Secondary Button:**
```css
background: transparent;
color: var(--color-primary-600);
border: 1px solid var(--color-border);
```

**Ghost Button:**
```css
background: transparent;
color: var(--color-text-muted);

&:hover {
  background: var(--color-surface-hover);
}
```

### Form Inputs

```css
.input {
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  padding: var(--space-2) var(--space-3);
  font-size: var(--text-sm);
  transition: var(--transition-fast);
}

.input:focus {
  outline: none;
  border-color: var(--color-primary-500);
  box-shadow: 0 0 0 3px var(--color-primary-50);
}

.input:disabled {
  background: var(--color-surface);
  color: var(--color-text-disabled);
  cursor: not-allowed;
}
```

### Cards

```css
.card {
  background: var(--color-background);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-lg);
  padding: var(--space-6);
  box-shadow: var(--shadow-sm);
  transition: var(--transition-base);
}

.card:hover {
  box-shadow: var(--shadow-md);
  border-color: var(--color-border-strong);
}
```

---

## Accessibility Requirements

### Color Contrast Ratios

**Normal Text (16px+):**
- Minimum: 4.5:1
- Enhanced: 7:1

**Large Text (18px+ or 14px+ bold):**
- Minimum: 3:1
- Enhanced: 4.5:1

**Interactive Elements:**
- Focus indicator: 3:1
- Border/icon: 3:1

### Focus States

```css
:focus-visible {
  outline: 2px solid var(--color-primary-500);
  outline-offset: 2px;
}
```

### Motion

```css
@media (prefers-reduced-motion: reduce) {
  * {
    animation-duration: 0.01ms !important;
    transition-duration: 0.01ms !important;
  }
}
```

---

## RTL Support (Arabic)

### Layout Adjustments

```css
[dir="rtl"] {
  direction: rtl;
  text-align: right;
}

[dir="rtl"] .sidebar {
  border-left: none;
  border-right: 1px solid var(--color-border);
}

[dir="rtl"] .icon {
  transform: scaleX(-1);  /* Flip directional icons */
}
```

### Typography

```css
[dir="rtl"] {
  --font-sans: 'IBM Plex Sans Arabic', 'Noto Sans Arabic', sans-serif;
  letter-spacing: 0;  /* Arabic doesn't need letter spacing */
}
```

---

## Performance Optimizations

### Font Loading

```css
@font-face {
  font-family: 'Inter';
  font-display: swap;  /* Prevent FOIT */
  unicode-range: U+0020-007F;  /* Subset */
}
```

### Critical CSS

Inline above-the-fold styles in `<head>` for LCP optimization.

### Lazy Loading

```javascript
// Load theme dynamically
const loadTheme = async (themeName) => {
  const theme = await import(`./themes/${themeName}.css`);
  document.documentElement.className = themeName;
};
```

---

## Implementation in Tailwind

### tailwind.config.js

```javascript
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: {
          50: 'var(--color-primary-50)',
          // ... all shades
          600: 'var(--color-primary-600)',
        },
        // ... other color scales
      },
      fontFamily: {
        sans: 'var(--font-sans)',
        mono: 'var(--font-mono)',
      },
      spacing: {
        1: 'var(--space-1)',
        // ... all spacing values
      },
      borderRadius: {
        sm: 'var(--radius-sm)',
        md: 'var(--radius-md)',
        lg: 'var(--radius-lg)',
      },
      boxShadow: {
        sm: 'var(--shadow-sm)',
        md: 'var(--shadow-md)',
        lg: 'var(--shadow-lg)',
      },
    },
  },
  plugins: [],
};
```

---

## Next Steps

1. ✅ Approve token system
2. Create interactive moodboard with component examples
3. Extract IPMR theme tokens from my.ip.mr
4. Implement theme switcher
5. Build component library

**Ready for your review!**
