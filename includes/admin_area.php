<?php

function register_akp_options() {
  register_setting( 'akp-options', 'expiry_time' );
  register_setting( 'akp-options', 'impression_expiry_time' );
  register_setting( 'akp-options', 'week_start' );
  register_setting( 'akp-options', 'revenue_currency' );
  register_setting( 'akp-options', 'pdf_theme' );
  register_setting( 'akp-options', 'akp_image_sizes' );
}
add_action( 'admin_init', 'register_akp_options' );

// Default Options
add_option( 'expiry_time', '+6 hours' );
add_option( 'impression_expiry_time', '+0 hours' );
add_option( 'week_starts', 'monday' );
add_option( 'revenue_currency', '$' );
add_option( 'pdf_theme', 'default' );
add_option( 'akp_image_sizes', '' );

// Register Adverts
function akp_create_post_type() {
    register_post_type( 'adverts_posts',
        array(
            'labels' => array(
                'name' => __( 'Adverts' ),
                'singular_name' => __( 'Advert' ),
                'all_items'=>'All Adverts',
                'edit_item'=>'Edit Advert',
                'update_item'=>'Update Advert',
                'add_new_item'=>'Add New Advert',
                'new_item_name'=>'New Advert',
            ),
            'public' => true,
            'exclude_from_search' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'thumbnail' )
        )
    );
}
add_action( 'init', 'akp_create_post_type' );

// Styling for the custom post type icon
function wpt_akp_icons() {
    ?>
    <style type="text/css" media="screen">
        #menu-posts-adverts_posts .wp-menu-image {
            background: url(<?= plugins_url('/images/akp-icon_16x16_sat.png', dirname(__FILE__)) ?>) no-repeat center center !important;
        }
	#menu-posts-adverts_posts:hover .wp-menu-image, #menu-posts-adverts_posts.wp-has-current-submenu .wp-menu-image {
            background: url(<?= plugins_url('/images/akp-icon_16x16.png', dirname(__FILE__)) ?>) no-repeat center center !important;
        }
	#icon-edit.icon32-posts-adverts_posts {background: url(<?= plugins_url('/images/akp-icon_32x32_sat.png', dirname(__FILE__)) ?>) no-repeat;}
    </style>
<?php }
add_action( 'admin_head', 'wpt_akp_icons' );

function akp_widget_registration() {
    register_widget( 'AdKingPro_Widget' );
}

add_action( 'widgets_init', 'akp_widget_registration');

// Add scripts to page
function akp_my_scripts_method() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 
        'jshowoff', 
        plugins_url('js/jquery.jshowoff.js', dirname(__FILE__)),
        array('jquery') );
    wp_enqueue_script(
        'adkingpro-js',
        plugins_url('js/adkingpro-functions.js', dirname(__FILE__)),
        array('jquery')
    );
    wp_localize_script( 'adkingpro-js', 'AkpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajaxnonce' => wp_create_nonce( 'akpN0nc3' ), ) );
}
add_action('wp_enqueue_scripts', 'akp_my_scripts_method');

//add extra fields to category edit form callback function
function akp_extra_advert_types_fields( $tag ) {    //check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "akp_advert_type_$t_id");
?>
<tr class="form-field">
<td colspan="2">
Leave the below blank to use the full size of the image you upload.          
</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_width"><?php _e('Set width of advert'); ?></label></th>
<td>
<input type="text" name="Cat_meta[advert_width]" id="Cat_meta[advert_width]" size="5" style="width:20%;" value="<?php echo $cat_meta['advert_width'] ? $cat_meta['advert_width'] : ''; ?>" /><br />
            <span class="description"><?php _e('If variable, please leave blank'); ?></span>
        </td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_height"><?php _e('Set height of advert'); ?></label></th>
<td>
<input type="text" name="Cat_meta[advert_height]" id="Cat_meta[advert_height]" size="5" style="width:20%;" value="<?php echo $cat_meta['advert_height'] ? $cat_meta['advert_height'] : ''; ?>" /><br />
            <span class="description"><?php _e('If variable, please leave blank'); ?></span>
        </td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_crop"><?php _e('Hard crop image?'); ?></label></th>
<td>
    <input type="hidden" name="Cat_meta[advert_crop]" value="0" />
<input type="checkbox" name="Cat_meta[advert_crop]" id="Cat_meta[advert_crop]" value="1"<?php if ($cat_meta['advert_crop'] == '1') echo ' checked'; ?> /><br />
        </td>
</tr>
<?php
}
//add extra fields to advert type edit form hook
add_action( 'advert_types_edit_form_fields', 'akp_extra_advert_types_fields', 10, 2);

