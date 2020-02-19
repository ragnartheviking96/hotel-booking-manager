(function ($) {
    'use strict';

    $('.adult-child-input').hide();
    $(document).ready(function () {
        $('.room-image-gallery').magnificPopup({
            delegate: 'a',
            type: 'image',
            tLoading: 'Loading image #%curr%...',
            mainClass: 'mfp-img-mobile',
            gallery: {
                enabled: true,
                navigateByImgClick: true,
                preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
            },
            image: {
                tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
                titleSrc: function (item) {
                    return item.el.attr('title') + '<small>by Mage People</small>';
                }
            }
        });

        $(".submit-to-cart").attr("disabled", true);
        var hotelName = 0;

        var total_stays = $("#total_day_stay").val();
        //console.log(total_stays);

        $(".room-quantity-number").change(function () {
            var subtotal = 0;
            var rooms = 0;
            //$('#booked-room').append('<tr>' + '<td class="booked-room-name"></td>' + '<td
            // class="booked-room-quantity"></td>' + '</tr>');
            /*$('.whbmt_single_room_preview').each(function () {
                hotelName = $(this).find(".room-heading").html();
            });
            $('.booked-room-name').html(hotelName);*/

            $(".room-quantity-number").each(function () {
                rooms += +$(this).val();
            });
            $('.booked-room-quantity').html(rooms);
            //console.log(rooms);
            if (rooms !== 0 && total_stays !=0) {
                $(".submit-to-cart").attr("disabled", false);
            } else {
                $(".submit-to-cart").attr("disabled", true);
            }


            $('#total-room').html(rooms);
            var room_price = 0;

            $('.whbmt_single_room_preview').each(function () {
                var quantity = $(this).find(".room-quantity-number").val();
                var price = $(this).find(".room-price").html();
                room_price += quantity * price;
                subtotal = room_price * total_stays;

            });

            $('#final_price').val(subtotal);
            $('#total_price').html(total_stays + " days X " + room_price + " = " + subtotal);
        });


        //fotorama Jquery Plugin for photo gallery slider
        $('.room-image-gallery').fotorama({
            allowfullscreen: true,
            autoplay: "1000",
            loop: "true"
        });

        //Date Ranger For Hotel Check in / out date
        var today = new Date();
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
        var yyyy = today.getFullYear();
        today = yyyy + '/' + mm + '/' + dd;

        $(".custom_whbm, #daterange").dateRangePicker({
            language: 'en',
            monthSelect: true,
            yearSelect: true,
            startDate: today,
            minDays: 1,
        });

        $('#qty-section').click(function () {
            $('.adult-child-input').toggle();
        });
        $("#child-qty-input").change(function () {
            $('.child-qty').html($(this).val());
        });
        $("#adult-qty-input").change(function () {
            $('.adult-qty').html($(this).val());
        });

        $('#dest-name').autocomplete({
            source: whbm_autocomplete,
            minLength: 1
        });

        // $(".col-md-7 .tab-content .tab-pane").first().addClass("active");
        $(".col-md-12 .tab-content .tab-pane").first().addClass("active");

        //    sticky menu

        $('.mage_hotel_type, .mage_hotel_cat, .room-price-range').change(function (e) {
            e.preventDefault();
            jQuery('.newly_added_ajax').empty();
            var mage_hotel_type = [];
            $('.mage_hotel_type:checked').each(function(i){
                mage_hotel_type[i] = $(this).val();
            });

            var mage_hotel_cat = [];
            $('.mage_hotel_cat:checked').each(function(i){
                mage_hotel_cat[i] = $(this).val();
            });
            var hotel_room_price_range = [];
           $('.room-price-range:checked').each(function (i) {
               hotel_room_price_range[i] = $(this).val().split('to');
           });

            var hotel_data = {
                action: "ajax_whbm_hotel_list",
                hotel_type: mage_hotel_type,
                hotel_facilities: mage_hotel_cat
            };
            jQuery.ajax({
                url: whbm_ajax_object,
                data: hotel_data,
                success: function (response) {
                    //console.log(response);
                    $(".tab-pane .whbmt_single_package").empty();
                    jQuery('.newly_added_ajax').append(response.posts);
                },
                error: function(response) {
                    var successmessage = 'Error';
                    $(".newly_added_ajax").text(successmessage);
                },
            })
        });

        $('.destination_search').select2();
    });

})(jQuery);
