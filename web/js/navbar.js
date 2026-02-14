(function () {
    var toggle = document.querySelector('.navbar-toggle');
    var navbarMain = document.querySelector('.navbar-main');
    var navLinks = document.querySelectorAll('#navbar .nav-link');

    // safety check to avoid errors if elements don't exist
    if (!toggle || !navbarMain) return;

    // open/close mobile menu
    toggle.addEventListener('click', function () {
        navbarMain.classList.toggle('nav-open');
        toggle.classList.toggle('open');
    });

    // close the menu automatically when a link is clicked
    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            navbarMain.classList.remove('nav-open');
            toggle.classList.remove('open');
        });
    });
})();


$(document).ready(function() {
    
    // when we click the "Réserver" button
    $(document).on('click', '.btn-open-modal', function (e) {
        e.preventDefault();
        var btn = $(this); // get the button I just clicked

        // fill the text in the modal so the user sees the info (Visual)
        $('#modal-trajet').text(btn.data('depart') + ' → ' + btn.data('arrivee'));
        $('#modal-prix').text(btn.data('prix').toFixed(2).replace('.', ',') + ' €');
        $('#modal-places').text(btn.data('places') + ' personne(s)');
        $('#modal-heure').text(btn.data('heure'));

        // put the ID in the hidden input so the form knows exactly which trip to book
        $('#input-voyage-id').val(btn.data('id'));
        $('#input-nb-places').val(btn.data('places'));

        // show the modal
        $('#modal-reservation').fadeIn(200);
    });

    // close the modal if I click the X or the Cancel button
    $(document).on('click', '#close-modal-x, #btn-cancel-modal', function () {
        $('#modal-reservation').fadeOut(200);
    });

    // close if I click on the background (outside the box)
    $(window).on('click', function (e) {
        if ($(e.target).is('#modal-reservation')) {
            $('#modal-reservation').fadeOut(200);
        }
    });
    
});