/**
 * HUBIT Theme Configuration
 * All 5 themes with metadata
 */

export const themes = [
  {
    id: 'ipmr',
    name: 'HUBIT IPMR',
    description: 'Brand match with my.ip.mr',
    primary: '#283194',
    isDefault: true,
    category: 'light',
  },
  {
    id: 'classic',
    name: 'HUBIT Classic',
    description: 'Professional and trustworthy',
    primary: '#4f46e5',
    isDefault: false,
    category: 'light',
  },
  {
    id: 'midnight',
    name: 'HUBIT Midnight',
    description: 'Modern dark theme',
    primary: '#a855f7',
    isDefault: false,
    category: 'dark',
  },
  {
    id: 'neon',
    name: 'HUBIT Neon',
    description: 'Vibrant and energetic',
    primary: '#06b6d4',
    isDefault: false,
    category: 'light',
  },
  {
    id: 'minimal',
    name: 'HUBIT Minimal',
    description: 'Clean and spacious',
    primary: '#475569',
    isDefault: false,
    category: 'light',
  },
];

export const getThemeById = (id) => {
  return themes.find(theme => theme.id === id) || themes.find(theme => theme.isDefault);
};

export const getDefaultTheme = () => {
  return themes.find(theme => theme.isDefault);
};
