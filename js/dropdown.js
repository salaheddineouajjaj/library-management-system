// Dropdown Menu Handler
document.addEventListener('DOMContentLoaded', function () {
    // Handle all dropdowns
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');

        if (!toggle || !menu) return;

        // Position dropdown on click/hover
        function positionDropdown() {
            const rect = toggle.getBoundingClientRect();
            menu.style.top = (rect.bottom + window.scrollY + 5) + 'px';
            menu.style.left = (rect.right - menu.offsetWidth + window.scrollX) + 'px';
        }

        // Click handler
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Close other dropdowns
            document.querySelectorAll('.dropdown.show').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });

            // Toggle this dropdown
            dropdown.classList.toggle('show');
            if (dropdown.classList.contains('show')) {
                positionDropdown();
            }
        });

        // Hover handler
        dropdown.addEventListener('mouseenter', function () {
            positionDropdown();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown.show').forEach(d => {
                d.classList.remove('show');
            });
        }
    });
});
