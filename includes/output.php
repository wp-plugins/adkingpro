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
                'advert_types'=>$type,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => 'never',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => current_time('timestamp'),
                        'type' => 'numeric',
                        'compare' => '>='
                    )
                )
                ));
            while (have_posts()) : the_post();
                $term = get_term_by("slug", $type, 'advert_types');
                $term_meta = get_option( "akp_advert_type_".$term->term_id);
                $post_id = get_the_ID();
                $cfields = akp_return_fields();
                if ($cfields['akp_expiry_date'][0] == '') $cfields['akp_expiry_date'][0] = 'never';
                if ($cfields['akp_expiry_date'][0] !== 'never')
                if ($cfields['akp_media_type'][0] == '') $cfields['akp_media_type'][0] = 'image';
                switch ($cfields['akp_media_type'][0]) {
                    case 'image':
                        $image = $cfields['akp_image_url'][0];
                        if ($image == '')
                            $image = akp_get_featured_image($post_id, "akp_".$term->term_id);
                        $display_link = true;
                        if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                        $output .= "<div class='adkingprobanner ".$type." banner".$post_id."' style='width: ".$term_meta['advert_width']."px; height: ".$term_meta['advert_height']."px;'>";
                        if ($display_link)
                            $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                        $output .= "<img src='".$image."' style='max-width: ".$term_meta['advert_width']."px; max-height: ".$term_meta['advert_height']."px;' />";
                        if ($display_link)
                            $output .= "</a>";
                        $output .= "</div>";
                        break;
                    
                    case 'flash':
                        $output .= "<div class='adkingprobannerflash ".$type." banner".$post_id."'>";
                        $output .= '<object width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'">';
                        $output .= '<param value="'.$cfields['akp_flash_url'][0].'" name="wmode" value="transparent">';
                        $output .= '<embed src="'.$cfields['akp_flash_url'][0].'" width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'" wmode="transparent" allowfullscreen="false" allowscriptaccess="always">';
                        $output .= '</embed>';
                        $output .= '</object>';
                        $output .= "</div>";
                        break;
                    
                    case 'adsense':
                        $output .= "<div class='adkingprobanneradsense ".$type." banner".$post_id."'>";
                        $output .= $cfields['akp_adsense_code'][0];
                        $output .= "</div>";
                        break;
                    
                    case 'text':
                        $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."' class='adkingprobannertext ".$type." banner".$post_id."'>";
                        $output .= $cfields['akp_text'][0];
                        $output .= "</a>";
                        break;
                }
            endwhile;
            wp_reset_query();
        } elseif (is_numeric($banner)) {
            query_posts(array(
                'post_type'=>'adverts_posts',
                'p'=>$banner,
                'meta_query' => array(
                    'relation' => 'OR',
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => 'never',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => '',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'akp_expiry_date',
                        'value' => current_time('timestamp'),
                        'type' => 'numeric',
                        'compare' => '>='
                    )
                )
                ));
            while (have_posts()) : the_post();
                $post_id = get_the_ID();
                $cfields = akp_return_fields();
                if ($cfields['akp_media_type'][0] == '') $cfields['akp_media_type'][0] = 'image';
                echo $cfields['akp_media_type'][0];
                switch ($cfields['akp_media_type'][0]) {
                    case 'image':
                        $image = $cfields['akp_image_url'][0];
                        if ($image == '')
                            $image = akp_get_featured_image($post_id);
                        $display_link = true;
                        if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                        $output .= "<div class='adkingprobanner ".$type." banner".$post_id."'>";
                        if ($display_link)
                            $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                        $output .= "<img src='".$image."' />";
                        if ($display_link)
                            $output .= "</a>";
                        $output .= "</div>";
                        break;
                    
                    case 'flash':
                        $output .= "<div class='adkingprobannerflash ".$type." banner".$post_id."' rel='".$post_id."'>";
                        $output .= '<object width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'">';
                        $output .= '<param value="'.$cfields['akp_flash_url'][0].'" name="wmode" value="transparent">';
                        $output .= '<embed src="'.$cfields['akp_flash_url'][0].'" width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'" wmode="transparent" allowfullscreen="false" allowscriptaccess="always">';
                        $output .= '</embed>';
                        $output .= '</object>';
                        $output .= "</div>";
                        break;
                    
                    case 'adsense':
                        $output .= "<div class='adkingprobanneradsense ".$type." banner".$post_id."' rel='".$post_id."'>";
                        $output .= $cfields['akp_adsense_code'][0];
                        $output .= "</div>";
                        break;
                    
                    case 'text':
                        $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."' class='adkingprobannertext ".$type." banner".$post_id."'>";
                        $output .= $cfields['akp_text'][0];
                        $output .= "</a>";
                        break;
                }
            endwhile;
            wp_reset_query();
        }
        if (isset($post_id))
            akp_log_impression($post_id);
	return $output;
}
add_shortcode( 'adkingpro', 'adkingpro_func' );
?>
