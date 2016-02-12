/* CUSTOM SCROLLBAR */
if (jQuery(window).width() > 1024) {
    jQuery(document).ready(function () {
        "use strict";
        jQuery(".dv-panel").mCustomScrollbar({
            scrollInertia: 500,
            autoHideScrollbar: true,
            theme: "light",
            advanced: {
                updateOnContentResize: true
            }
        });
    });
}

/* REMOVE HOVER EFFECT ON TOUCH DEVICES */
jQuery(document).ready(function () {
    if ("ontouchstart" in document.documentElement) {
        jQuery('.dv-member-name').addClass('rmveffect');
        jQuery('.dv-member-info').addClass('rmveffect');
        jQuery('.dv-member-desc').addClass('rmveffect');
        jQuery('img').addClass('rmveffect');
        jQuery('.dv-member-zoom').addClass('rmveffect');
    } else {
        /* CUSTOM ZOOM ANIMATION */
        jQuery(".dvteamgrid figure").hover(
            function () {
                "use strict";
                jQuery(this).find('.dv-member-zoom').removeClass('dv-zoomout');
                jQuery(this).find('.dv-member-zoom').addClass('dv-zoomin');
            }, function () {
                "use strict";
                jQuery(this).find('.dv-member-zoom').removeClass('dv-zoomin');
                jQuery(this).find('.dv-member-zoom').addClass('dv-zoomout');
            }
        );
    }
});

/* SKILLS */
jQuery('.dvskillbar').each(function () {
    jQuery(this).find('.dvskillbar-bar').css('width', jQuery(this).attr('data-percent'));
});

/* POPUP WINDOWS */
jQuery(document).ready(function() {
    "use strict";
    jQuery('.popup-with-zoom-anim').magnificPopup({
        type: 'inline',
        fixedContentPos: false,
        fixedBgPos: true,
        overflowY: 'auto',
        closeBtnInside: true,
        preloader: false,
        midClick: true,
        removalDelay: 300,
        mainClass: 'my-mfp-zoom-in'
    });
});