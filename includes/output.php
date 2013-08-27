<?php

// [adkingpro type="sidebar" banner="random" rotate="false" speed="5000" changespeed="600" effect='fade' render="1"]
function adkingpro_func( $atts ) {
	extract( shortcode_atts( array(
		'type' => 'sidebar',
		'banner' => 'random',
                'rotate' => false,
                'speed' => '5000',
                'changespeed' => '600',
                'effect'=> 'fade',
                'render' => '0'
	), $atts ) );
        
        $output = '';
        
        $effects = array('fade', 'slideLeft', 'none');
        if (!in_array($effect, $effects)) $effect = 'fade';
        
        $banner = explode(',', $banner);
        if (count($banner) == 1) $banner = $banner[0];
        else {
            for ($b=0;$b<count($banner);$b++) {
                $banner[$b] = trim($banner[$b]);
                if (!is_numeric($banner[$b])) unset($banner[$b]);
            }
        }
        
        if ($banner == 'random') {
            // ADVERT TYPE OUTPUT
            if ($render == 0 && $rotate) $render = -1;
            if ($render == 0) $render = 1;
            query_posts(array(
                'post_type'=>'adverts_posts',
                'orderby'=>'rand',
                'showposts'=>$render,
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
            
            if ($render > 1 || $render === -1) {
                $slideshow = "";
                if ($rotate) $slideshow = "akp_slideshow".rand(10000, 99999);
                $output .= "<div class='adkingprocontainer' id='".$slideshow."'>";
            }
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
                        $alt = $cfields['akp_image_alt'][0];
                        if ($image == '')
                            $image = akp_get_featured_image($post_id, "akp_".$term->term_id);
                        $display_link = true;
                        if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                        $output .= "<div class='adkingprobanner ".$type." akpbanner banner".$post_id."' style='width: ".$term_meta['advert_width']."px; height: ".$term_meta['advert_height']."px;'>";
                        if ($display_link)
                            $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                        $output .= "<img src='".$image."' style='max-width: ".$term_meta['advert_width']."px; max-height: ".$term_meta['advert_height']."px;' alt='".$alt."' />";
                        if ($display_link)
                            $output .= "</a>";
                        $output .= "</div>";
                        break;
                    
                    case 'flash':
                        $output .= "<div class='adkingprobannerflash ".$type." akpbanner banner".$post_id."'>";
                        $output .= '<object width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'">';
                        $output .= '<param value="'.$cfields['akp_flash_url'][0].'" name="wmode" value="transparent">';
                        $output .= '<embed src="'.$cfields['akp_flash_url'][0].'" width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'" wmode="transparent" allowfullscreen="false" allowscriptaccess="always">';
                        $output .= '</embed>';
                        $output .= '</object>';
                        $output .= "</div>";
                        break;
                    
                    case 'adsense':
                        $output .= "<div class='adkingprobanneradsense ".$type." akpbanner banner".$post_id."'>";
                        $output .= $cfields['akp_adsense_code'][0];
                        $output .= "</div>";
                        break;
                    
                    case 'text':
                        if ($rotate) $output .= "<div class='adkingprobannertextcontainer ".$type." akpbanner banner".$post_id."'>";
                        $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."' class='adkingprobannertext ".$type." banner".$post_id."'>";
                        $output .= $cfields['akp_text'][0];
                        $output .= "</a>";
                        if ($rotate) $output .= "</div>";
                        break;
                }
                if (isset($post_id))
                    akp_log_impression($post_id);
            endwhile;
            if ($render > 1 || $render === -1) {
                $output .= "</div>";
                if ($rotate) {
                    $output .= "<script type='text/javascript'>jQuery('#".$slideshow."').jshowoff({ speed:".$speed.", changeSpeed:".$changespeed.", effect: '".$effect."', links: false, controls: false });</script>";
                }
            }
            wp_reset_query();
        } elseif (is_array($banner)) {
            // MULTIPLE BANNER IDS
            if ($render == 0 && $rotate) count($banner);
            if ($render == 0) $render = 1;
            query_posts(array(
                'post_type'=>'adverts_posts',
                'orderby'=>'rand',
                'showposts'=>$render,
                'post__in'=>$banner,
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
            
            if ($render > 1) {
                $slideshow = "";
                if ($rotate) $slideshow = "akp_slideshow".rand(10000, 99999);
                $output .= "<div class='adkingprocontainer' id='".$slideshow."'>";
            }
            while (have_posts()) : the_post();
                $post_id = get_the_ID();
                $cfields = akp_return_fields();
                if ($cfields['akp_expiry_date'][0] == '') $cfields['akp_expiry_date'][0] = 'never';
                if ($cfields['akp_expiry_date'][0] !== 'never')
                if ($cfields['akp_media_type'][0] == '') $cfields['akp_media_type'][0] = 'image';
                switch ($cfields['akp_media_type'][0]) {
                    case 'image':
                        $image = $cfields['akp_image_url'][0];
                        $alt = $cfields['akp_image_alt'][0];
                        if ($image == '')
                            $image = akp_get_featured_image($post_id);
                        $display_link = true;
                        if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                        $output .= "<div class='adkingprobanner ".$type." akpbanner banner".$post_id."'>";
                        if ($display_link)
                            $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                        $output .= "<img src='".$image."' alt='".$alt."' />";
                        if ($display_link)
                            $output .= "</a>";
                        $output .= "</div>";
                        break;
                    
                    case 'flash':
                        $output .= "<div class='adkingprobannerflash ".$type." akpbanner banner".$post_id."'>";
                        $output .= '<object width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'">';
                        $output .= '<param value="'.$cfields['akp_flash_url'][0].'" name="wmode" value="transparent">';
                        $output .= '<embed src="'.$cfields['akp_flash_url'][0].'" width="'.$cfields['akp_flash_width'][0].'" height="'.$cfields['akp_flash_height'][0].'" wmode="transparent" allowfullscreen="false" allowscriptaccess="always">';
                        $output .= '</embed>';
                        $output .= '</object>';
                        $output .= "</div>";
                        break;
                    
                    case 'adsense':
                        $output .= "<div class='adkingprobanneradsense ".$type." akpbanner banner".$post_id."'>";
                        $output .= $cfields['akp_adsense_code'][0];
                        $output .= "</div>";
                        break;
                    
                    case 'text':
                        if ($rotate) $output .= "<div class='adkingprobannertextcontainer ".$type." akpbanner banner".$post_id."'>";
                        $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."' class='adkingprobannertext ".$type." banner".$post_id."'>";
                        $output .= $cfields['akp_text'][0];
                        $output .= "</a>";
                        if ($rotate) $output .= "</div>";
                        break;
                }
                if (isset($post_id))
                    akp_log_impression($post_id);
            endwhile;
            if ($render > 1) {
                $output .= "</div>";
                if ($rotate) {
                    $output .= "<script type='text/javascript'>jQuery('#".$slideshow."').jshowoff({ speed:".$speed.", changeSpeed:".$changespeed.", effect: '".$effect."', links: false, controls: false });</script>";
                }
            }
            wp_reset_query();
        } elseif (is_numeric($banner)) {
            // SINGLE BANNER ID
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
                switch ($cfields['akp_media_type'][0]) {
                    case 'image':
                        $image = $cfields['akp_image_url'][0];
                        $alt = $cfields['akp_image_alt'][0];
                        if ($image == '')
                            $image = akp_get_featured_image($post_id);
                        $display_link = true;
                        if (!isset($cfields['akp_remove_url']) || (isset($cfields['akp_remove_url']) && $cfields['akp_remove_url'][0] == 1)) $display_link = false;
                        $output .= "<div class='adkingprobanner ".$type." banner".$post_id."'>";
                        if ($display_link)
                            $output .= "<a href='".get_the_title()."' target='_blank' rel='".$post_id."'>";
                        $output .= "<img src='".$image."' alt='".$alt."' />";
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
                if (isset($post_id))
                    akp_log_impression($post_id);
            endwhile;
            wp_reset_query();
        }
	return $output;
}
add_shortcode( 'adkingpro', 'adkingpro_func' );
?>
