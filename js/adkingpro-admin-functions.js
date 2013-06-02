jQuery(document).ready(function($) {
    $(".banner_detailed_stat h2").click(function() {
        if ($(this).parent().height() > 46) {
            $(this).removeClass('open').parent().animate({'height': '46px'});
        } else {
            var height = $(this).parent().css('height', 'auto').height();
            $(this).parent().css('height', '46px');
            $(this).addClass('open').parent().animate({'height': height+'px'}, function() {
                $(this).css('height', 'auto');
            });
        }
    });
    
    $(".akp_detailed").click(function() {
        if (!$(this).hasClass('active')) {
            $(this).parent().children(".akp_detailed.active").removeClass('active');
            $(this).addClass('active');
            var set = $(this).attr('rel');
            $(this).parent().next(".detailed_details").children('div').hide();
            $(this).parent().next(".detailed_details").children('.akp_detailed_'+set+'_details').fadeIn();
        }
    });
    
    $('.datepicker').datepicker({ dateFormat: "dd/mm/yy" });
    
    $(".from_adkingpro_date, .to_adkingpro_date").live('focus', function() {
        $(this).css('border-color', '#DFDFDF');
    });
    
    // Ajax erroring
    $(".akp_custom_date").live('click', function() {
        var valid = true;
        var from_date = $(this).parent().children(".from_adkingpro_date").val();
        if (from_date == '') {
            $(this).parent().children(".from_adkingpro_date").css('border-color', '#FF0000');
            valid = false;
        }
        var to_date = $(this).parent().children(".to_adkingpro_date").val();
        if (to_date == '') {
            $(this).parent().children(".to_adkingpro_date").css('border-color', '#FF0000');
            valid = false;
        }
        var banner_id = $(this).attr('rel');
        var target_div = $(this).parent().next('.returned_data');
        if (valid) {
            target_div.html("<div class='akploading'></div>");
            $.post(ajaxurl, {action: 'akpdaterange', ajaxnonce : ajaxnonce, from_date: from_date, to_date:to_date, banner_id:banner_id}, function(response) {
                target_div.html(response);
            });
        }
    });
    
    // Document generation
    $(".akp_csv").live('click', function() {
        var info = $(this).attr('rel').split('/');
        var from_date = $(this).parent().parent().parent().find(".from_adkingpro_date").val();
        var to_date = $(this).parent().parent().parent().find(".to_adkingpro_date").val();
        $.post(ajaxurl, {action: 'akpoutputcsv', ajaxnonce : ajaxnonce, set: info[0], id:info[1], from_date:from_date, to_date:to_date}, function(response) {
            window.location = response;
        });
    });
    
    $(".akp_pdf").live('click', function() {
        var info = $(this).attr('rel').split('/');
        var from_date = $(this).parent().parent().parent().find(".from_adkingpro_date").val();
        var to_date = $(this).parent().parent().parent().find(".to_adkingpro_date").val();
        $.post(ajaxurl, {action: 'akpoutputpdf', ajaxnonce : ajaxnonce, set: info[0], id:info[1], from_date:from_date, to_date:to_date}, function(response) {
            window.location = response;
        });
    });
});