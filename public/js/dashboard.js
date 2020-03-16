jQuery(document).ready(function ($) {

    //===============================================

    // Extra Service add by selected services

    //===============================================

    $(".create-product-vendor-section").on("click", ".ex_feat_add", function (e) {

        e.preventDefault();

        var $last_count = $('.ex_feature_last_count');
        var $last_count_val = parseInt($last_count.val());

        $last_count_val++;

        $last_count.val($last_count_val);


        var fields = '<p><label for="extra_features_number">Extra Feature Name</label><input type="text"' +
            ' id="extra_features_number" name="extra_features[' + $last_count_val + '][text_field]" class="' +
            ' time_field" > - <label for="extra_features_number">Extra Feature Price</label><input type="number"' +
            ' id="extra_features_number" name="extra_features[' + $last_count_val + '][number_field]"' +
            ' class="time_field" >' +
            '<a href="" class="button ex_feat_remove"><span class="dashicons dashicons-trash" style="margin-top: 3px;color: red;"></span>Remove</a></p>';

        $('.extra_feat_fields').append(fields);


    });

    //================

    // Remove extra service section specific row

    //================
    // add extra feature section
    $(".ex_feat").on("click", ".ex_feat_remove", function (e) {
        e.preventDefault();
        $(this).closest('p').remove();
    });


    $(".create-product-vendor-section").on("click", ".faq_add", function (e) {

        e.preventDefault();


        var $faq_last_count = $('.faq_last_count');
        var $faq_last_count_val = parseInt($faq_last_count.val());

        $faq_last_count_val++;

        $faq_last_count.val($faq_last_count_val);


        var fields = '<p><label for="hotel_faq_number">FAQ Title</label><input type="text"' +
            ' id="hotel_faq_number" name="hotel_faq[' + $faq_last_count_val + '][text_field]" class="' +
            ' faq_text_field" > - <label for="hotel_faq_number">Possible Answer</label><input type="text"' +
            ' id="hotel_faq_number" name="hotel_faq[' + $faq_last_count_val + '][number_field]"' +
            ' class="faq_number_field" >' +
            '<a href="" class="button faq_remove"><span class="dashicons dashicons-trash" style="margin-top: 3px;color: red;"></span>Remove</a></p>';

        $('.faq_fields').append(fields);

    });

    // add faq
    $(".hotel_faq").on("click", ".faq_remove", function (e) {
        e.preventDefault();
        $(this).closest('p').remove();
    });


});
