/**
 * voyage.js - Main AJAX Handler & Notifications
 */


// ** NOTIFICATIONS

function notifMain(message, type) {
    var notif = $('#notif');
    var cssType = 'alert defaultNotif';

    // choose the color based on the type (red for error, green for success)
    if (type === 'error' || type === 'danger') cssType = 'alert notifDanger';
    else if (type === 'warn' || type === 'warning') cssType = 'alert notifWarn';
    else if (type === 'success') cssType = 'alert goodNotif';

    // show the notification
    notif.removeClass('notif-appear alert notifDanger notifWarn goodNotif defaultNotif');
    notif.addClass(cssType + ' notif-appear').html(message); 

    // make it disappear automatically after 3 seconds
    setTimeout(function () {
        notif.removeClass('notif-appear alert notifDanger notifWarn goodNotif defaultNotif');
        notif.empty();
    }, 3000);
}

// ** CENTRALIZED RESPONSE HANDLER

function handleResponse(res, url) {
    
    // Case 1: The server wants to redirect us (e.g. after login)
    if (res.redirect) {
        if (res.message) {
            // we save the message in the browser memory so we can show it AFTER the page reloads
            localStorage.setItem('voyage_flash_message', JSON.stringify({
                message: res.message,
                type: res.messageType || 'success'
            }));
        }
        window.location.href = res.redirect;
        return;
    }

    // Case 2: Standard Page Update AJAX
    if (res.html) {
        $('#content-container').html(res.html);
        // change the url in the address bar so it looks like a real navigation
        if (url) window.history.pushState({path: url}, '', url);
        window.scrollTo(0, 0);
    }

    // Case 3: Partial Update (Just the search results table)
    if (res.tableResult) {
        $('#table-result').html(res.tableResult);
    }

    // if the server sent a notification message, show it
    if (res.message) {
        notifMain(res.message, res.messageType);
    }

    // always close the reservation modal if it was open
    $('#modal-reservation').fadeOut(200);
}

// **DOM EVENTS

$(document).ready(function() {

    // --- CHECK FOR SAVED NOTIFICATIONS ---
    // check if we have a message saved from the previous page (like "Login Success")
    var storedFlash = localStorage.getItem('voyage_flash_message');
    if (storedFlash) {
        var data = JSON.parse(storedFlash);
        notifMain(data.message, data.type);
        localStorage.removeItem('voyage_flash_message'); // clean up
    }

    // --- INTERCEPT LINKS ---
    // we catch all clicks on links to load them via ajax instead of reloading the page
    $(document).on('click', 'a', function (e) {
        var url = $(this).attr('href');
        
        // ignore empty links, javascript links, or external tabs
        if (!url || url === '#' || url.indexOf('javascript:') !== -1) return;
        if ($(this).attr('target') === '_blank') return;
        if ($(this).data('method')) return; // let Yii handle special buttons like Delete

        e.preventDefault();
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(res) { handleResponse(res, url); },
            error: function() { notifMain('Erreur de chargement', 'error'); }
        });
    });

    // --- INTERCEPT FORMS ---
    // same thing for forms: submit via ajax (login, search, create)
    $(document).on('submit', 'form', function (e) {
        e.preventDefault();
        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            url: url,
            type: form.attr('method'),
            data: form.serialize(),
            dataType: 'json',
            success: function (res) {
                var newUrl = null;
                // update URL only if it's a GET search
                if (form.attr('method').toLowerCase() === 'get' && !res.tableResult) {
                     newUrl = url + '?' + form.serialize();
                }
                handleResponse(res, newUrl);
            },
            error: function () {
                notifMain('Erreur technique (AJAX)', 'error');
            }
        });
    });

    // --- BROWSER BACK/FORWARD ---
    // handle what happens when the user clicks the browser's "Back" button
    window.onpopstate = function (event) {
        if (event.state) {
            // if we have a saved state, we reload just the content via ajax
            $.get(window.location.href, function(res) {
                 if(res.html) $('#content-container').html(res.html);
            }, 'json');
        } else {
            // otherwise force a real page reload
            window.location.reload();
        }
    };
});