// save extra taxonomy fields callback function
function akp_save_extra_advert_types_fields( $term_id ) {
    if ( isset( $_POST['Cat_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "akp_advert_type_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['Cat_meta'][$key])){
                $term_meta[$key] = $_POST['Cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "akp_advert_type_$t_id", $term_meta );
        
        // Use values to save image size for generation. $term_meta['advert_width'].'x'.$term_meta['advert_height']
        $sizes = get_option('akp_image_sizes');
        //$sizes = array();
        if ($term_meta['advert_width'] !== '' || $term_meta['advert_height'] !== '') {
            $sizes['akp_'.$t_id]['width'] = ($term_meta['advert_width']) ? $term_meta['advert_width'] : 9999;
            $sizes['akp_'.$t_id]['height'] = ($term_meta['advert_height']) ? $term_meta['advert_height'] : 9999;
            $sizes['akp_'.$t_id]['crop'] = ($term_meta['advert_crop']) ? $term_meta['advert_crop'] : 0;
        } else unset($sizes['akp_'.$t_id]);
        
        update_option( "akp_image_sizes", $sizes );
        
        // Generate existing images
         
        $adverts =& get_posts( array(
                'post_type' => 'adverts_posts',
                'numberposts' => -1,
                'output' => 'object',
        ) );
        
        foreach ( $adverts as $advert ) {
            
            $attachments =& get_children(array('post_type'=>'attachment', 'post_mimi_type'=>'image', 'post_parent'=>$advert->ID));
            
            foreach ($attachments as $attachment) {
            
                $fullsizepath = get_attached_file( $attachment->ID );

                if ( FALSE !== $fullsizepath && @file_exists($fullsizepath) ) {
                    set_time_limit( 30 );

                    $metadata = array();
                    if ( preg_match('!^image/!', get_post_mime_type( $attachment )) && file_is_displayable_image($fullsizepath) ) {
                            $imagesize = getimagesize( $fullsizepath );
                            $metadata['width'] = $imagesize[0];
                            $metadata['height'] = $imagesize[1];
                            list($uwidth, $uheight) = wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
                            $metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";

                            // Make the file path relative to the upload dir
                            $metadata['file'] = _wp_relative_upload_path($fullsizepath);

                            $intermediate_size = image_make_intermediate_size( $fullsizepath, $term_meta['advert_width'], $term_meta['advert_height'], $term_meta['advert_crop'] );

                            if ($intermediate_size)
                                $metadata['sizes']['akp_'.$t_id] = $intermediate_size;

                            // fetch additional metadata from exif/iptc
                            $image_meta = wp_read_image_metadata( $fullsizepath );
                            if ( $image_meta )
                                    $metadata['image_meta'] = $image_meta;

                    }
                    wp_update_attachment_metadata( $attachment->ID, apply_filters( 'wp_generate_attachment_metadata', $metadata, $attachment->ID ) );
                }
            }
        }
        return;
    }
}
add_action( 'edited_advert_types', 'akp_save_extra_advert_types_fields', 10, 2);

function akp_image_sizes()
{

    if ( function_exists( 'add_image_size' ) ) { 
        $sizes = get_option('akp_image_sizes');
        if (!empty($sizes)) :
            foreach ($sizes as $image_name => $dimensions) {
                if ($dimensions['crop'] == '1') $crop = true; else $crop = false;
                add_image_size( $image_name, $dimensions['width'], $dimensions['width'], $crop);
            }
        endif;
    }
}
add_action( 'init', 'akp_image_sizes' );

// Update title field to become URL field
function akp_title_text_input( $title ){
    global $post;
    if($post->post_type == 'adverts_posts') 
        return $title = 'Advert URL ie http://kingpro.me/plugins/ad-king-pro';
    return $title;
}
add_filter( 'enter_title_here', 'akp_title_text_input' );

// Update Feature Image to become Advert Image
function akp_change_meta_boxes()
{
    add_meta_box('akpmediatype', __('Media Type'), 'akp_media_type', 'adverts_posts', 'normal', 'high');
    
    remove_meta_box( 'postimagediv', 'adverts_posts', 'side' );
    if (current_theme_supports('post-thumbnails')) {
        add_meta_box('postimagediv', __('Advert Image'), 'post_thumbnail_meta_box', 'adverts_posts', 'normal', 'high');
        add_meta_box('akpimageattrbox', __('Advert Image Attributes'), 'akp_image_attrs_box', 'adverts_posts', 'normal', 'high');
    } else 
        add_meta_box('akpimagebox', __('Advert Image'), 'akp_image_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akpflashbox', __('Advert Flash File'), 'akp_flash_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akpadsensebox', __('Advert AdSense Code'), 'akp_adsense_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akptextbox', __('Advert Text'), 'akp_text_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('postremoveurllink', __('Remove Link from Advert?'), 'akp_remove_url_link', 'adverts_posts', 'advanced', 'high');
    add_meta_box('postclickstatsdiv', __('Advert Stats'), 'akp_post_click_stats', 'adverts_posts', 'advanced', 'low');
    add_meta_box('revenuevaluesdiv', __('Advert Revenue'), 'akp_revenue_values', 'adverts_posts', 'side', 'low');
    add_meta_box('linkoptionsdiv', __('Link Options'), 'akp_link_options', 'adverts_posts', 'side', 'low');
}
add_action('do_meta_boxes', 'akp_change_meta_boxes');

add_action( 'post_submitbox_misc_actions', 'expiry_in_publish' );
function expiry_in_publish($post)
{
    global $post;
    if (get_post_type($post) == 'adverts_posts') {
        $expiry = (get_post_meta($post->ID, 'akp_expiry_date', true)) ? get_post_meta($post->ID, 'akp_expiry_date', true) : 'never';
        if ($expiry !== 'never') {
            $expiry_m = date('m', $expiry);
            $expiry_d = date('d', $expiry);
            $expiry_y = date('Y', $expiry);
            $expiry_h = date('H', $expiry);
            $expiry_i = date('i', $expiry);
            $expiry_output = date('M j, Y @ G:i', $expiry);
            $expiry_value = date('Y-m-d G:i:s', $expiry);

        } else {
            $expiry_output = 'Never';
            $expiry_value = $expiry;
            $expiry_m = date('m', current_time('timestamp'));
            $expiry_d = date('d', current_time('timestamp'));
            $expiry_y = date('Y', current_time('timestamp'));
            $expiry_h = date('H', current_time('timestamp'));
            $expiry_i = date('i', current_time('timestamp'));
        }
        echo '<div class="misc-pub-section misc-pub-section-last curtime">
             <span id="expiry">
            Expire on: <b>'.$expiry_output.'</b>
            </span>
            <a href="#edit_expiry" class="edit-expiry hide-if-no-js">Edit</a>
            <div id="expirydiv" class="hide-if-js"><div class="expiry-wrap"><select id="exp_m">
                            <option value="01"'.(($expiry_m == '01') ? ' selected="selected"' : '').'>01-Jan</option>
                            <option value="02"'.(($expiry_m == '02') ? ' selected="selected"' : '').'>02-Feb</option>
                            <option value="03"'.(($expiry_m == '03') ? ' selected="selected"' : '').'>03-Mar</option>
                            <option value="04"'.(($expiry_m == '04') ? ' selected="selected"' : '').'>04-Apr</option>
                            <option value="05"'.(($expiry_m == '05') ? ' selected="selected"' : '').'>05-May</option>
                            <option value="06"'.(($expiry_m == '06') ? ' selected="selected"' : '').'>06-Jun</option>
                            <option value="07"'.(($expiry_m == '07') ? ' selected="selected"' : '').'>07-Jul</option>
                            <option value="08"'.(($expiry_m == '08') ? ' selected="selected"' : '').'>08-Aug</option>
                            <option value="09"'.(($expiry_m == '09') ? ' selected="selected"' : '').'>09-Sep</option>
                            <option value="10"'.(($expiry_m == '10') ? ' selected="selected"' : '').'>10-Oct</option>
                            <option value="11"'.(($expiry_m == '11') ? ' selected="selected"' : '').'>11-Nov</option>
                            <option value="12"'.(($expiry_m == '12') ? ' selected="selected"' : '').'>12-Dec</option>
    </select><input type="text" id="exp_d" value="'.$expiry_d.'" size="2" maxlength="2" autocomplete="off">, <input type="text" id="exp_y" value="'.$expiry_y.'" size="4" maxlength="4" autocomplete="off"> @ <input type="text" id="exp_h" value="'.$expiry_h.'" size="2" maxlength="2" autocomplete="off"> : <input type="text" id="exp_i" value="'.$expiry_i.'" size="2" maxlength="2" autocomplete="off"></div><input type="hidden" id="exp_s" value="55">

    <input type="hidden" id="hidden_exp_m" value="'.$expiry_m.'">
    <input type="hidden" id="hidden_exp_d" value="'.$expiry_d.'">
    <input type="hidden" id="hidden_exp_y" value="'.$expiry_y.'">
    <input type="hidden" id="hidden_exp_h" value="'.$expiry_h.'">
    <input type="hidden" id="hidden_exp_i" value="'.$expiry_i.'">

    <input type="hidden" name="akp_expiry_date" id="akp_expiry_date" value="'.$expiry_value.'" />

    <p>
    <a href="#edit_expiry" class="save-expiry hide-if-no-js button">OK</a>
    <a href="#edit_expiry" class="cancel-expiry hide-if-no-js">Cancel</a>
    <a href="#edit_expiry" class="set-never-expiry hide-if-no-js button right">Set to Never</a>
    </p>
                    </div>
        </div>';
    }
}

// Selection of media type
function akp_media_type($object, $box) {
    global $wpdb, $post;
    $media_type = (get_post_meta( $post->ID, 'akp_media_type', true )) ? get_post_meta( $post->ID, 'akp_media_type', true ) : 'image';
    $flash = ($media_type == 'flash') ? ' selected' : '';
    $adsense = ($media_type == 'adsense') ? ' selected' : '';
    $text = ($media_type == 'text') ? ' selected' : '';
    
    echo "<select name='akp_media_type' id='akp_change_media_type'>";
    echo "<option value='image'>Image</option>";
    echo "<option value='flash'".$flash.">Flash</option>";
    echo "<option value='adsense'".$adsense.">AdSense</option>";
    echo "<option value='text'".$text.">Text</option>";
    echo "</select>";
}

function akp_image_box($object, $box) {
    global $post;
    $image_url = (get_post_meta( $post->ID, 'akp_image_url', true )) ? get_post_meta( $post->ID, 'akp_image_url', true ) : '';
    $image_alt = (get_post_meta( $post->ID, 'akp_image_alt', true )) ? get_post_meta( $post->ID, 'akp_image_alt', true ) : '';
    
    echo '<label for="akp_image_url">';
    echo '<input id="akp_image_url" type="text" size="36" name="akp_image_url" value="'.$image_url.'" />';
    echo '<input id="akp_image_url_button" class="button" type="button" value="Upload Image" />';
    echo '<br />Enter a URL or upload an image (You are seeing this box as you have disabled "post-thumbnails" support.)';
    echo '</label><br /><br />';
    echo '<label for="akp_image_alt">Banner description (this will be added to the alt tag on the image)</label>';
    echo '<br /><input id="akp_image_alt" type="text" size="36" name="akp_image_alt" value="'.$image_alt.'" />';
    echo '<br /><br />';
}

function akp_image_attrs_box($object, $box) {
    global $post;
    $image_alt = (get_post_meta( $post->ID, 'akp_image_alt', true )) ? get_post_meta( $post->ID, 'akp_image_alt', true ) : '';

    echo '<label for="akp_image_alt">Banner description (this will be added to the alt tag on the image)</label>';
    echo '<br /><input id="akp_image_alt" type="text" style="width: 100%;" name="akp_image_alt" value="'.$image_alt.'" />';
    echo '<br /><br />';
}

function akp_flash_box($object, $box) {
    global $post;
    $flash_url = (get_post_meta( $post->ID, 'akp_flash_url', true )) ? get_post_meta( $post->ID, 'akp_flash_url', true ) : '';
    $flash_width = (get_post_meta( $post->ID, 'akp_flash_width', true )) ? get_post_meta( $post->ID, 'akp_flash_width', true ) : '';
    $flash_height = (get_post_meta( $post->ID, 'akp_flash_height', true )) ? get_post_meta( $post->ID, 'akp_flash_height', true ) : '';
    echo '<label for="akp_flash_url">';
    echo '<input id="akp_flash_url" type="text" size="36" name="akp_flash_url" value="'.$flash_url.'" />';
    echo '<input id="akp_flash_url_button" class="button" type="button" value="Upload SWF File" />';
    echo '<br />Enter a URL or upload a SWF file';
    echo '</label><br /><br />';
    echo '<label for="akp_flash_width" style="width: 85px; display: block; float: left;">SWF Width</label><input type="text" name="akp_flash_width" value="'.$flash_width.'" style="width: 60px;" /><br />';
    echo '<label for="akp_flash_height" style="width: 85px; display: block; float: left;">SWF Height</label><input type="text" name="akp_flash_height" value="'.$flash_height.'" style="width: 60px;" /><br />';
}

function akp_adsense_box($object, $box) {
    global $post;
    $adsense_code = (get_post_meta( $post->ID, 'akp_adsense_code', true )) ? get_post_meta( $post->ID, 'akp_adsense_code', true ) : '';
    echo '<label for="akp_adsense_code">Enter the Ad Unit Code given from your Google AdSense account</label>';
    echo '<br /><textarea name="akp_adsense_code" style="width: 100%; height: 200px;">'.$adsense_code.'</textarea><br />';
    echo '<br /><strong>Please note that only impressions are tracked for these ads as the clicks are registers via AdSense</strong>';
}

function akp_text_box($object, $box) {
    global $post;
    $text = (get_post_meta( $post->ID, 'akp_text', true )) ? get_post_meta( $post->ID, 'akp_text', true ) : '';
    
    echo '<label for="akp_text">Enter the text you would like on the link that will be tracked</label>';
    echo '<br /><input type="text" name="akp_text" style="width: 100%;" value="'.$text.'" /><br />';
}

// Output stats for post
function akp_post_click_stats($object, $box) {
    global $wpdb, $post;
    $clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$object->ID'");
    echo "This banner has had ".$clicks[0]->clicks." since being published. <a href='".admin_url('/index.php?page=akp-detailed-stats')."'>View Detailed Report</a>";
    echo '<br /><br /><a href="'.admin_url('admin.php?action=akpresetdata&post='.$post->ID).'" onclick="if(!confirm(\'Are you sure you want to reset the tracking data back to 0? There is no reversing this action.\')){return false;}">Reset Tracking Data</a>';
}

function akpresetdata_admin_action()
{
    global $wpdb;
    $post_id = $_GET['post'];
    
    $wpdb->query( $wpdb->prepare( 
            "DELETE FROM ".$wpdb->prefix."akp_impressions_expire
             WHERE post_id = %d
            ",
            $post_id
            )
    );
    $wpdb->query( $wpdb->prepare( 
            "DELETE FROM ".$wpdb->prefix."akp_impressions_log
            WHERE post_id = %d
            ", 
            $post_id
            ) 
    );
    
    $wpdb->query( $wpdb->prepare( 
            "DELETE FROM ".$wpdb->prefix."akp_click_expire
             WHERE post_id = %d
            ",
            $post_id
            )
    );
    $wpdb->query( $wpdb->prepare( 
            "DELETE FROM ".$wpdb->prefix."akp_click_log
            WHERE post_id = %d
            ", 
            $post_id
            )
    );
    
    header("Location: ".$_SERVER['HTTP_REFERER']);
    
}
add_action( 'admin_action_akpresetdata', 'akpresetdata_admin_action' );

// Add checkbox to remove URL Link off advert
//function akp_remove_url_link($object, $box) {
//    global $post;
//    $remove_url = get_post_meta( $post->ID, 'akp_remove_url', true );
//    // Use nonce for verification
//    echo '<input type="hidden" name="akp_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
//    echo '<input type="checkbox" value="1" name="akp_remove_url" id="akp_remove_url"', $remove_url ? ' checked="checked"' : '', ' />';
//}

// Add checkbox to remove URL Link off advert
function akp_revenue_values($object, $box) {
    global $post;
    $revenue_impression = get_post_meta( $post->ID, 'akp_revenue_per_impression', true );
    $revenue_click = get_post_meta( $post->ID, 'akp_revenue_per_click', true );
    
    echo '<div class="misc-pub-section"><label for="akp_revenue_per_impression">Revenue Per Impression:</label>';
    echo '<input type="text" name="akp_revenue_per_impression" value="', $revenue_impression ? $revenue_impression : '0.00', '" style="width: 70px;float: right;margin-top: -3px;" />';
    echo '</div>';
    echo '<div class="misc-pub-section"><label for="akp_revenue_per_click">Revenue Per Click:</label>';
    echo '<input type="text" name="akp_revenue_per_click" value="', $revenue_click ? $revenue_click : '0.00', '" style="width: 70px;float: right;margin-top: -3px;" />';
    echo '</div>';
}

// Add checkbox to remove URL Link off advert
function akp_link_options($object, $box) {
    global $post;
    $remove_url = get_post_meta( $post->ID, 'akp_remove_url', true );
    $target = (get_post_meta( $post->ID, 'akp_target', true )) ? get_post_meta( $post->ID, 'akp_target', true ) : '';
    $self = ($target == 'self') ? ' selected' : '';
    $parent = ($target == 'parent') ? ' selected' : '';
    $top = ($target == 'top') ? ' selected' : '';
    $none = ($target == 'none') ? ' selected' : '';
    $nofollow = (get_post_meta( $post->ID, 'akp_nofollow', true )) ? get_post_meta( $post->ID, 'akp_nofollow', true ) : '';
    
    // Use nonce for verification
    echo '<input type="hidden" name="akp_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    
    echo '<div class="misc-pub-section"><label for="akp_remove_url">Remove URL from link:</label>';
    echo '<input type="checkbox" value="1" name="akp_remove_url" id="akp_remove_url"', $remove_url ? ' checked="checked"' : '', ' style="width: 70px;float: right;margin-top: -3px;" />';
    echo '</div>';
    
    echo "<div class='misc-pub-section'><label for='akp_target'>Window Target</label><select name='akp_target' style='width: 70px;float: right;margin-top: -3px;' >";
    echo "<option value='blank'>_blank</option>";
    echo "<option value='self'".$self.">_self</option>";
    echo "<option value='parent'".$parent.">_parent</option>";
    echo "<option value='top'".$top.">_top</option>";
    echo "<option value='none'".$none.">none</option>";
    echo "</select></div>";
    
    echo '<div class="misc-pub-section"><label for="akp_nofollow">Add "nofollow" to link?</label><input type="hidden" name="akp_nofollow" value="0" /><input type="checkbox" value="1" name="akp_nofollow" id="akp_nofollow"', $nofollow ? ' checked="checked"' : '', ' style="width: 70px;float: right;margin-top: -3px;" /></div>';
}

// Process the custom metabox fields
function akp_save_custom_fields( ) {
	global $post;	
        
        // verify nonce
        if (!isset($_POST['akp_meta_box_nonce']) || !wp_verify_nonce($_POST['akp_meta_box_nonce'], basename(__FILE__))) {
            return;
        }
	
	if( $_POST ) {
            if (isset($_POST['akp_remove_url']))
                update_post_meta( $post->ID, 'akp_remove_url', $_POST['akp_remove_url'] );
            else
                update_post_meta( $post->ID, 'akp_remove_url', 0 );
            
            if ($_POST['akp_expiry_date'] == 'never') update_post_meta( $post->ID, 'akp_expiry_date', $_POST['akp_expiry_date'] );
            else {
                update_post_meta( $post->ID, 'akp_expiry_date', strtotime($_POST['akp_expiry_date']) );
            }
            
            update_post_meta( $post->ID, 'akp_revenue_per_impression', $_POST['akp_revenue_per_impression'] );
            update_post_meta( $post->ID, 'akp_revenue_per_click', $_POST['akp_revenue_per_click'] );
            update_post_meta( $post->ID, 'akp_media_type', $_POST['akp_media_type'] );
            if (isset($_POST['akp_image_url']))
                update_post_meta( $post->ID, 'akp_image_url', $_POST['akp_image_url'] );
            update_post_meta( $post->ID, 'akp_image_alt', $_POST['akp_image_alt'] );
            update_post_meta( $post->ID, 'akp_flash_url', $_POST['akp_flash_url'] );
            update_post_meta( $post->ID, 'akp_flash_width', $_POST['akp_flash_width'] );
            update_post_meta( $post->ID, 'akp_flash_height', $_POST['akp_flash_height'] );
            update_post_meta( $post->ID, 'akp_adsense_code', $_POST['akp_adsense_code'] );
            update_post_meta( $post->ID, 'akp_text', $_POST['akp_text'] );
            
            if (isset($_POST['akp_target']))
                update_post_meta( $post->ID, 'akp_target', $_POST['akp_target'] );
            if (isset($_POST['akp_nofollow']))
                update_post_meta( $post->ID, 'akp_nofollow', $_POST['akp_nofollow'] );
            
	}
}

add_action( 'save_post', 'akp_save_custom_fields' );

// Process the custom metabox fields
function akp_return_fields( $id = NULL ) {
	global $post;
        if (is_null($id)) $id = $post->ID;
	$output = array();
        $output['akp_remove_url'] = (get_post_meta( $id, 'akp_remove_url' ) ? get_post_meta( $id, 'akp_remove_url' ) : array(''));
        $output['akp_expiry_date'] = (get_post_meta( $id, 'akp_expiry_date' ) ? get_post_meta( $id, 'akp_expiry_date' ) : 'never');
        $output['akp_revenue_per_impression'] = (get_post_meta( $id, 'akp_revenue_per_impression' ) ? get_post_meta( $id, 'akp_revenue_per_impression' ) : array(''));
        $output['akp_revenue_per_click'] = (get_post_meta( $id, 'akp_revenue_per_click' ) ? get_post_meta( $id, 'akp_revenue_per_click' ) : array(''));
        $output['akp_media_type'] = (get_post_meta( $id, 'akp_media_type' ) ? get_post_meta( $id, 'akp_media_type' ) : array(''));
        $output['akp_image_url'] = (get_post_meta( $id, 'akp_image_url' ) ? get_post_meta( $id, 'akp_image_url' ) : array(''));
        $output['akp_image_alt'] = (get_post_meta( $id, 'akp_image_alt' ) ? get_post_meta( $id, 'akp_image_alt' ) : array(''));
        $output['akp_flash_url'] = (get_post_meta( $id, 'akp_flash_url' ) ? get_post_meta( $id, 'akp_flash_url' ) : array(''));
        $output['akp_flash_width'] = (get_post_meta( $id, 'akp_flash_width' ) ? get_post_meta( $id, 'akp_flash_width' ) : array(''));
        $output['akp_flash_height'] = (get_post_meta( $id, 'akp_flash_height' ) ? get_post_meta( $id, 'akp_flash_height' ) : array(''));
        $output['akp_adsense_code'] = (get_post_meta( $id, 'akp_adsense_code' ) ? get_post_meta( $id, 'akp_adsense_code' ) : array(''));
        $output['akp_text'] = (get_post_meta( $id, 'akp_text' ) ? get_post_meta( $id, 'akp_text' ) : array(''));
        $output['akp_target'] = (get_post_meta( $id, 'akp_target' ) ? get_post_meta( $id, 'akp_target' ) : array('blank'));
        $output['akp_nofollow'] = (get_post_meta( $id, 'akp_nofollow' ) ? get_post_meta( $id, 'akp_nofollow' ) : array('0'));
        
        return $output;
}

// Remove the Permalinks
function akp_perm($return, $id, $new_title, $new_slug){
    global $post;
    if(isset($post->post_type) && $post->post_type == 'adverts_posts') return '';
    return $return;
}
add_filter('get_sample_permalink_html', 'akp_perm', '', 4);

// Change text labels
function akp_swap_featured_image_metabox($translation, $text, $domain) {
	global $post;
	$translations = get_translations_for_domain( $domain);
        if (isset($post->post_type)) {
            switch( $post->post_type ){
                case 'adverts_posts':
                    if ( $text == 'Set featured image')
                        return $translations->translate( 'Set Advert Image' );
                    if ( $text == 'Remove featured image')
                        return $translations->translate( 'Remove Advert Image' );
                    break;
            }
        }
	return $translation;
}
add_filter('gettext', 'akp_swap_featured_image_metabox', 10, 4);

// Register Advert types taxonomy
 function akp_taxonomies() {
       register_taxonomy(
        'advert_types',
        'adverts_posts',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name'=>'Advert Types',
                'singular_name'=>'Advert Type',
                'all_items'=>'All Advert Types',
                'edit_item'=>'Edit Advert Type',
                'update_item'=>'Update Advert Type',
                'add_new_item'=>'Add New Advert Type',
                'new_item_name'=>'New Advert Type',
                'search_items'=>'Search Advert Types',
            ),
            'query_var' => true,
            'rewrite' => array('slug' => 'adverts_slug')
        )
    );
}
add_action( 'init', 'akp_taxonomies' );

// Columns in custom post types
function akp_edit_adverts_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'banner_id' => __( 'Banner ID' ),
        'impressions' => __( 'Impressions' ),
        'clicks' => __( 'Clicks' ),
        'title' => __( 'URL' ),
        'advert_type' => __( 'Advert Type'),
        'advert_image' => __( 'Advert Image'),
        'date' => __( 'Date' ),
    );

    return $columns;
}
add_filter( 'manage_edit-adverts_posts_columns', 'akp_edit_adverts_columns' ) ;

