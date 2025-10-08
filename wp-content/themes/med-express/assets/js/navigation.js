(function () {
    const siteNavigation = document.getElementById('site-navigation');
    const menuToggle = document.querySelector('.menu-toggle');

    if (!siteNavigation || !menuToggle) {
        return;
    }

    menuToggle.addEventListener('click', function () {
        const expanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', (!expanded).toString());
        siteNavigation.classList.toggle('is-open');
    });
})();
