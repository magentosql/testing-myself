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
    jQuery('.openPromoPopup').click();
}

jQuery(function ($) {
    $(".openPromoPopup").colorbox({
        inline: true,
        opacity: 0.5,
        speed: 300,
        width: 760,
        height: 470,
        closeButton: false,
        className: 'pageleave-colorbox'
    });

});
jQuery(document).on('cbox_open', function () {
    jQuery('body').css({overflow: 'hidden'});
    jQuery('body > .wrapper').addClass('blurred');
}).on('cbox_closed', function () {
    jQuery('body').css({overflow: ''});
    jQuery('body > .wrapper').removeClass('blurred');
});
