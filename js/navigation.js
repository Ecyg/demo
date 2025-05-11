// Define all navigation items in a single configuration object
const siteNavigation = {
    mainItems: [
        {
            label: "Dashboard",
            url: "template.php",
            submenu: null
        },
        {
            label: "Vulnerabilities",
            url: "#",
            submenu: [
                { label: "Mass Assignment", url: "mass_assignment.php" },
                { label: "XSS via HTTP Parameter Pollution", url: "hpp.php" },
                { label: "Type Juggling", url: "type_juggling.php" },
                { label: "Angular XSS", url: "angular_xss.php" },
                { label: "HTTP Request Smuggling", url: "request_smuggling.php" },
                { label: "Reflected XSS", url: "reflected_xss.php" }

            ]
        },
        {
            label: "Bug Reports",
            url: "#",
            submenu: [
                { label: "TBA", url: "services-service1.php" },
                { label: "TBA", url: "services-service2.php" }
            ]
        },
        {
            label: "About Us",
            url: "about.php",
            submenu: null
        },
        {
            label: "Contact",
            url: "contact.php",
            submenu: null
        },
        {
            label: "Settings",
            url: "settings.php",
            submenu: null
        }
    ]
};

// Function to build the navigation menu
function buildNavigation() {
    const menuContainer = document.querySelector('.menu-items');
    if (!menuContainer) return;

    // Clear any existing items
    menuContainer.innerHTML = '';

    // Build menu items based on the configuration
    siteNavigation.mainItems.forEach(item => {
        const li = document.createElement('li');
        li.className = item.submenu ? 'menu-item has-submenu' : 'menu-item';
        
        const a = document.createElement('a');
        a.href = item.url;
        a.textContent = item.label;
        
        if (item.submenu) {
            a.className = 'submenu-toggle';
            li.appendChild(a);
            
            const submenu = document.createElement('ul');
            submenu.className = 'submenu';
            
            item.submenu.forEach(subitem => {
                const subli = document.createElement('li');
                subli.className = 'submenu-item';
                
                const suba = document.createElement('a');
                suba.href = subitem.url;
                suba.textContent = subitem.label;
                
                subli.appendChild(suba);
                submenu.appendChild(subli);
            });
            
            li.appendChild(submenu);
        } else {
            li.appendChild(a);
        }
        
        menuContainer.appendChild(li);
    });

    // Re-attach event listeners for submenu toggles
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            submenu.classList.toggle('active');
        });
    });
}

// Execute once DOM is loaded
document.addEventListener('DOMContentLoaded', buildNavigation);