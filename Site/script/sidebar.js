document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggle-sidebar');
    const closeButton = document.getElementById('close-sidebar');
    const sidebar = document.getElementById('sidebar');
    const contentWrapper = document.getElementById('page-content-wrapper');

    toggleButton.addEventListener('click', function () {
        sidebar.classList.toggle('active');
        contentWrapper.classList.toggle('active');
    });

    closeButton.addEventListener('click', function () {
        sidebar.classList.remove('active');
        contentWrapper.classList.remove('active');
    });
});
