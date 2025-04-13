document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements
    const menuToggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const closeSidebar = document.getElementById('close-sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const body = document.body;
    
    // Toggle sidebar when clicking the hamburger menu
    menuToggle.addEventListener('click', function() {
        sidebar.classList.toggle('active');
        sidebarOverlay.classList.toggle('active');
        body.classList.toggle('sidebar-open');
    });
    
    // Close sidebar when clicking the X button
    closeSidebar.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        body.classList.remove('sidebar-open');
    });
    
    // Close sidebar when clicking the overlay
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
        body.classList.remove('sidebar-open');
    });
    
    // Handle submenu toggles
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('active');
        });
    });
    
    // Handle navigation via hash changes
    function handleHashChange() {
        const hash = window.location.hash;
        if (hash) {
            console.log(`Navigated to: ${hash}`);
            // You could load content based on the hash here
        }
    }
    
    window.addEventListener('hashchange', handleHashChange);
    handleHashChange();
    
    // Close sidebar on window resize if screen becomes small
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
            // Optional: automatically close sidebar on small screens when resizing
            // Uncomment the next lines if you want this behavior
            // sidebar.classList.remove('active');
            // sidebarOverlay.classList.remove('active');
            // body.classList.remove('sidebar-open');
        }
    });
    
    // Optional: Close sidebar when pressing ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('active')) {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
            body.classList.remove('sidebar-open');
        }
    });
    
    // Close sidebar when clicking a menu item (optional, good for mobile)
    const menuItems = document.querySelectorAll('.menu-item > a:not(.submenu-toggle)');
    menuItems.forEach(item => {
        item.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
                body.classList.remove('sidebar-open');
            }
        });
    });
});