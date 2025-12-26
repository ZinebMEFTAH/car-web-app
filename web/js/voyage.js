// voyage.js
//handle the notification in the main!
function notifMain(message, type) {
    var notif = $('#notif');
    var cssType;


    if (type === 'error') {
        cssType = 'alert notifDanger';
    } 
    
    else if (type === 'warn') {
        cssType = 'alert notifWarn';
    } else if (type === 'success') {
        cssType = 'alert goodNotif';
    } else {
        cssType = 'alert defaultNotif';
    }

    // if anytime a problem happens and the prev notif did not get deleted, we make sure they do nog collpse !
    notif.removeClass('notif-appear alert notifDanger notifWarn goodNotif defaultNotif');

    notif.addClass(cssType + ' notif-appear').text(message);

    // hide after a few seconds(better dynamic)
    setTimeout(function () {
        notif.removeClass('notif-appear alert notifDanger notifWarn goodNotif defaultNotif');
        notif.text('');
    }, 3000);
}

//AJAX Implementatino
function envoyerRechercheVoyage() {

    var searchForm = $( '#form-recherche-voyage' ) ;

    $.ajax({
        type: searchForm.attr( 'method'),
        url:searchForm.attr( 'action' ),
        data: searchForm.serialize() ,
        dataType:'json'

    }).done(function (request) { // if success:
        if (request.tableResult) { // if there are travels correspond
            $( '#table-result' ).html( request.tableResult ) ;

        }

        if (request.message) { // at the same time we update the notificaiton 
            notifMain(request.message , request.messageType);
        }

    }).fail(function () { 

        //if there is a problem :
        notifMain('Error while AJAX request' , 'error');

    });
}

$(function () {
    //prevent full reload
    $('#form-recherche-voyage').on('submit', function (e) {

        e.preventDefault() ;
        
        envoyerRechercheVoyage();
        return false;
    });

    // Réserver does nothing (no page yet)
    $(document).on('click', '.vc-reserve-btn', function (e) {
        e.preventDefault();
        
        return false;
    });
});


$(function () {
    // 1. Search Logic
    $('#form-recherche-voyage').on('submit', function (e) {
        e.preventDefault();
        envoyerRechercheVoyage();
        return false;
    });

    // 2. Open Modal Logic (Generic JS)
    $(document).on('click', '.btn-open-modal', function (e) {
        e.preventDefault();
        var btn = $(this);

        // Fill data
        $('#modal-trajet').text(btn.data('depart') + ' → ' + btn.data('arrivee'));
        $('#modal-prix').text(btn.data('prix').toFixed(2).replace('.', ',') + ' €'); // Format price
        $('#modal-places').text(btn.data('places') + ' personne(s)');
        $('#modal-heure').text(btn.data('heure'));

        // Fill inputs
        $('#input-voyage-id').val(btn.data('id'));
        $('#input-nb-places').val(btn.data('places'));

        // Show Modal (Fade In)
        $('#modal-reservation').fadeIn(200);
    });

    // 3. Close Modal Logic (Click X or Cancel)
    $(document).on('click', '#close-modal-x, #btn-cancel-modal', function () {
        $('#modal-reservation').fadeOut(200);
    });

    // 4. Close if clicking outside the box
    $(window).on('click', function (e) {
        if ($(e.target).is('#modal-reservation')) {
            $('#modal-reservation').fadeOut(200);
        }
    });
});