// Update column data with custom data
function akp_columns($column_name, $ID) {
    switch ($column_name) {
        case 'advert_type' :
            $terms = get_the_terms( $ID, 'advert_types' );
            if ( !empty( $terms ) ) {
                $out = array();
                foreach ( $terms as $term ) {
                    $out[] = sprintf( '<a href="%s">%s</a>',
                        esc_url( add_query_arg( array( 'post_type' => 'adverts_posts', 'advert_type' => $term->slug ), 'edit.php' ) ),
                        esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'genre', 'display' ) )
                    );
                }
                echo join( ', ', $out );
            } else {
                echo 'No Advert Types Assigned';
            }
            break;
            
        case 'advert_image' :
            $post_featured_image = akp_get_featured_image($ID);
            if ($post_featured_image) {
                echo '<img src="' . $post_featured_image . '" style="width: 300px;" />';
            }
            break;
            
        case 'banner_id' :
            echo $ID;
            break;
        
        case 'impressions' :
            global $wpdb;
            $revenue = get_post_meta($ID, 'akp_revenue_per_impression');
            if (!isset($revenue[0]) || $revenue[0] == '') $revenue[0] = '0.00';
            $sign = get_option('revenue_currency');
            $impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE post_id = '$ID'");
            echo $sign.$revenue[0]." x ".$impressions[0]->impressions;
            break;
        
        case 'clicks' :
            global $wpdb;
            $revenue = get_post_meta($ID, 'akp_revenue_per_click');
            if (!isset($revenue[0]) || $revenue[0] == '') $revenue[0] = '0.00';
            $sign = get_option('revenue_currency');
            $clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$ID'");
            echo $sign.$revenue[0]." x ".$clicks[0]->clicks;
            break;
    }
}
add_action('manage_adverts_posts_posts_custom_column', 'akp_columns', 10, 2); 

