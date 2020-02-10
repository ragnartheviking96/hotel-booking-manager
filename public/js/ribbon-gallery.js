(function ($) {
    'use strict';
    $(document).ready(function () {

        $(".ribbon-gallery img").click(function () {
            var img = $(this);
            var src = img.attr('src');
            $("body").append("<div class='substrate'>" +
                "<div class='substrate-bg'></div>" +
                "<img src=" + src + " class='substrate-img' />" +
                "</div>");
            $(".substrate").fadeIn(300);
            $(".substrate-bg,.substrate-img").click(function () {
                $(".substrate").fadeOut(300);
                setTimeout(function () {
                    $(".substrate").remove();
                }, 300);
            });
        });

    });
})(jQuery);
