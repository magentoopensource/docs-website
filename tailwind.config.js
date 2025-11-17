import defaultTheme from "tailwindcss/defaultTheme";
import colors from "tailwindcss/colors";

export default {
    content: ["resources/views/**/*.blade.php", "resources/js/**/*.js"],
    darkMode: "class",
    theme: {
        extend: {
            fontSize: {
                "6.5xl": ["4rem", { lineHeight: "1" }],
                "7xl": ["4.5rem", { lineHeight: "1" }],
                "8xl": ["6rem", { lineHeight: "1" }],
                // Figma design system sizes
                "6xl": ["60px", { lineHeight: "1" }],     // Type/6xl
                "5xl": ["48px", { lineHeight: "1" }],     // Type/5xl
                "3xl": ["30px", { lineHeight: "1.2" }],   // Type/3xl
                "xl": ["20px", { lineHeight: "1.4" }],    // Type/xl
                "lg": ["18px", { lineHeight: "1.42" }],   // Type/lg
                "base": ["16px", { lineHeight: "1.5" }],  // text-base
                "sm": ["14px", { lineHeight: "1.42" }],   // text-sm
                "xs": ["12px", { lineHeight: "1.333" }],  // text-xs
            },
            maxWidth: {
                xxs: "16rem",
                "8xl": "90rem", // 1440px - Standard large desktop viewport
                "9xl": "96rem",
            },
            spacing: {
                18: "4.5rem",
                22: "5.5rem",
                88: "22rem",
                128: "32rem",
                144: "36rem",
                224: "56rem",
            },
            width: {
                "logo-sm": "220px",  // Mobile logo width
                "logo-md": "240px",  // Tablet logo width
                "logo-lg": "270px",  // Desktop logo width
            },
            borderRadius: {
                "4xl": "2rem",
                "5xl": "2.5rem",
            },
            keyframes: {
                cube: {
                    "50%": { transform: "translateY(1rem)" },
                },
                "fade-in": {
                    "0%": { opacity: "0", transform: "translateY(10px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                "slide-in-right": {
                    "0%": { opacity: "0", transform: "translateX(20px)" },
                    "100%": { opacity: "1", transform: "translateX(0)" },
                },
            },
            animation: {
                cube: "cube 6s ease-in-out infinite",
                "fade-in": "fade-in 0.5s ease-out",
                "slide-in-right": "slide-in-right 0.3s ease-out",
            },
            transitionDuration: {
                "400": "400ms",
                "600": "600ms",
            },
        },
        boxShadow: {
            none: "0 0 0 0 rgba(0, 0, 0, 0)",
            sm: `0 10px 15px -8px rgba(9, 9, 16, .1)`,
            lg: "0 20px 30px -16px rgba(9, 9, 16, .2)",
            xl: "0 10px 20px 0 rgba(9, 9, 16, .15)",
        },
        colors: {
            transparent: "transparent",
            current: "currentColor",
            // Brand colors
            orange: {
                DEFAULT: "#F26423",
                500: "#F26423",  // For @apply directives
            },
            yellow: "#F1BC1B",
            red: {
                DEFAULT: "#BC3312",
                500: "#BC3312",  // For theme() function
                600: "#BC3312",  // For @apply directives
            },
            purple: "#9747FF",
            // Neutrals
            black: "#000000",
            white: "#FFFFFF",
            charcoal: "#2C2C2C",
            slate: "#34323A",
            brown: "#442204",
            "off-white": "#FAFAFA",
            silver: "#C9C9C9",
            // Dark mode colors (very dark backgrounds)
            dark: {
                500: "#171923",
                600: "#12141C",
            },
            // Gray scale with semantic names
            gray: {
                darkest: "#474747",
                darker: "#818181",
                medium: "#848484",
                light: "#CCCCCC",
                lighter: "#D9D9D9",
                lightest: "#F1F1F1",
                // Numbered shades for @apply directives
                50: "#FAFAFA",
                100: "#F1F1F1",
                200: "#D9D9D9",
                300: "#CCCCCC",
                400: "#848484",
                500: "#848484",
                600: "#818181",
                700: "#474747",
                800: "#34323A",
                900: "#2C2C2C",
            },
            // Minimal functional colors for callouts/alerts (keep minimal shades)
            blue: {
                50: "#eff6ff",
                400: "#60a5fa",
                600: "#2563eb",
                800: "#1e40af",
            },
            green: {
                50: "#f0fdf4",
                400: "#4ade80",
                600: "#16a34a",
                800: "#166534",
            },
            amber: {
                50: "#fffbeb",
                500: "#f59e0b",
            },
            // Legacy color names for @apply directives (mapped to our palette)
            primary: {
                50: "#FFF7F0",    // Very light orange
                100: "#FFE7D7",   // Light orange
                500: "#F26423",   // Main orange
                600: "#F26423",   // Orange
                700: "#E65D1F",   // Darker orange
                800: "#D9541B",   // Even darker orange
            },
            secondary: {
                500: "#BC3312",   // Red
                600: "#BC3312",   // Red
                700: "#A62D10",   // Darker red
            },
        },
        fontFamily: {
            sans: ["Inter Tight", ...defaultTheme.fontFamily.sans],
            mono: ["source-code-pro", "SF Mono", "Monaco", "Inconsolata", "Roboto Mono", ...defaultTheme.fontFamily.mono],
            display: ["Inter Tight", ...defaultTheme.fontFamily.sans],
            "inter-tight": ["Inter Tight", ...defaultTheme.fontFamily.sans], // Used in typography
        },
        letterSpacing: {
            tighter: "-0.05em",
            tight: "-0.025em",
            normal: "0",
            wide: "0.025em",
            wider: "0.05em",
            widest: "0.1em",
        },
        lineHeight: {
            3: ".75rem",
            4: "1rem",
            5: "1.25rem",
            6: "1.5rem",
            7: "1.75rem",
            8: "2rem",
            9: "2.25rem",
            10: "2.5rem",
            none: "1",
            tight: "1.25",
            snug: "1.375",
            normal: "1.5",
            relaxed: "1.625",
            loose: "2",
        },
    },
    plugins: [],
};