// GET FEATURED IMAGE
function akp_get_featured_image($post_ID, $thumb = 'custom_thumbnail') {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, $thumb);
        return $post_thumbnail_img[0];
    }
}

function akp_log_impression($post_id) {
    $timestamp = current_time('timestamp');
    $expire = strtotime(get_option('impression_expiry_time'), $timestamp);
    $ip_address = $_SERVER['REMOTE_ADDR'];
    global $wpdb;
    $ip = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."akp_impressions_expire WHERE ip_address = '$ip_address' AND post_id = '$post_id'");
    if ($ip != null) {
        if ($ip->expire < $timestamp) {
            $wpdb->query( $wpdb->prepare( 
                    "DELETE FROM ".$wpdb->prefix."akp_impressions_expire
                     WHERE post_id = %d
                     AND ip_address = %s
                    ",
                    $post_id, $ip_address
                    )
            );
            $wpdb->query( $wpdb->prepare( 
                    "INSERT INTO ".$wpdb->prefix."akp_impressions_log
                    ( post_id, ip_address, timestamp )
                    VALUES ( %d, %s, %d )", 
                    array(
                        $post_id, 
                        $ip_address, 
                        $timestamp
                    ) 
            ) );
            $wpdb->query( $wpdb->prepare( 
                    "INSERT INTO ".$wpdb->prefix."akp_impressions_expire
                    ( post_id, ip_address, expire )
                    VALUES ( %d, %s, %d )", 
                    array(
                        $post_id, 
                        $ip_address, 
                        $expire
                    ) 
            ) );
        }
    } else {
        $wpdb->query( $wpdb->prepare( 
                "INSERT INTO ".$wpdb->prefix."akp_impressions_log
                ( post_id, ip_address, timestamp )
                VALUES ( %d, %s, %d )", 
                array(
                    $post_id, 
                    $ip_address, 
                    $timestamp
                ) 
        ) );
        $wpdb->query( $wpdb->prepare( 
                "INSERT INTO ".$wpdb->prefix."akp_impressions_expire
                ( post_id, ip_address, expire )
                VALUES ( %d, %s, %d )", 
                array(
                    $post_id, 
                    $ip_address, 
                    $expire
                ) 
        ) );
    }
}
       
