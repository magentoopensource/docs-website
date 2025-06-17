<script>
    function updateTheme() {
        // Force light mode only
        localStorage.theme = 'light';
        document.documentElement.classList.remove('dark');
        document.documentElement.setAttribute('color-theme', 'light');
        document.documentElement.setAttribute('data-theme', 'light');

        // Set light theme colors
        document.querySelector('meta[name="color-scheme"]').setAttribute('content', 'light');
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#ffffff');
    }

    updateTheme();
</script>
