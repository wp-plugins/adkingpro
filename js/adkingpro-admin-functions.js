jQuery(document).ready(function($) {
    if ($("#akp_change_media_type").length > 0) {
        $('#postimagediv, #akpflashbox, #akpadsensebox, #postremoveurllink, #akpimagebox, #akptextbox').hide();
        if ($("#akp_change_media_type").val() === 'image') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wordpress/plugins/adkingpro');
            $('#postimagediv').fadeIn();
            $('#akpimagebox').fadeIn();
            $('#postremoveurllink').fadeIn();
        } else if ($("#akp_change_media_type").val() === 'flash') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpflashbox').fadeIn();
        } else if ($("#akp_change_media_type").val() === 'adsense') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpadsensebox').fadeIn();
        } else if ($("#akp_change_media_type").val() === 'text') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wordpress/plugins/adkingpro');
            $('#akptextbox').fadeIn();
        }
    }
    $('#akp_change_media_type').change(function() {
        // Change views
        $('#postimagediv, #akpflashbox, #akpadsensebox, #postremoveurllink, #akpimagebox, #akptextbox').hide();
        if ($(this).val() === 'image') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wordpress/plugins/adkingpro');
            $('#postimagediv').fadeIn();
            $('#akpimagebox').fadeIn();
            $('#postremoveurllink').fadeIn();
        } else if ($(this).val() === 'flash') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpflashbox').fadeIn();
        } else if ($(this).val() === 'adsense') {
            $('#title-prompt-text').text('Advert description (for internal use)');
            $('#akpadsensebox').fadeIn();
        } else if ($(this).val() === 'text') {
            $('#title-prompt-text').text('Advert URL ie http://durham.net.au/wordpress/plugins/adkingpro');
            $('#akptextbox').fadeIn();
        }
    });
    
    var flash_custom_uploader;
    $('#akp_flash_url_button').click(function(e) {
        e.preventDefault();
        
        //If the uploader object has already been created, reopen the dialog
        if (flash_custom_uploader) {
            flash_custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        flash_custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Flash File',
            button: {
                text: 'Choose Flash File'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        flash_custom_uploader.on('select', function() {
            attachment = flash_custom_uploader.state().get('selection').first().toJSON();
            var url = '';
            url = attachment['url'];
            $('#akp_flash_url').val(url);
        });
 
        //Open the uploader dialog
        flash_custom_uploader.open();
    });
    
    var image_custom_uploader;
    $('#akp_image_url_button').click(function(e) {
        e.preventDefault();
        
        //If the uploader object has already been created, reopen the dialog
        if (image_custom_uploader) {
            image_custom_uploader.open();
            return;
        }
 
        //Extend the wp.media object
        image_custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
 
        //When a file is selected, grab the URL and set it as the text field's value
        image_custom_uploader.on('select', function() {
            attachment = image_custom_uploader.state().get('selection').first().toJSON();
            var url = '';
            url = attachment['url'];
            $('#akp_image_url').val(url);
        });
 
        //Open the uploader dialog
        image_custom_uploader.open();
    });
    
    $('#expirydiv').siblings('a.edit-expiry').click(function() {
            if ($('#expirydiv').is(":hidden")) {
                    $('#expirydiv').slideDown('fast');
                    $('#exp_m').focus();
                    $(this).hide();
            }
            return false;
    });

    $('.cancel-expiry', '#expirydiv').click(function() {
            $('#expirydiv').slideUp('fast');
//            $('#exp_m').val($('#hidden_exp_m').val());
//            $('#exp_d').val($('#hidden_exp_d').val());
//            $('#exp_y').val($('#hidden_exp_y').val());
//            $('#exp_h').val($('#hidden_exp_h').val());
//            $('#exp_i').val($('#hidden_exp_i').val());
            $('#expirydiv').siblings('a.edit-expiry').show();
//            updateExpiryText();
            return false;
    });

    $('.save-expiry', '#expirydiv').click(function () { // crazyhorse - multiple ok cancels
            if ( updateExpiryText() ) {
                    $('#expirydiv').slideUp('fast');
                    $('#expirydiv').siblings('a.edit-expiry').show();
            }
            return false;
    });
    
    $(".set-never-expiry").click(function() {
        $('#expiry').html(
            'Expire on: <b>Never</b>'
        );

        $("#akp_expiry_date").val(
            'never'
        );
            
        $('#expirydiv').slideUp('fast');
        $('#expirydiv').siblings('a.edit-expiry').show();
    });
    
    function updateExpiryText() {
        
            var stamp = $('#expiry').html();

            if ( ! $('#expirydiv').length )
                    return true;

            var exp_y = $('#exp_y').val(),
                    exp_m = $('#exp_m').val(), exp_d = $('#exp_d').val(), exp_h = $('#exp_h').val(), exp_i = $('#exp_i').val(), exp_s = $('#exp_s').val();

            attemptedDate = new Date( exp_y, exp_m - 1, exp_d, exp_h, exp_i );
            originalDate = new Date( $('#hidden_exp_y').val(), $('#hidden_exp_m').val() -1, $('#hidden_exp_d').val(), $('#hidden_exp_h').val(), $('#hidden_exp_i').val() );

            if ( attemptedDate.getFullYear() != exp_y || (1 + attemptedDate.getMonth()) != exp_m || attemptedDate.getDate() != exp_d || attemptedDate.getMinutes() != exp_i ) {
                    $('.expiry-wrap', '#expirydiv').addClass('form-invalid');
                    return false;
            } else {
                    $('.expiry-wrap', '#expirydiv').removeClass('form-invalid');
            }

            
            if ( originalDate.toUTCString() == attemptedDate.toUTCString() ) { //hack
                    $('#expiry').html(stamp);
            } else {
                    $('#expiry').html(
                        'Expire on: <b>' +
                        $('option[value="' + $('#exp_m').val() + '"]', '#exp_m').text() + ' ' +
                        exp_d + ', ' +
                        exp_y + ' @ ' +
                        exp_h + ':' +
                        exp_i + '</b> '
                    );
                        
                    $("#akp_expiry_date").val(
                        exp_y+'-'+exp_m+'-'+exp_d+' '+exp_h+':'+exp_i+':'+exp_s
                    );
            }

            return true;
    }
    
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