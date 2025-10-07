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
                "6xl": ["60px", { lineHeight: "1" }],     // Type/6xl from Figma
                "5xl": ["48px", { lineHeight: "1" }],     // Type/5xl from Figma  
                "3xl": ["30px", { lineHeight: "1.2" }],   // Type/3xl from Figma
                "xl": ["20px", { lineHeight: "1.4" }],    // Type/xl from Figma
                "lg": ["18px", { lineHeight: "1.42" }],   // Type/lg from Figma
                "base": ["16px", { lineHeight: "1.5" }],  // text-base from Figma
                "sm": ["14px", { lineHeight: "1.42" }],   // text-sm from Figma
                "xs": ["12px", { lineHeight: "1.333" }],  // text-xs from Figma
            },
            maxWidth: {
                xxs: "16rem",
                "8xl": "88rem",
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
            black: "#000",
            white: "#fff",
            // Primary brand colors (Flamingo from Figma)
            primary: {
                50: "#fef7f0",
                100: "#fef1e7",
                200: "#faccae", // Flamingo/200 from Figma
                300: "#fdba74", 
                400: "#f27945", // Flamingo/400 from Figma
                500: "#ff6500", // Keep existing for compatibility
                600: "#e85d04",
                700: "#d9480f",
                800: "#8a2e13",
                900: "#73230d",
            },
            // Frame 53 specific colors
            waterloo: {
                500: "#7b7792", // Waterloo/500 from Figma
                600: "#6f6a7f", // Waterloo/600 from Figma
                800: "#504e56", // Waterloo/800 from Figma
                900: "#434246", // Waterloo/900 from Figma
                950: "#27262a", // Waterloo/950 from Figma
            },
            blackrock: {
                900: "#05021e", // Black Rock/900 from Figma
            },
            flamingo: {
                50: "#fef5ee",   // Flamingo/50 from Figma
                100: "#fde7d7",  // Flamingo/100 from Figma
                200: "#faccae",  // Flamingo/200 from Figma
                300: "#f5a77a",  // Medium light flamingo 
                400: "#f27945",  // Flamingo/400 from Figma
                500: "#ef5924",  // Flamingo/500 from Figma
                600: "#d14d1c",  // Much darker flamingo
                700: "#b8451a",  // Even darker flamingo
                800: "#973617",  // Very dark flamingo
                900: "#7f2814",  // Darkest flamingo
            },
            secondary: {
                50: "#fee2e2",
                100: "#fecaca",
                200: "#fca5a5",
                300: "#f87171",
                400: "#EB4432",
                500: "#F9322C",
                600: "#ec0e00", // Main secondary red
                700: "#ca473f",
                800: "#981d15",
                900: "#7f1d1d",
            },
            gray: {
                900: "#232323",
                800: "#222222",
                700: "#565454",
                600: "#777777",
                500: "#93939e",
                400: "#B5B5BD",
                300: "#d7d7dc",
                200: "#e7e8f2",
                100: "#f5f5fa",
                50: "#fbfbfd",
            },
            dark: {
                900: "#0C0D12",
                800: "#12141C",
                700: "#171923",
                600: "#252A37",
                500: "#394056",
            },
            red: {
                50: "#fef2f2",
                100: "#fee2e2",
                200: "#fecaca",
                300: "#fca5a5",
                400: "#f87171",
                500: "#ef4444",
                600: "#dc2626",
                700: "#b91c1c",
                800: "#991b1b",
                900: "#7f1d1d",
            },
            orange: {
                50: "#fff7ed",
                100: "#ffedd5",
                200: "#fed7aa",
                300: "#fdba74",
                400: "#fb923c",
                500: "#ff6500", // Keep for backward compatibility
                600: "#e85d04",
                700: "#d9480f",
                800: "#8a2e13",
                900: "#73230d",
            },
            blue: {
                50: "#eff6ff",
                100: "#dbeafe", 
                200: "#bfdbfe",
                300: "#93c5fd",
                400: "#60a5fa",
                500: "#3b82f6",
                600: "#2563eb",
                700: "#1d4ed8",
                800: "#1e40af",
                900: "#1e3a8a",
            },
            green: {
                50: "#f0fdf4",
                100: "#dcfce7",
                200: "#bbf7d0", 
                300: "#86efac",
                400: "#4ade80",
                500: "#22c55e",
                600: "#16a34a",
                700: "#15803d",
                800: "#166534",
                900: "#14532d",
            },
            yellow: {
                50: "#fefce8",
                100: "#fef3c7",
                200: "#fde68a",
                300: "#fcd34d", 
                400: "#fbbf24",
                500: "#f59e0b",
                600: "#d97706",
                700: "#b45309",
                800: "#92400e",
                900: "#78350f",
            },
            purple: {
                600: "#8338ec",
            },
            vapor: "#25c4f2",
            forge: "#1EB786",
            envoyer: "#F56857",
            horizon: "#8C6ED3",
            nova: "#4099DE",
            echo: "#4AB2B0",
            lumen: "#F6AE7A",
            homestead: "#E7801C",
            spark: "#9B8BFB",
            valet: "#5E47CD",
            mix: "#294BA5",
            cashier: "#91D630",
            dusk: "#BB358B",
            passport: "#7DD9F2",
            scout: "#F55D5C",
            socialite: "#E394BA",
            telescope: "#4040C8",
            tinker: "#EC7658",
            jetstream: "#6875f5",
            sail: "#38BDF7",
            sanctum: "#1D5873",
            octane: "#CA3A31",
            breeze: "#F3C14B",
            pint: "#ffd000",
            // Figma Design System Colors
            "mine-shaft": {
                50: "#f1f1f1",   // Mine Shaft/50 from Figma
                100: "#d9d9d9",  // Mine Shaft/100 from Figma
                300: "#818181",  // Mine Shaft/300 from Figma (secondary text)
                400: "#474747",  // Mine Shaft/400 from Figma
                500: "#2c2c2c",  // Mine Shaft/500 from Figma (primary text)
            },
            "alabaster": {
                500: "#fafafa",  // Alabaster/500 from Figma (background)
                600: "#cccccc",  // Alabaster/600 from Figma (borders)
            },
            "figma-orange": {
                500: "#f26423",  // Orange/500 from Figma (accent)
                700: "#bc3312",  // Orange/700 from Figma (links)
            },
            "lightning-yellow": {
                500: "#f1bc1b",  // Lightning Yellow/500 from Figma (highlights)
                950: "#442204",  // Lightning Yellow/950 from Figma (text on yellow)
            },
            // Lightning Yellow palette from Figma
            lightningYellow: {
                50: "#fffef7",
                100: "#fffaeb",
                200: "#fff2cc",
                300: "#fff147", // Lightning Yellow/300 from Figma
                400: "#ffd633",
                500: "#f2bc02", // Lightning Yellow/500 from Figma
                600: "#e09900", // Lightning Yellow/600 from Figma
                700: "#cc9900",
                800: "#b37d00",
                900: "#7c450b", // Lightning Yellow/900 from Figma
            },
            // Sweet Corn palette from Figma
            sweetCorn: {
                50: "#fef5ee", // Flamingo/50 from Figma for bg
                100: "#fde7d7", // Flamingo/100 from Figma
                200: "#fff2cc",
                300: "#ffe599",
                400: "#ffdc66", // Sweet Corn/400 from Figma
                500: "#ffd333",
                600: "#e6b800",
                700: "#cc9900",
                800: "#b37d00",
                900: "#996100",
                950: "#6c3d08", // Sweet Corn/950 from Figma
            },
        },
        fontFamily: {
            sans: ["Inter Tight", ...defaultTheme.fontFamily.sans], // Primary Figma typeface
            mono: ["source-code-pro", "SF Mono", "Monaco", "Inconsolata", "Roboto Mono", ...defaultTheme.fontFamily.mono],
            display: ["Inter Tight", ...defaultTheme.fontFamily.sans], // Figma typeface
            "inter-tight": ["Inter Tight", ...defaultTheme.fontFamily.sans], // Explicit Figma font
            "neue-haas": ["Neue Haas Grotesk Text Pro", ...defaultTheme.fontFamily.sans], // Figma body typeface
            alegreya: ["Alegreya Sans", ...defaultTheme.fontFamily.sans], // Frame 53 font
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
