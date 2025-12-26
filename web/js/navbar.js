(function () {
    var toggle = document.querySelector('.navbar-toggle');
    var navbarMain = document.querySelector('.navbar-main');
    var navLinks = document.querySelectorAll('#navbar .nav-link');

    if (!toggle || !navbarMain) return;

    toggle.addEventListener('click', function () {
        navbarMain.classList.toggle('nav-open');
        toggle.classList.toggle('open');
    });

    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            navbarMain.classList.remove('nav-open');
            toggle.classList.remove('open');
        });
    });
})();