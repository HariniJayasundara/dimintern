// sidebar.js

// Function to set the active page in the sidebar
function setActivePage() {
    const currentURL = window.location.href;
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    sidebarLinks.forEach(link => {
        if (currentURL.includes(link.getAttribute('href'))) {
            link.parentElement.classList.add('active'); // Add 'active' class to the parent li element
        } else {
            link.parentElement.classList.remove('active'); // Remove 'active' class if not on this page
        }
    });
}

// Add an event listener for DOMContentLoaded to call the setActivePage function
document.addEventListener('DOMContentLoaded', function () {
    setActivePage();
});
