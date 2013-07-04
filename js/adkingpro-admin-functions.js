jQuery(document).ready(function($) {
    if ($("#akp_change_media_type").length > 0) {
        $('#postimagediv, #akpflashbox, #akpadsensebox, #postremoveurllink').hide();
        if ($("#akp_change_media_type").val() === 'image') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wp-plugins/adkingpro');
            $('#postimagediv').fadeIn();
            $('#postremoveurllink').fadeIn();
        } else if ($("#akp_change_media_type").val() === 'flash') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpflashbox').fadeIn();
        } else if ($("#akp_change_media_type").val() === 'adsense') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpadsensebox').fadeIn();
        }
    }
    $('#akp_change_media_type').change(function() {
        // Change views
        $('#postimagediv, #akpflashbox, #akpadsensebox, #postremoveurllink').hide();
        if ($(this).val() === 'image') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wp-plugins/adkingpro');
            $('#postimagediv').fadeIn();
            $('#postremoveurllink').fadeIn();
        } else if ($(this).val() === 'flash') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpflashbox').fadeIn();
        } else if ($(this).val() === 'adsense') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpadsensebox').fadeIn();
        }
    });
    
    var custom_uploader;
    $('#akp_flash_url_button').click(function(e) {
        e.preventDefault();
        
        //If the uploader object has already been created, reopen the dialog
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Flash File',
            button: {
                text: 'Choose Flash File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            var url = '';
            url = attachment['url'];
            $('#akp_flash_url').val(url);
        });
 
        //Open the uploader dialog
        custom_uploader.open();
    });
    
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
            $.post(akp_ajax_object.ajax_url, {action: 'akpdaterange', ajaxnonce : akp_ajax_object.akp_ajaxnonce, from_date: from_date, to_date:to_date, banner_id:banner_id}, function(response) {
                target_div.html(response);
            });
        }
    });
    
    // Document generation
    $(".akp_csv").live('click', function() {
        var info = $(this).attr('rel').split('/');
        var from_date = $(this).parent().parent().parent().find(".from_adkingpro_date").val();
        var to_date = $(this).parent().parent().parent().find(".to_adkingpro_date").val();
        $.post(akp_ajax_object.ajax_url, {action: 'akpoutputcsv', ajaxnonce : akp_ajax_object.akp_ajaxnonce, set: info[0], id:info[1], from_date:from_date, to_date:to_date}, function(response) {
            window.location = response;
        });
    });
    
    $(".akp_pdf").live('click', function() {
        var info = $(this).attr('rel').split('/');
        var from_date = $(this).parent().parent().parent().find(".from_adkingpro_date").val();
        var to_date = $(this).parent().parent().parent().find(".to_adkingpro_date").val();
        $.post(akp_ajax_object.ajax_url, {action: 'akpoutputpdf', ajaxnonce : akp_ajax_object.akp_ajaxnonce, set: info[0], id:info[1], from_date:from_date, to_date:to_date}, function(response) {
            window.location = response;
        });
    });
});