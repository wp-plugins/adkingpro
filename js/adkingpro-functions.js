jQuery(document).ready(function($) {
    $(".adkingprobanner a").click(function(e) {
        //var url = $(this).attr('href');
        var post_id = $(this).data('id');
        var ga = $(this).data('ga');
        //$.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, url: url, post_id:post_id});
        track_click(post_id, ga);
    });
    
    $(".adkingprobannerflash").mousedown(function(e) {
        var post_id = $(this).attr('rel');
        var ga = $(this).data('ga');
        track_click(post_id, ga);
        //$.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
    });
    
    $(".adkingprobannertext").click(function(e) {
        var post_id = $(this).data('id');
        var ga = $(this).data('ga');
        track_click(post_id, ga);
        //$.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
    });
    
//    $(".adkingprobanneradsense").mousedown(function(e) {
//        var post_id = $(this).attr('rel');
//        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
//    });

    $(".akpbanner-iframe").each(function() {
        var post_id = $(this).data('id');
        var ga = $(this).data('ga');
        $(this).contents().find('a').each(function() {
            //var url = $(this).attr('href');
            $(this).attr({'onClick': 'parent.track_click('+post_id+', \''+ga+'\')', 'target': '_blank'}).css('cursor', 'pointer');
        });
    });
});

function track_click(post_id, ga) {
    if (typeof(post_id) == 'undefined') return false;
    if (typeof(ga) == 'undefined') ga = false;
    
    if (ga) {
        ga = $.parseJSON(ga);
        if (ga.implemented == 'classic')
            _gaq.push(['_trackEvent',ga.campaign, ga.click_action, ga.banner]);
        else if (ga.implemented == 'universal')
            ga('send', 'event', ga.campaign, ga.click_action, ga.banner);
    } else
        jQuery.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, post_id:post_id});
}