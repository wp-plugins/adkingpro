jQuery(document).ready(function($) {
    $(".adkingprobanner a").click(function(e) {
        var url = $(this).attr('href');
        var post_id = $(this).data('id');
        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, url: url, post_id:post_id});
    });
    
    $(".adkingprobannerflash").mousedown(function(e) {
        var post_id = $(this).attr('rel');
        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
    });
    
    $(".adkingprobannertext").click(function(e) {
        var post_id = $(this).data('id');
        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
    });
    
//    $(".adkingprobanneradsense").mousedown(function(e) {
//        var post_id = $(this).attr('rel');
//        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
//    });

    $(".akpbanner-iframe").each(function() {
        var post_id = $(this).data('id');
        $(this).contents().find('a').each(function() {
            var url = $(this).attr('href');
            $(this).attr({'onClick': 'parent.track_click('+post_id+', \''+url+'\')', 'target': '_blank'}).css('cursor', 'pointer');
        });
    });
});

function track_click(post_id, url) {
    jQuery.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, url: url, post_id:post_id});
}