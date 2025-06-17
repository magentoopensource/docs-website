export const toDarkMode = () => {
    // Dark mode disabled - force light mode
    localStorage.theme = "light";
    window.updateTheme();
};

export const toLightMode = () => {
    // Always use light mode
    localStorage.theme = "light";
    window.updateTheme();
};

export const toSystemMode = () => {
    // System mode disabled - force light mode
    localStorage.theme = "light";
    window.updateTheme();
};
