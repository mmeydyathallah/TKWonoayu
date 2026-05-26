# Design System Strategy: The Playful Atelier

## 1. Overview & Creative North Star
**Creative North Star: "The Playful Atelier"**
The design system for TK Wonoayu Madiun moves away from the sterile, spreadsheet-heavy look of traditional administrative software. Instead, it adopts the philosophy of a high-end art studio: organized, professional, yet bursting with creative energy. 

We break the "standard template" look by utilizing **Structured Whimsy**. This means using rigid administrative data but housing it within organic, ultra-rounded containers, asymmetric layouts, and deep tonal layering. We treat the desktop interface not as a flat screen, but as a series of tactile, overlapping papers and glass panels that feel approachable for teachers while remaining authoritative for administrators.

## 2. Colors & Surface Philosophy
The palette balances the serenity of education with the vibrancy of childhood. 

*   **Primary (Blue - #0060ad):** Represents the "Trust" of the institution. Used for primary navigation and deep administrative actions.
*   **Secondary (Green - #136e27):** Represents "Growth." Used for student progress, success states, and environmental themes.
*   **Tertiary (Yellow - #775b00):** Represents "Energy." Used for highlights, schedules, and playful accents.

### The "No-Line" Rule
To achieve a premium, editorial feel, **1px solid borders are strictly prohibited** for sectioning. Boundaries must be defined by:
*   **Background Shifts:** Place a `surface-container-low` card on a `surface` background.
*   **Negative Space:** Use the spacing scale to create "islands" of information.

### Surface Hierarchy & Nesting
Treat the UI as a physical desk. 
1.  **The Desk (Base):** `surface` (#f8f9fb).
2.  **The Blotter (Sections):** `surface-container-low` (#f0f4f7) to group related modules.
3.  **The Paper (Active Items):** `surface-container-lowest` (#ffffff) for cards or focused data entry.
4.  **The Glass (Floating Elements):** Use `surface-container-lowest` at 70% opacity with a `20px` backdrop blur for modals and dropdowns.

### Signature Textures
Avoid flat blocks of color for major components. Use a subtle linear gradient from `primary` to `primary-container` (at a 135-degree angle) for primary buttons and dashboard hero headers to provide a "lit from within" glow.

## 3. Typography: The Editorial Voice
We use a dual-font strategy to balance character with clarity.

*   **Display & Headlines (Plus Jakarta Sans):** A friendly, geometric sans-serif. Use `display-lg` for dashboard welcomes and `headline-md` for section titles. The generous x-height feels modern and welcoming.
*   **Body & Labels (Manrope):** A high-performance functional font. Manrope’s clean structure ensures that complex student data and financial tables remain legible even at `body-sm` (0.75rem).

**Editorial Hint:** Use `display-sm` for numeric data (e.g., total student count) to make administrative stats feel like a curated magazine layout rather than a database.

## 4. Elevation & Depth: Tonal Layering
Traditional drop shadows are replaced with **Ambient Depth**.

*   **The Layering Principle:** Instead of shadows, stack `surface-container-lowest` cards on top of `surface-container-high` backgrounds. This creates a "soft lift."
*   **Ambient Shadows:** If a floating element (like a student profile popover) requires a shadow, use a large blur (32px) at 6% opacity, using the `on-surface` color (#2c3437) rather than pure black.
*   **The "Ghost Border" Fallback:** If a container needs more definition against a similar background, use a 1px border with the `outline-variant` token at **15% opacity**. It should be felt, not seen.

## 5. Components

### Buttons
*   **Primary:** Ultra-rounded (`rounded-full`), using the signature gradient. 
*   **Secondary:** No background. Use a "Ghost Border" and `primary` text.
*   **Interaction:** On hover, the button should scale by 1.02 and increase the shadow diffusion slightly—never change the background color to a darker, muddy tone.

### Input Fields
*   **Style:** Avoid the "white box with a border." Use `surface-container-high` as the field background with a `rounded-DEFAULT` (1rem) corner. 
*   **Focus State:** The background shifts to `surface-container-lowest` and gains a 2px `primary` ghost border.

### Cards & Lists
*   **Cards:** Use `rounded-lg` (2rem) for dashboard cards. Forbid dividers. Separate list items using a `8px` vertical gap and a subtle background shift on hover (`surface-container-highest`).
*   **Student Profile Chips:** Use `rounded-full` with `secondary-container` backgrounds and `on-secondary-container` text for a soft, friendly "pill" look.

### Specific App Components
*   **The "Daily Ribbon":** A horizontal scrolling timeline for school activities using `tertiary-container` cards with `rounded-xl` corners.
*   **Status Indicators:** Instead of small dots, use "Soft Blobs"—small, organic, non-perfect circles in `error-container` or `secondary-container` to indicate attendance or payment status.

## 6. Do's and Don'ts

### Do:
*   **Use Intentional Asymmetry:** Align text to the left but allow imagery or "blobs" of color to break the grid on the right.
*   **Embrace Large Radii:** Use `rounded-xl` (3rem) for decorative elements to maintain the "child-centric" feel.
*   **Prioritize Breathing Room:** Give headers at least `48px` of bottom margin to let the editorial typography "sing."

### Don't:
*   **Don't use pure black:** Always use `on-surface` (#2c3437) for text to keep the interface soft.
*   **Don't use hard edges:** No `rounded-none` or `rounded-sm` unless it is for a very small utility icon.
*   **Don't crowd the data:** If a table feels cramped, move to a "Card List" format. Administrative work shouldn't feel like a chore; it should feel like browsing a high-end catalogue.