// Dashboard Widget

function akp_enqueue($hook) {
    
    wp_register_style( 'akp_jquery_ui', plugins_url('css/jquery-ui.css', dirname(__FILE__)), false, '1.9.2' );
    wp_register_style( 'akp_css', plugins_url('css/adkingpro-styles.css', dirname(__FILE__)), false, '1.0.0' );
    
    wp_enqueue_style('akp_jquery_ui');
    wp_enqueue_style( 'akp_css' );
        
    wp_enqueue_script( 'jquery-ui-datepicker');
    wp_register_script('akp_admin_js', plugins_url( '/js/adkingpro-admin-functions.js', dirname(__FILE__) ), array('jquery', 'jquery-ui-datepicker'), '1.0.0');
    wp_enqueue_script( 'akp_admin_js');

    // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script( 'akp_admin_js', 'akp_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'akp_ajaxnonce' => wp_create_nonce( 'akpN0nc3' ) ) );
}
add_action( 'admin_enqueue_scripts', 'akp_enqueue' );

function akp_dashboard() {
    global $wpdb;
    $advert_types = get_terms('advert_types');
    $count = count($advert_types);
    if ($count > 0) {
        foreach ($advert_types as $type) {
            echo "<h3>$type->name</h3>";
            query_posts(array(
                'post_type'=>'adverts_posts',
                'advert_types'=>$type->slug
                ));
            echo "<ul>";
            while (have_posts()) : the_post();
                $post_id = get_the_ID();
                $image = akp_get_featured_image($post_id);
                
                // Get All Time Click Count
                $all_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$post_id'");
                $all_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE post_id = '$post_id'");
                
                // Get This Month Click Count
                $month_start = mktime(0, 0, 0, date('n', current_time('timestamp')), 1, date('Y', current_time('timestamp')));
                $month_end = mktime(23, 59, 59, date('n', current_time('timestamp')), date('t', current_time('timestamp')), date('Y', current_time('timestamp')));
                $month_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$month_start' AND '$month_end' AND post_id = '$post_id'");
                $month_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$month_start' AND '$month_end' AND post_id = '$post_id'");
                
                // Get This Week click count
                $start_week = get_option('week_starts');
                if (strtolower(date('l', current_time('timestamp'))) == $start_week) {
                    $day = date('j', current_time('timestamp'));
                    $month = date('n', current_time('timestamp'));
                    $year = date('Y', current_time('timestamp'));
                } else {
                    $day = date('j', strtotime('last '.$start_week));
                    $month = date('n', strtotime('last '.$start_week));
                    $year = date('Y', strtotime('last '.$start_week));
                }
                $week_start = mktime(0, 0, 0, $month, $day, $year);
                $week_end = mktime(23, 59, 59, date('n', strtotime("+7 days", $week_start)), date('j', strtotime("+7 days", $week_start)), date('Y', strtotime("+7 days", $week_start)));
                $week_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$week_start' AND '$week_end' AND post_id = '$post_id'");
                $week_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$week_start' AND '$week_end' AND post_id = '$post_id'");
                
                // Get Today Click count
                $today_start = mktime(0, 0, 0, date('n', current_time('timestamp')), date('j', current_time('timestamp')), date('Y', current_time('timestamp')));
                $today_end = mktime(23, 59, 59, date('n', current_time('timestamp')), date('j', current_time('timestamp')), date('Y', current_time('timestamp')));
                $today_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$today_start' AND '$today_end' AND post_id = '$post_id'");
                $today_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$today_start' AND '$today_end' AND post_id = '$post_id'");

                ?>
                <li class="banner_stat">
                    <div class="banner">
                        <a href='<?= admin_url("post.php?post=".$post_id."&action=edit") ?>'><img src='<?= $image ?>' /></a>
                    </div>
                    <div class='stats'>
                        <div class='stat'><h4>All Time</h4><span title="Impressions: <?= $all_impressions[0]->impressions ?>" alt="Impressions: <?= $all_impressions[0]->impressions ?>"><?= $all_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4>This Month</h4><span title="<?= $month_impressions[0]->impressions ?>" alt="<?= $month_impressions[0]->impressions ?>"><?= $month_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4>This Week</h4><span title="<?= $week_impressions[0]->impressions ?>" alt="<?= $week_impressions[0]->impressions ?>"><?= $week_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4>Today</h4><span title="<?= $today_impressions[0]->impressions ?>" alt="<?= $today_impressions[0]->impressions ?>"><?= $today_clicks[0]->clicks ?></span></div>
                    </div>
                    <br style="clear: both;" />
                </li>
                <?php 
            endwhile;
            echo "</ul>";
            wp_reset_query();
        }
    } else {
        // No advert types set
    }
} 

function akp_add_dashboard_widgets() {
	wp_add_dashboard_widget('akp_dashboard_widget', 'Ad King Pro - Banner Stats Summary', 'akp_dashboard');	
} 
add_action('wp_dashboard_setup', 'akp_add_dashboard_widgets' );

// Add settings area
function adkingpro_settings() {
	add_options_page('Ad King Pro', 'Ad King Pro', 'manage_options', 'adkingpro', 'akp_settings_output');
        add_dashboard_page('Ad King Pro Detailed Stats', 'Ad King Pro Stats', 'read', 'akp-detailed-stats', 'akp_detailed_output');
}
add_action('admin_menu', 'adkingpro_settings');

