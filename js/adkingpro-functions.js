$(document).ready(function() {
    $(".adkingprobanner a").click(function(e) {
        var url = $(this).attr('href');
        var post_id = $(this).attr('rel');
        $.post(AkpAjax.ajaxurl, {action: 'akplogclick', ajaxnonce : AkpAjax.ajaxnonce, url: url, post_id:post_id});
    });
});