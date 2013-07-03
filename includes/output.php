<?php

// [adkingpro type="sidebar" banner="random"]
function adkingpro_func( $atts ) {
	extract( shortcode_atts( array(
		'type' => 'sidebar',
		'banner' => 'random',
	), $atts ) );
        
        $output = '';
        
        if ($banner == 'random') {
            query_posts(array(
                'post_type'=>'adverts_posts',
                'orderby'=>'rand',
                'showposts'=>1,
                'advert_types'=>$type
                ));
            while (have_posts()) : the_post();
                $post_id = get_the_ID();
                $image = akp_get_featured_image($post_id);
                $cfields = akp_return_fields();
                $display_link = true;
                if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                $output .= "<div class='adkingprobanner ".$type." banner".$post_id."'>";
                if ($display_link)
                    $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                $output .= "<img src='".$image."' />";
                if ($display_link)
                    $output .= "</a>";
                $output .= "</div>";
            endwhile;
            wp_reset_query();
        } elseif (is_numeric($banner)) {
            query_posts(array(
                'post_type'=>'adverts_posts',
                'p'=>$banner
                ));
            while (have_posts()) : the_post();
                $post_id = get_the_ID();
                $image = akp_get_featured_image($post_id);
                $cfields = akp_return_fields();
                $display_link = true;
                if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                $output .= "<div class='adkingprobanner ".$type." banner".$post_id."'>";
                if ($display_link)
                    $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                $output .= "<img src='".$image."' />";
                if ($display_link)
                    $output .= "</a>";
                $output .= "</div>";
            endwhile;
            wp_reset_query();
        }
        akp_log_impression($post_id);
	return $output;
}
add_shortcode( 'adkingpro', 'adkingpro_func' );
?>
