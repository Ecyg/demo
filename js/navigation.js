
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
                { label: "Reflected XSS", url: "reflected_xss.php" },
                { label : "Reflected XSS (more)", url: "reflected_xss2.php" },
                { label : "OS Command Injection", "url" : "os_command_injection.php" }
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
            
            const submenuWrapper = document.createElement('div');
            submenuWrapper.className = 'submenu-wrapper';
            
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
            
            submenuWrapper.appendChild(submenu);
            li.appendChild(submenuWrapper);
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
            const submenuWrapper = this.nextElementSibling;
            submenuWrapper.classList.toggle('active');
            
            // Check if submenu needs scrollbar
            const submenu = submenuWrapper.querySelector('.submenu');
            if (submenu) {
                checkSubmenuOverflow(submenuWrapper, submenu);
            }
        });
    });
    
    // Add window resize event to adjust submenu heights when window size changes
    window.addEventListener('resize', function() {
        const activeSubmenuWrappers = document.querySelectorAll('.submenu-wrapper.active');
        activeSubmenuWrappers.forEach(wrapper => {
            const submenu = wrapper.querySelector('.submenu');
            if (submenu) {
                checkSubmenuOverflow(wrapper, submenu);
            }
        });
    });
}

// Function to check if submenu needs scrolling and adjust its height
function checkSubmenuOverflow(wrapper, submenu) {
    // Reset any previous max-height to properly calculate the full height
    submenu.style.maxHeight = 'none';
    
    const submenuHeight = submenu.offsetHeight;
    const viewportHeight = window.innerHeight;
    const submenuRect = submenu.getBoundingClientRect();
    const topPosition = submenuRect.top;
    
    // Calculate how much space we have from the top of the submenu to the bottom of the viewport
    const availableHeight = viewportHeight - topPosition - 20; // 20px buffer
    
    // If submenu is taller than available space, make it scrollable
    if (submenuHeight > availableHeight) {
        wrapper.classList.add('scrollable');
        submenu.style.maxHeight = availableHeight + 'px';
    } else {
        wrapper.classList.remove('scrollable');
        submenu.style.maxHeight = 'none';
    }
}

// Execute once DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    buildNavigation();
    
    // Add CSS for scrollable submenus
    const style = document.createElement('style');
    style.textContent = `
        .submenu-wrapper {
            display: none;
            position: relative;
        }
        
        .submenu-wrapper.active {
            display: block;
        }
        
        .submenu-wrapper.scrollable .submenu {
            overflow-y: auto;
        }
        
        .submenu-wrapper.scrollable:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(to top, rgba(255,255,255,0.8), transparent);
            pointer-events: none;
        }
        
        /* Custom scrollbar styling for better UX */
        .submenu-wrapper.scrollable .submenu::-webkit-scrollbar {
            width: 6px;
        }
        
        .submenu-wrapper.scrollable .submenu::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        
        .submenu-wrapper.scrollable .submenu::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        
        .submenu-wrapper.scrollable .submenu::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.3);
        }
    `;
    document.head.appendChild(style);
});