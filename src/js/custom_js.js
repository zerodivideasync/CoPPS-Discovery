$(document).ready(function () {
    $(window).scroll(function () {
        if ($(window).scrollTop() > 50) {
            $(".navbar").addClass("index-navbar-fixed");
        } else {
            $(".navbar").removeClass("index-navbar-fixed");
        }
    });

    $(".navbar a,a.btn").on('click', function (event) {
        var hash = this.hash;
        if (hash) {
            event.preventDefault();
            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 600, function () {
                window.location.hash = hash;
            });
        }
    });

    alert = function (message) {
        $("<div></div>").dialog({
            buttons: {"Close": function () {
                    $(this).dialog("close");
                }},
            close: function (event, ui) {
                $(this).remove();
            },
            closeOnEscape: false,
            open: function(event, ui) {
                $(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            },
            resizable: false,
            title: 'Warning!',
            modal: true
        }).text(message);
    }
});