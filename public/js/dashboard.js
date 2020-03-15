jQuery(document).ready(function ($) {

    //===============================================

    // Extra Service add by selected services

    //===============================================

    $(".create-product-vendor-section").on("click",".ex_feat_add", function (e) {

        e.preventDefault();

        console.log('hello');


        var $last_count = $('.ex_feature_last_count');
        var $last_count_val = parseInt($last_count.val());

        $last_count_val++;

        $last_count.val($last_count_val);


        var fields = '<p><input type="text" name="extra_features[' + $last_count_val + '][text_field]" class=" time_field" > - <input type="text" name="extra_features[' + $last_count_val + '][number_field]" class="time_field" >' +
            '<a href="" class="button ex_feat_remove"><span class="dashicons dashicons-trash" style="margin-top: 3px;color: red;"></span>Remove</a></p>';

        $('.extra_feat_fields').append(fields);


    });

    //================

    // Remove extra service section specific row

    //================
    $(".ex_feat").on("click" , ".ex_feat_remove", function (e) {

        e.preventDefault();

        $(this).closest('p').remove();
    });



});
