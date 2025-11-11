document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const content = document.querySelector('.content-with-sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    // Toggle menu on button click
    menuToggle.addEventListener('click', function() {
        menuToggle.classList.toggle('active');
        sidebar.classList.toggle('active');
        content.classList.toggle('sidebar-active');
        if (overlay) {
            overlay.classList.toggle('active');
        }
    });

    // Close sidebar when clicking overlay (mobile)
    if (overlay) {
        overlay.addEventListener('click', function() {
            menuToggle.classList.remove('active');
            sidebar.classList.remove('active');
            content.classList.remove('sidebar-active');
            overlay.classList.remove('active');
        });
    }

    // Close sidebar on window resize if in mobile view
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768 && sidebar.classList.contains('active')) {
            menuToggle.classList.remove('active');
            sidebar.classList.remove('active');
            content.classList.remove('sidebar-active');
            if (overlay) {
                overlay.classList.remove('active');
            }
        }
    });
}); 