function akp_settings_output() {
	?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Ad King Pro</h2>
<div class="akp_settings">
<form method="post" action="options.php">
<?php settings_fields('akp-options'); ?>
<?php do_settings_sections('akp-options'); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Click Expiry Time Length (per IP)</th>
        <td>
            <?php $expiry = get_option('expiry_time'); ?>
            <select name="expiry_time">
                <option value="+0 hours"<?php if ($expiry == "+0 hours") : ?> selected<?php endif; ?>>None</option>
                <option value="+1 hour"<?php if ($expiry == "+1 hours") : ?> selected<?php endif; ?>>1 Hour</option>
                <option value="+2 hours"<?php if ($expiry == "+2 hours") : ?> selected<?php endif; ?>>2 Hours</option>
                <option value="+4 hours"<?php if ($expiry == "+4 hours") : ?> selected<?php endif; ?>>4 Hours</option>
                <option value="+6 hours"<?php if ($expiry == "+6 hours") : ?> selected<?php endif; ?>>6 Hours</option>
                <option value="+8 hours"<?php if ($expiry == "+8 hours") : ?> selected<?php endif; ?>>8 Hours</option>
                <option value="+10 hours"<?php if ($expiry == "+10 hours") : ?> selected<?php endif; ?>>10 Hours</option>
                <option value="+16 hours"<?php if ($expiry == "+16 hours") : ?> selected<?php endif; ?>>16 Hours</option>
                <option value="+24 hours"<?php if ($expiry == "+24 hours") : ?> selected<?php endif; ?>>24 Hours</option>
            </select>
        </td>
        <td></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Impression Expiry Time Length (per IP)</th>
        <td>
            <?php $expiry = get_option('impression_expiry_time'); ?>
            <select name="impression_expiry_time">
                <option value="+0 hours"<?php if ($expiry == "+0 hours") : ?> selected<?php endif; ?>>None</option>
                <option value="+1 hour"<?php if ($expiry == "+1 hours") : ?> selected<?php endif; ?>>1 Hour</option>
                <option value="+2 hours"<?php if ($expiry == "+2 hours") : ?> selected<?php endif; ?>>2 Hours</option>
                <option value="+4 hours"<?php if ($expiry == "+4 hours") : ?> selected<?php endif; ?>>4 Hours</option>
                <option value="+6 hours"<?php if ($expiry == "+6 hours") : ?> selected<?php endif; ?>>6 Hours</option>
                <option value="+8 hours"<?php if ($expiry == "+8 hours") : ?> selected<?php endif; ?>>8 Hours</option>
                <option value="+10 hours"<?php if ($expiry == "+10 hours") : ?> selected<?php endif; ?>>10 Hours</option>
                <option value="+16 hours"<?php if ($expiry == "+16 hours") : ?> selected<?php endif; ?>>16 Hours</option>
                <option value="+24 hours"<?php if ($expiry == "+24 hours") : ?> selected<?php endif; ?>>24 Hours</option>
            </select>
        </td>
        <td></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Week starts* (for stats)</th>
        <td>
            <?php $start = get_option('week_starts'); ?>
            <select name="week_starts">
                <option value="monday"<?php if ($start == "monday") : ?> selected<?php endif; ?>>Monday</option>
                <option value="tuesday"<?php if ($start == "tuesday") : ?> selected<?php endif; ?>>Tuesday</option>
                <option value="wednesday"<?php if ($start == "wednesday") : ?> selected<?php endif; ?>>Wednesday</option>
                <option value="thursday"<?php if ($start == "thursday") : ?> selected<?php endif; ?>>Thursday</option>
                <option value="friday"<?php if ($start == "friday") : ?> selected<?php endif; ?>>Friday</option>
                <option value="saturday"<?php if ($start == "saturday") : ?> selected<?php endif; ?>>Saturday</option>
                <option value="sunday"<?php if ($start == "sunday") : ?> selected<?php endif; ?>>Sunday</option>
            </select>
        </td>
        <td>* Week starts at midnight on the day chosen.</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Revenue Currency Sign</th>
        <td>
            <?php $sign = get_option('revenue_currency'); ?>
            <input type="text" name="revenue_currency" value="<?= $sign ?>" />
        </td>
        <td>* This sign will be used throughout the reporting section</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">PDF Theme</th>
        <td>
            <?php $theme = get_option('pdf_theme'); ?>
            <select name="pdf_theme">
                <?php
                    $folder = scandir(str_replace("includes/","",plugin_dir_path(__FILE__)).'themes/');
                    $exclude = array('.', '..');
                    foreach ($folder as $f) {
                        if (!in_array($f, $exclude)) {
                            $selected = '';
                            if ($theme == $f) $selected = ' selected';
                            echo '<option value="'.$f.'"'.$selected.'>'.ucwords(str_replace(array('-', '_'), ' ', $f)).'</option>';
                        }
                    }
                ?>
            </select>
        </td>
        <td>* More themes can be downloaded from the <a href="http://kingpro.me/plugins/ad-king-pro/themes/" target="_blank">KIng Pro Plugins website</a></td>
        </tr>
    </table>
<?php submit_button(); ?>
</form>
</div>
<div class="akp_faq_help">
    <div class="akp_faq">
        <h2>Connect</h2>
        <h3><a href="https://www.facebook.com/KingProPlugins" target="_blank">Follow on Facebook</a></h3>
        <h3><a href="https://twitter.com/KingProPlugins" target="_blank">Follow on Twitter</a></h3>
        <h3><a href="https://plus.google.com/b/101488033905569308183/101488033905569308183/about" target="_blank">Follow on Google+</a></h3>
        <h4>Found an issue? Post your issue on the <a href="http://wordpress.org/support/plugin/adkingpro" target="_blank">support forums</a>. If you would prefer, please email your concern to <a href="mailto:plugins@kingpro.me">plugins@kingpro.me</a></h4>
        <h2>How To</h2>
        <h3>Use Shortcodes</h3>
        <p>Shortcodes can be used in any page or post on your site. By default:</p>
        <pre>[adkingpro]</pre>
        <p>is defaulting to the advert type 'Sidebar' and randomly chosing from that. You can define your own advert type and display the adverts attached to that type by:</p>
        <pre>[adkingpro type='your-advert-type-slug']</pre>
        <p>Alternatively, you can display a single advert by entering its "Banner ID" which can be found in the table under the Adverts section:</p>
        <pre>[adkingpro banner='{banner_id}']</pre>
        <p>Have a select few adverts that you'd like to show? No problem, just specify the ids separated by commas:</p>
        <pre>[adkingpro banner='{banner_id1}, {banner_id2}']</pre>
        <p>Want to output a few adverts at once? Use the 'render' option in the shortcode:</p>
        <pre>[adkingpro banner='{banner_id1}, {banner_id2}' render='2']</pre>
        <pre>[adkingpro type='your-advert-type-slug' render='2']</pre>
        <p>Only have a small space and what a few adverts to display? Turn on the auto rotating slideshow!:</p>
        <pre>[adkingpro type="your-advert-type-slug" rotate='true']</pre>
        <p>There are also some settings you can play with to get it just right:</p>
        <ul>
            <li>Effect: "fade | slideLeft | none" Default - fade</li>
            <li>Pause Speed: "Time in ms" Default - 5000 (5s)</li>
            <li>Change Speed: "Time in ms" Default - 600 (0.6s)</li>
        </ul>
        <p>Use one or all of these settings:</p>
        <pre>[adkingpro rotate='true' effect='fade' speed='5000' changespeed='600']</pre>
        <p>To add this into a template, just use the "do_shortcode" function:</p>
        <pre>&lt;?php 
    if (function_exists('adkingpro_func'))
        echo do_shortcode("[adkingpro type='sidebar']");
?&gt;</pre>
        <h3>Install PDF Themes</h3>
        <p>Download themes from the <a href="http://kingpro.me/plugins/ad-king-pro/themes/" target="_blank">King Pro Plugins page</a>. Locate the themes folder in the adkingpro plugin folder, generally located:</p>
        <pre>/wp-content/plugins/adkingpro/themes/</pre>
        <p>Unzip the downloaded zip file and upload the entire folder into the themes folder mentioned above.</p>
        <p>Once uploaded, return to this page and your theme will be present in the PDF Theme dropdown to the left. Choose the theme and save the options. Next time you generate a report, the theme you have chosen will be used.</p>
        <p>The ability to upload the zip file straight from here will be added soon</p>
    </div>
    
    <div class="akp_help">
        <h2>FAQ</h2>
        <h4>Q. After activating this plugin, my site has broken! Why?</h4>
        <p>Nine times out of ten it will be due to your own scripts being added above the standard area where all the plugins are included. 
            If you move your javascript files below the function, "wp_head()" in the "header.php" file of your theme, it should fix your problem.</p>
        <h4>Q. I want to track clicks on a banner that scrolls to or opens a flyout div on my site. Is it possible?</h4>
        <p>Yes. Enter a '#' in as the URL for the banner when setting it up. At output, the banner is given a number of classes to allow for styling, one being "banner{banner_id}",
            where you would replace the "{banner_id}" for the number in the required adverts class.
            Use this in a jquery click event and prevent the default action of the click to make it do the action you require:</p>
        <pre>$(".adkingprobanner.banner{banner_id}").click(
        function(e) {
        &nbsp;&nbsp;&nbsp;&nbsp;e.preventDefault();
        &nbsp;&nbsp;&nbsp;&nbsp;// Your action here
        });</pre>
        <h4>I get an error saying the PDF can't be saved due to write permissions on the server. What do I do?</h4>
        <p>The plugin needs your permission to save the PDFs you generate to the output folder in the plugins folder. To do this, you are required to
        update the outputs permissions to be writable. Please see <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank">the wordpress help page</a> to carry this out.</p>
        <br />
        <h4>Found an issue? Post your issue on the <a href="http://wordpress.org/support/plugin/adkingpro" target="_blank">support forums</a>. If you would prefer, please email your concern to <a href="mailto:plugins@kingpro.me">plugins@kingpro.me</a></h4>
    </div>
</div>
</div>
<?php }

