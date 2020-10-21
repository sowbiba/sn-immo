/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';


const $ = require('jquery');
require('bootstrap');

$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $(this).toggleClass('active');
    });

    window.setTimeout(function() {
        $(".alert-dismissible").fadeOut(1000);
    }, 3000);

    $('.toggle-button').on('click', function() {
       const target = $(this).data('target');

       $(`#${target}`).toggle('slow');
    });

    $('#confirm-delete').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').on('click', function() {
            $.ajax(
                $(e.relatedTarget).data('href'),
                {
                    method: 'DELETE'
                }
            )
            .done(function( data ) {
                if ( console && console.log ) {
                    console.log(data);
                }
                window.location.reload();
            });
        });
        $(this).find('.btn-cancel').trigger('focus');
    });
});
