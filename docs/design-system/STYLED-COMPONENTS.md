# 💅 Panduan Implementasi `styled-components` — Koneksa Platform

Repositori ini telah menginstal dependensi `styled-components` (`^6.6.0`). Dokumen ini menjelaskan standar implementasi, arsitektur tema, dan pola pembuatan komponen antarmuka yang terisolasi dan mudah dikelola.

---

## 1. Tujuan Penggunaan `styled-components`

Di Koneksa Platform, `styled-components` digunakan untuk:
1. **Enkapsulasi Desain Komponen (Encapsulated CSS-in-JS)**: Mencegah kebocoran styling antar-halaman.
2. **Dynamic Styling Berbasis Props**: Komponen dapat beradaptasi secara elegan terhadap *state* (misal: `$variant="primary"`, `$isActive={true}`, `$elevation="high"`).
3. **Penyatuan Design Tokens**: Mengonsumsi variabel token tema global secara terpusat.
4. **Desain Komponen Atomik**: Membangun fondasi tombol, badge, card, modal, dan elemen interaktif yang konsisten.

---

## 2. Struktur Direktori Styling Komponen

Disarankan mengorganisir komponen berbasis styling pada direktori berikut:

```text
resources/js/
├── Components/
│   ├── UI/                           # Komponen UI atomik
│   │   ├── Button/
│   │   │   ├── StyledButton.js       # Definisi styled-components
│   │   │   └── index.js (atau .vue)  # Komponen pembungkus
│   │   ├── Card/
│   │   │   └── StyledCard.js
│   │   └── Badge/
│   │       └── StyledBadge.js
│   └── ...
├── Theme/
│   ├── tokens.js                     # Objek token (warna, font, radius, shadow)
│   ├── themeProvider.js              # Wrapper ThemeProvider
│   └── index.js
```

---

## 3. Konfigurasi Objek Tema (`tokens.js`)

Buat berkas `resources/js/Theme/tokens.js` sebagai acuan tema:

```javascript
// resources/js/Theme/tokens.js
export const theme = {
    colors: {
        primary: {
            50: '#EEF2FF',
            100: '#E0E7FF',
            500: '#6366F1',
            600: '#4F46E5', // Brand Primary
            700: '#4338CA',
            900: '#312E81',
        },
        secondary: {
            500: '#06B6D4',
            600: '#0891B2',
        },
        neutral: {
            50: '#F8FAFC',
            100: '#F1F5F9',
            200: '#E2E8F0',
            700: '#334155',
            800: '#1E293B',
            900: '#0F172A',
        },
        semantic: {
            success: '#10B981',
            warning: '#F59E0B',
            danger: '#EF4444',
            info: '#3B82F6',
        },
    },
    radii: {
        sm: '4px',
        md: '8px',
        lg: '12px',
        full: '9999px',
    },
    shadows: {
        sm: '0 1px 2px 0 rgb(0 0 0 / 0.05)',
        md: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
        lg: '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
    },
    transitions: {
        fast: '150ms cubic-bezier(0.4, 0, 0.2, 1)',
        normal: '250ms cubic-bezier(0.4, 0, 0.2, 1)',
    },
};
```

---

## 4. Pola Pembuatan Komponen (Styled Primitives)

### Aturan Transient Props (`$propName`)
Dalam `styled-components` versi 6, selalu gunakan prefiks dollar (`$`) untuk props kustom agar prop tersebut tidak diteruskan ke elemen DOM HTML standar:

```javascript
// Contoh: resources/js/Components/UI/Button/StyledButton.js
import styled, { css } from 'styled-components';

export const StyledButton = styled.button`
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: inherit;
    font-weight: 600;
    font-size: 0.875rem;
    line-height: 1.25rem;
    border-radius: ${({ theme }) => theme.radii?.md || '8px'};
    padding: ${({ $size }) => ($size === 'lg' ? '12px 24px' : $size === 'sm' ? '6px 12px' : '10px 18px')};
    cursor: pointer;
    border: 1px solid transparent;
    transition: all ${({ theme }) => theme.transitions?.fast || '150ms ease'};

    /* Varian Primer */
    ${({ $variant, theme }) =>
        $variant === 'primary' &&
        css`
            background-color: ${theme.colors?.primary?.[600] || '#4F46E5'};
            color: #ffffff;
            &:hover:not(:disabled) {
                background-color: ${theme.colors?.primary?.[700] || '#4338CA'};
                box-shadow: ${theme.shadows?.md || '0 4px 6px -1px rgba(0,0,0,0.1)'};
            }
        `}

    /* Varian Sekunder */
    ${({ $variant, theme }) =>
        $variant === 'secondary' &&
        css`
            background-color: #ffffff;
            color: ${theme.colors?.neutral?.[800] || '#1E293B'};
            border-color: ${theme.colors?.neutral?.[200] || '#E2E8F0'};
            &:hover:not(:disabled) {
                background-color: ${theme.colors?.neutral?.[50] || '#F8FAFC'};
            }
        `}

    /* Varian Bahaya / Danger */
    ${({ $variant, theme }) =>
        $variant === 'danger' &&
        css`
            background-color: ${theme.colors?.semantic?.danger || '#EF4444'};
            color: #ffffff;
            &:hover:not(:disabled) {
                background-color: #dc2626;
            }
        `}

    /* State Disabled */
    &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* Accessibility Focus Visible */
    &:focus-visible {
        outline: 2px solid ${theme.colors?.primary?.[500] || '#6366F1'};
        outline-offset: 2px;
    }
`;
```

---

## 5. Hubungan dengan Tailwind CSS (Koeksistensi Bersih)

Agar kode tetap rapi dan tidak saling menimpa (*conflicting*):

1. **Gunakan Tailwind untuk**:
   - Layout halaman utama (`flex`, `grid`, `grid-cols-3`, `gap-6`).
   - Margin jarak antar section (`my-8`, `container mx-auto`).
   - Penyesuaian responsif makro (`hidden md:block`).

2. **Gunakan `styled-components` untuk**:
   - Komponen atomik & molekuler (Button, Badge, Avatar, Alert, Card spesifik).
   - Efek CSS tingkat lanjut (masking, custom keyframes animation, pseudo-elements kompleks).
   - Komponen yang perilakunya sangat bergantung pada props JavaScript dinamis.

3. **Hindari**:
   - Menggunakan `!important` di Tailwind untuk menimpa class styled-components.
   - Menulis styling inline `style={{ ... }}` jika dapat ditangani oleh styled props.