function akp_detailed_output() {
	?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Ad King Pro Detailed Stats</h2>
<div class="akp_detailed_stats">
    <?php
    global $wpdb;
    
    query_posts(array(
        'post_type'=>'adverts_posts'
        ));
    $currency_sign = get_option('revenue_currency');
    while (have_posts()) : the_post();
        $post_id = get_the_ID();
        $image = akp_get_featured_image($post_id);
        $dets = akp_return_fields($post_id);
        
        // Get All Time Click Count
        $all_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$post_id'");
        $all_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE post_id = '$post_id'");
        
        $all_impression_cost = (is_numeric($dets['akp_revenue_per_impression'][0])) ? $dets['akp_revenue_per_impression'][0] : '0.00';
        $all_click_cost = (is_numeric($dets['akp_revenue_per_click'][0])) ? $dets['akp_revenue_per_click'][0] : '0.00';
        
        $all_per_impression = $currency_sign.number_format($all_impression_cost, 2);
        $all_impression_total = $all_impression_cost * $all_impressions[0]->impressions;
        $all_impression_total_output = $currency_sign.number_format($all_impression_total, 2);
        
        $all_per_click = $currency_sign.number_format($all_click_cost, 2);
        $all_click_total = $all_click_cost * $all_clicks[0]->clicks;
        $all_click_total_output = $currency_sign.number_format($all_click_total, 2);
        
        $all_total_made = $all_impression_total + $all_click_total;
        $all_total_made_output = $currency_sign.number_format($all_total_made, 2);

        // Get This Month Click Count
        $month_start = mktime(0, 0, 0, date('n', current_time('timestamp')), 1, date('Y', current_time('timestamp')));
        $month_end = mktime(23, 59, 59, date('n', current_time('timestamp')), date('t', current_time('timestamp')), date('Y', current_time('timestamp')));
        $month_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$month_start' AND '$month_end' AND post_id = '$post_id'");
        $month_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$month_start' AND '$month_end' AND post_id = '$post_id'");
        
        $month_impression_cost = (is_numeric($dets['akp_revenue_per_impression'][0])) ? $dets['akp_revenue_per_impression'][0] : '0.00';
        $month_click_cost = (is_numeric($dets['akp_revenue_per_click'][0])) ? $dets['akp_revenue_per_click'][0] : '0.00';
        
        $month_per_impression = $currency_sign.number_format($month_impression_cost, 2);
        $month_impression_total = $month_impression_cost * $month_impressions[0]->impressions;
        $month_impression_total_output = $currency_sign.number_format($month_impression_total, 2);
        
        $month_per_click = $currency_sign.number_format($month_click_cost, 2);
        $month_click_total = $month_click_cost * $month_clicks[0]->clicks;
        $month_click_total_output = $currency_sign.number_format($month_click_total, 2);
        
        $month_total_made = $month_impression_total + $month_click_total;
        $month_total_made_output = $currency_sign.number_format($month_total_made, 2);

        // Get This Week click count
        $start_week = get_option('week_starts');
        if (strtolower(date('l', current_time('timestamp'))) == $start_week) {
            $day = date('j', current_time('timestamp'));
            $month = date('n', current_time('timestamp'));
            $year = date('Y', current_time('timestamp'));
        } else {
            $day = date('j', strtotime('last '.$start_week));
            $month = date('n', strtotime('last '.$start_week));
            $year = date('Y', strtotime('last '.$start_week));
        }
        $week_start = mktime(0, 0, 0, $month, $day, $year);
        $week_end = mktime(23, 59, 59, date('n', strtotime("+7 days", $week_start)), date('j', strtotime("+7 days", $week_start)), date('Y', strtotime("+7 days", $week_start)));
        $week_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$week_start' AND '$week_end' AND post_id = '$post_id'");
        $week_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$week_start' AND '$week_end' AND post_id = '$post_id'");

        $week_impression_cost = (is_numeric($dets['akp_revenue_per_impression'][0])) ? $dets['akp_revenue_per_impression'][0] : '0.00';
        $week_click_cost = (is_numeric($dets['akp_revenue_per_click'][0])) ? $dets['akp_revenue_per_click'][0] : '0.00';
        
        $week_per_impression = $currency_sign.number_format($week_impression_cost, 2);
        $week_impression_total = $week_impression_cost * $week_impressions[0]->impressions;
        $week_impression_total_output = $currency_sign.number_format($week_impression_total, 2);
        
        $week_per_click = $currency_sign.number_format($week_click_cost, 2);
        $week_click_total = $week_click_cost * $week_clicks[0]->clicks;
        $week_click_total_output = $currency_sign.number_format($week_click_total, 2);
        
        $week_total_made = $week_impression_total + $week_click_total;
        $week_total_made_output = $currency_sign.number_format($week_total_made, 2);
        
        // Get Today Click count
        $today_start = mktime(0, 0, 0, date('n', current_time('timestamp')), date('j', current_time('timestamp')), date('Y', current_time('timestamp')));
        $today_end = mktime(23, 59, 59, date('n', current_time('timestamp')), date('j', current_time('timestamp')), date('Y', current_time('timestamp')));
        $today_clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$today_start' AND '$today_end' AND post_id = '$post_id'");
        $today_impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE timestamp BETWEEN '$today_start' AND '$today_end' AND post_id = '$post_id'");
        
        $today_impression_cost = (is_numeric($dets['akp_revenue_per_impression'][0])) ? $dets['akp_revenue_per_impression'][0] : '0.00';
        $today_click_cost = (is_numeric($dets['akp_revenue_per_click'][0])) ? $dets['akp_revenue_per_click'][0] : '0.00';
        
        $today_per_impression = $currency_sign.number_format($today_impression_cost, 2);
        $today_impression_total = $today_impression_cost * $today_impressions[0]->impressions;
        $today_impression_total_output = $currency_sign.number_format($today_impression_total, 2);
        
        $today_per_click = $currency_sign.number_format($today_click_cost, 2);
        $today_click_total = $today_click_cost * $today_clicks[0]->clicks;
        $today_click_total_output = $currency_sign.number_format($today_click_total, 2);
        
        $today_total_made = $today_impression_total + $today_click_total;
        $today_total_made_output = $currency_sign.number_format($today_total_made, 2);
        
        // Initilize Detail log
        $all_clicks_detailed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$post_id' ORDER BY timestamp DESC");
        $month_clicks_detailed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$month_start' AND '$month_end' AND post_id = '$post_id' ORDER BY timestamp DESC");
        $week_clicks_detailed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$week_start' AND '$week_end' AND post_id = '$post_id' ORDER BY timestamp DESC");
        $day_clicks_detailed = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."akp_click_log WHERE timestamp BETWEEN '$today_start' AND '$today_end' AND post_id = '$post_id' ORDER BY timestamp DESC");

        ?>
        <div class="banner_detailed_stat">
            <div class="banner">
                <a href='<?= admin_url("post.php?post=".$post_id."&action=edit") ?>'><img src='<?= $image ?>' /></a><h3><?php the_title(); ?></h3>
            </div>
            <div class='stats'>
                <h2>Summary</h2>
                <div class='stat'><h4>All Time</h4><span title="Impressions: <?= $all_impressions[0]->impressions ?>" alt="Impressions: <?= $all_impressions[0]->impressions ?>"><?= $all_clicks[0]->clicks ?></span></div>
                <div class='stat'><h4>This Month</h4><span title="Impressions: <?= $month_impressions[0]->impressions ?>" alt="Impressions: <?= $month_impressions[0]->impressions ?>"><?= $month_clicks[0]->clicks ?></span></div>
                <div class='stat'><h4>This Week</h4><span title="Impressions: <?= $week_impressions[0]->impressions ?>" alt="Impressions: <?= $week_impressions[0]->impressions ?>"><?= $week_clicks[0]->clicks ?></span></div>
                <div class='stat'><h4>Today</h4><span title="Impressions: <?= $today_impressions[0]->impressions ?>" alt="Impressions: <?= $today_impressions[0]->impressions ?>"><?= $today_clicks[0]->clicks ?></span></div>
            </div>
            <div class='detailed'>
                <h2>Detailed</h2>
                <div class="detailed_menu">
                    <a class="active akp_detailed" rel="all">View all clicks</a>
                    <a class="akp_detailed" rel="month">View this month clicks</a>
                    <a class="akp_detailed" rel="week">View this week clicks</a>
                    <a class="akp_detailed" rel="day">View todays clicks</a>
                    <a class="akp_detailed" rel="date">View date range clicks</a>
                </div>
                <div class="detailed_details">
                    <div class="akp_detailed_all_details" style="display: block;">
                        <br />
                        <table>
                            <tr>
                                <td></td>
                                <th class="center">Count</th>
                                <th class="center">Cost Per</th>
                                <th class="center">Total</th>
                            </tr>
                            <tr>
                                <td>Impressions</td>
                                <td class="center"><?= $all_impressions[0]->impressions ?></td>
                                <td class="right"><?= $all_per_impression ?></td>
                                <td class="right"><?= $all_impression_total_output ?></td>
                            </tr>
                            <tr>
                                <td>Clicks</td>
                                <td class="center"><?= $all_clicks[0]->clicks ?></td>
                                <td class="right"><?= $all_per_click ?></td>
                                <td class="right"><?= $all_click_total_output ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right bold">TOTAL</td>
                                <td class="right bold"><?= $all_total_made_output ?></td>
                            </tr>
                        </table>
                        <br />
                        <div class="akp_reporting">
                            <strong>Download report: </strong> <a class="akp_csv" rel="all/<?= $post_id ?>">CSV</a> <a class="akp_pdf" rel="all/<?= $post_id ?>">PDF</a>
                        </div>
                        <br />
                        <table>
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($all_clicks_detailed as $acd) : ?>
                            <tr>
                                <td><?= date('d/m/Y h:i:sa', $acd->timestamp) ?></td>
                                <td><?= $acd->ip_address ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="akp_detailed_month_details">
                        <br />
                        <table>
                            <tr>
                                <td></td>
                                <th class="center">Count</th>
                                <th class="center">Cost Per</th>
                                <th class="center">Total</th>
                            </tr>
                            <tr>
                                <td>Impressions</td>
                                <td class="center"><?= $month_impressions[0]->impressions ?></td>
                                <td class="right"><?= $month_per_impression ?></td>
                                <td class="right"><?= $month_impression_total_output ?></td>
                            </tr>
                            <tr>
                                <td>Clicks</td>
                                <td class="center"><?= $month_clicks[0]->clicks ?></td>
                                <td class="right"><?= $month_per_click ?></td>
                                <td class="right"><?= $month_click_total_output ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right bold">TOTAL</td>
                                <td class="right bold"><?= $month_total_made_output ?></td>
                            </tr>
                        </table>
                        <br />
                        <div class="akp_reporting">
                            <strong>Download report: </strong> <a class="akp_csv" rel="month/<?= $post_id ?>">CSV</a> <a class="akp_pdf" rel="month/<?= $post_id ?>">PDF</a>
                        </div>
                        <br />
                        <table>
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($month_clicks_detailed as $acd) : ?>
                            <tr>
                                <td><?= date('d/m/Y h:i:sa', $acd->timestamp) ?></td>
                                <td><?= $acd->ip_address ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="akp_detailed_week_details">
                        <br />
                        <table>
                            <tr>
                                <td></td>
                                <th class="center">Count</th>
                                <th class="center">Cost Per</th>
                                <th class="center">Total</th>
                            </tr>
                            <tr>
                                <td>Impressions</td>
                                <td class="center"><?= $week_impressions[0]->impressions ?></td>
                                <td class="right"><?= $week_per_impression ?></td>
                                <td class="right"><?= $week_impression_total_output ?></td>
                            </tr>
                            <tr>
                                <td>Clicks</td>
                                <td class="center"><?= $week_clicks[0]->clicks ?></td>
                                <td class="right"><?= $week_per_click ?></td>
                                <td class="right"><?= $week_click_total_output ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right bold">TOTAL</td>
                                <td class="right bold"><?= $week_total_made_output ?></td>
                            </tr>
                        </table>
                        <br />
                        <div class="akp_reporting">
                            <strong>Download report: </strong> <a class="akp_csv" rel="week/<?= $post_id ?>">CSV</a> <a class="akp_pdf" rel="week/<?= $post_id ?>">PDF</a>
                        </div>
                        <br />
                        <table>
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($week_clicks_detailed as $acd) : ?>
                            <tr>
                                <td><?= date('d/m/Y h:i:sa', $acd->timestamp) ?></td>
                                <td><?= $acd->ip_address ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="akp_detailed_day_details">
                        <br />
                        <table>
                            <tr>
                                <td></td>
                                <th class="center">Count</th>
                                <th class="center">Cost Per</th>
                                <th class="center">Total</th>
                            </tr>
                            <tr>
                                <td>Impressions</td>
                                <td class="center"><?= $today_impressions[0]->impressions ?></td>
                                <td class="right"><?= $today_per_impression ?></td>
                                <td class="right"><?= $today_impression_total_output ?></td>
                            </tr>
                            <tr>
                                <td>Clicks</td>
                                <td class="center"><?= $today_clicks[0]->clicks ?></td>
                                <td class="right"><?= $today_per_click ?></td>
                                <td class="right"><?= $today_click_total_output ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td class="right bold">TOTAL</td>
                                <td class="right bold"><?= $today_total_made_output ?></td>
                            </tr>
                        </table>
                        <br />
                        <div class="akp_reporting">
                            <strong>Download report: </strong> <a class="akp_csv" rel="today/<?= $post_id ?>">CSV</a> <a class="akp_pdf" rel="today/<?= $post_id ?>">PDF</a>
                        </div>
                        <br />
                        <table>
                            <thead>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($day_clicks_detailed as $acd) : ?>
                            <tr>
                                <td><?= date('d/m/Y h:i:sa', $acd->timestamp) ?></td>
                                <td><?= $acd->ip_address ?></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Date/Time</th>
                                    <th>IP Address</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="akp_detailed_date_details">
                        <div class="choose_custom_date">
                            <h4>Choose your date range:</h4>
                            <label>From: </label><input type="text" class="datepicker from_adkingpro_date" />
                            <label>To: </label><input type="text" class="datepicker to_adkingpro_date" />
                            <a class="akp_custom_date" rel="<?= $post_id ?>">Search</a>
                        </div>
                        <div class="returned_data">
                            
                        </div>
                    </div>
                </div>
            </div>
            <br style="clear: both;" />
        </div>
        <?php 
        endwhile;
        wp_reset_query();
        ?>
</div>
</div>
<?php } ?>