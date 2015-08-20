var topCursor = false;
var wasAlreadyDisplayed = docCookies.hasItem('leaving_page_displayed') == 1;
$(window).mouseleave(function (e) {
    if (e.pageY < 5) {
        topCursor = true;
    }
});
$(window).mouseenter(function (e) {
    topCursor = false;
});

jQuery(window).blur(function(e) {
    if (topCursor == true) {
        invokePromoModal();
    }
});

function invokePromoModal() {
    if (!wasAlreadyDisplayed && jQuery('.openPromoPopup').length > 0) {
        jQuery('.openPromoPopup').click();
        wasAlreadyDisplayed = true;
        remeberCustomerAction();
    }
}

jQuery(function ($) {
    $(".openPromoPopup").colorbox({
        inline: true,
        opacity: 0.5,
        speed: 300,
        width: 760,
        height: 470,
        closeButton: false,
        className: 'pageleave-colorbox',
        onCleanup: function() {
            jQuery('body').css({overflow: ''});
            jQuery('body > .wrapper').removeClass('blurred');

        },
        onComplete: function() {
            jQuery('body').css({overflow: 'hidden'});
            jQuery('body > .wrapper').addClass('blurred');
            jQuery('#mc-embedded-subscribe-modal').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                var form = jQuery('#mc-embedded-subscribe-form-modal');
                jQuery.get('/leavingpage', form.serialize(), function(response) {
                    if (response.success == true) {
                        jQuery('#page_leave_modal').addClass('redeem');
                        jQuery('#page_leave_modal').html(response.content);
                        jQuery('#stay-on-page').on('click', function(e) {
                            jQuery.colorbox.close();
                            if(window.ga) {
                                ga('send', 'event', 'button', 'click', 'exit popup');
                            }
                            window.location.href = $(this).data('href');
                        });
                    }
                    else {
                        alert("An error occurred: \n" + response.message);
                    }
                }, 'json');
            });
            jQuery('#no-redeem').on('click', function(e) {
                jQuery.colorbox.close();
            });
        }
    });
});

function remeberCustomerAction() {
    var date = new Date();
    date.setTime(date.getTime()+(365*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
    docCookies.setItem('leaving_page_displayed', 1, expires, '/');
}
