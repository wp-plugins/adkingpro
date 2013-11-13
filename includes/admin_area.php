<?php

function akp_check_page($hook) {
    global $current_screen;
    $akp_pages = array('dashboard_page_akp-detailed-stats', 'index.php', 'king-pro-plugins_page_adkingpro', "toplevel_page_kpp_menu");
    $pages_req = array('post.php', 'post-new.php', 'edit.php');
    
    if (in_array($hook, $akp_pages)) return true;
    if (in_array($hook, $pages_req) && $current_screen->post_type == 'adverts_posts') return true;
    return false;
}

// Check for capabilities and throw error if doesn't exist.
require_once(ABSPATH . 'wp-includes/pluggable.php');
if (!current_user_can('akp_edit_one')) {
    function akp_admin_notice() {
        ?>
        <div class="error">
            <p>Ad King Pro <?= __("capabilities haven't been added to the list which will prevent you from using Ad King Pro. <strong>Please deactivate and reactivate the plugin to add these</strong>.", 'akptext' ); ?></p>
        </div>
        <?php
    }
    add_action( 'admin_notices', 'akp_admin_notice' );
}

function register_akp_options() {
  register_setting( 'akp-options', 'expiry_time' );
  register_setting( 'akp-options', 'impression_expiry_time' );
  register_setting( 'akp-options', 'week_start' );
  register_setting( 'akp-options', 'revenue_currency' );
  register_setting( 'akp-options', 'pdf_theme' );
  register_setting( 'akp-options', 'akp_image_sizes' );
  register_setting( 'akp-options', 'akp_auth_role' );
}
add_action( 'admin_init', 'register_akp_options' );

// Default Options
add_option( 'expiry_time', '+6 hours' );
add_option( 'impression_expiry_time', '+0 hours' );
add_option( 'week_starts', 'monday' );
add_option( 'revenue_currency', '$' );
add_option( 'pdf_theme', 'default' );
add_option( 'akp_image_sizes', '' );
add_option( 'akp_auth_role', 'subscriber');

function akp_allowed_cap() {
    $role = get_option('akp_auth_role');
    $cap = 'akp_edit_one';
    switch ($role) {
        case 'administrator':
            $cap = 'akp_edit_five';
            break;

        case 'editor':
            $cap = 'akp_edit_four';
            break;

        case 'author':
            $cap = 'akp_edit_three';
            break;

        case 'contributor':
            $cap = 'akp_edit_two';
            break;

        case 'subscriber':
            $cap = 'akp_edit_one';
            break;
    }
    
    return $cap;
}

// Register Adverts
function akp_create_post_type() {
    
    $cap = akp_allowed_cap();
    
    register_post_type( 'adverts_posts',
        array(
            'labels' => array(
                'name' => __( 'Adverts', 'akptext' ),
                'singular_name' => __( 'Advert', 'akptext' ),
                'all_items'=>__( 'All Adverts', 'akptext' ),
                'edit_item'=>__( 'Edit Advert', 'akptext' ),
                'update_item'=>__( 'Update Advert', 'akptext' ),
                'add_new_item'=>__( 'Add New Advert', 'akptext' ),
                'new_item_name'=>__( 'New Advert', 'akptext' ),
            ),
            'capabilities' => array(
                'publish_posts' => $cap,
                'edit_posts' => $cap,
                'edit_others_posts' => $cap,
                'delete_posts' => $cap,
                'delete_others_posts' => $cap,
                'read_private_posts' => $cap,
                'edit_post' => $cap,
                'delete_post' => $cap,
                'read_post' => $cap,
            ),
            'public' => true,
            'exclude_from_search' => true,
            'menu_position' => 5,
            'supports' => array( 'title', 'thumbnail' )
        )
    );
    
    register_taxonomy(
        'advert_types',
        'adverts_posts',
        array(
            'hierarchical' => true,
            'labels' => array(
                'name'=>__('Advert Types', 'akptext' ),
                'singular_name'=>__('Advert Type', 'akptext' ),
                'all_items'=>__('All Advert Types', 'akptext' ),
                'edit_item'=>__('Edit Advert Type', 'akptext' ),
                'update_item'=>__('Update Advert Type', 'akptext' ),
                'add_new_item'=>__('Add New Advert Type', 'akptext' ),
                'new_item_name'=>__('New Advert Type', 'akptext' ),
                'search_items'=>__('Search Advert Types', 'akptext' ),
            ),
            'query_var' => true,
            'rewrite' => array('slug' => 'adverts_slug')
        )
    );
}
add_action( 'init', 'akp_create_post_type' );

// Styling for the custom post type icon
function wpt_akp_icons() {
    ?>
    <style type="text/css" media="screen">
        #toplevel_page_kpp_menu .wp-menu-image {
            background: url(<?= plugins_url('/images/kpp-icon_16x16_sat.png', dirname(__FILE__)) ?>) no-repeat center center !important;
        }
	#toplevel_page_kpp_menu:hover .wp-menu-image, #toplevel_page_kpp_menu.wp-has-current-submenu .wp-menu-image {
            background: url(<?= plugins_url('/images/kpp-icon_16x16.png', dirname(__FILE__)) ?>) no-repeat center center !important;
        }
	#icon-options-general.icon32-posts-kpp_menu, #icon-kpp_menu.icon32 {background: url(<?= plugins_url('/images/kpp-icon_32x32.png', dirname(__FILE__)) ?>) no-repeat;}
        
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
<?= __("Leave the below blank to use the full size of the image you upload.", 'akptext') ?>        
</td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_width"><?= __('Set width of advert', 'akptext'); ?></label></th>
<td>
<input type="text" name="Cat_meta[advert_width]" id="Cat_meta[advert_width]" size="5" style="width:20%;" value="<?php echo $cat_meta['advert_width'] ? $cat_meta['advert_width'] : ''; ?>" /><br />
            <span class="description"><?= __('If variable, please leave blank', 'akptext'); ?></span>
        </td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_height"><?= __('Set height of advert', 'akptext'); ?></label></th>
<td>
<input type="text" name="Cat_meta[advert_height]" id="Cat_meta[advert_height]" size="5" style="width:20%;" value="<?php echo $cat_meta['advert_height'] ? $cat_meta['advert_height'] : ''; ?>" /><br />
            <span class="description"><?= __('If variable, please leave blank'); ?></span>
        </td>
</tr>
<tr class="form-field">
<th scope="row" valign="top"><label for="advert_crop"><?= __('Hard crop image?', 'akptext'); ?></label></th>
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
        return $title = __('Advert URL ie http://kingpro.me/plugins/ad-king-pro', 'akptext');
    return $title;
}
add_filter( 'enter_title_here', 'akp_title_text_input' );

// Update Feature Image to become Advert Image
function akp_change_meta_boxes()
{
    add_meta_box('akpmediatype', __('Media Type', 'akptext'), 'akp_media_type', 'adverts_posts', 'normal', 'high');
    
    remove_meta_box( 'postimagediv', 'adverts_posts', 'side' );
    if (current_theme_supports('post-thumbnails')) {
        add_meta_box('postimagediv', __('Advert Image', 'akptext'), 'post_thumbnail_meta_box', 'adverts_posts', 'normal', 'high');
        add_meta_box('akpimageattrbox', __('Advert Image Attributes', 'akptext'), 'akp_image_attrs_box', 'adverts_posts', 'normal', 'high');
    } else 
        add_meta_box('akpimagebox', __('Advert Image', 'akptext'), 'akp_image_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akpflashbox', __('Advert Flash File', 'akptext'), 'akp_flash_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akpadsensebox', __('Advert AdSense Code', 'akptext'), 'akp_adsense_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('akptextbox', __('Advert Text', 'akptext'), 'akp_text_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('postremoveurllink', __('Remove Link from Advert?', 'akptext'), 'akp_remove_url_link', 'adverts_posts', 'advanced', 'high');
    add_meta_box('postclickstatsdiv', __('Advert Stats', 'akptext'), 'akp_post_click_stats', 'adverts_posts', 'advanced', 'low');
    add_meta_box('revenuevaluesdiv', __('Advert Revenue', 'akptext'), 'akp_revenue_values', 'adverts_posts', 'side', 'low');
    add_meta_box('linkoptionsdiv', __('Link Options', 'akptext'), 'akp_link_options', 'adverts_posts', 'side', 'low');
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
            '.__('Expire on', 'akptext').': <b>'.$expiry_output.'</b>
            </span>
            <a href="#edit_expiry" class="edit-expiry hide-if-no-js">'.__('Edit', 'akptext').'</a>
            <div id="expirydiv" class="hide-if-js"><div class="expiry-wrap"><select id="exp_m">
                            <option value="01"'.(($expiry_m == '01') ? ' selected="selected"' : '').'>'.__('01-Jan', 'akptext').'</option>
                            <option value="02"'.(($expiry_m == '02') ? ' selected="selected"' : '').'>'.__('02-Feb', 'akptext').'</option>
                            <option value="03"'.(($expiry_m == '03') ? ' selected="selected"' : '').'>'.__('03-Mar', 'akptext').'</option>
                            <option value="04"'.(($expiry_m == '04') ? ' selected="selected"' : '').'>'.__('04-Apr', 'akptext').'</option>
                            <option value="05"'.(($expiry_m == '05') ? ' selected="selected"' : '').'>'.__('05-May', 'akptext').'</option>
                            <option value="06"'.(($expiry_m == '06') ? ' selected="selected"' : '').'>'.__('06-Jun', 'akptext').'</option>
                            <option value="07"'.(($expiry_m == '07') ? ' selected="selected"' : '').'>'.__('07-Jul', 'akptext').'</option>
                            <option value="08"'.(($expiry_m == '08') ? ' selected="selected"' : '').'>'.__('08-Aug', 'akptext').'</option>
                            <option value="09"'.(($expiry_m == '09') ? ' selected="selected"' : '').'>'.__('09-Sep', 'akptext').'</option>
                            <option value="10"'.(($expiry_m == '10') ? ' selected="selected"' : '').'>'.__('10-Oct', 'akptext').'</option>
                            <option value="11"'.(($expiry_m == '11') ? ' selected="selected"' : '').'>'.__('11-Nov', 'akptext').'</option>
                            <option value="12"'.(($expiry_m == '12') ? ' selected="selected"' : '').'>'.__('12-Dec', 'akptext').'</option>
    </select><input type="text" id="exp_d" value="'.$expiry_d.'" size="2" maxlength="2" autocomplete="off">, <input type="text" id="exp_y" value="'.$expiry_y.'" size="4" maxlength="4" autocomplete="off"> @ <input type="text" id="exp_h" value="'.$expiry_h.'" size="2" maxlength="2" autocomplete="off"> : <input type="text" id="exp_i" value="'.$expiry_i.'" size="2" maxlength="2" autocomplete="off"></div><input type="hidden" id="exp_s" value="55">

    <input type="hidden" id="hidden_exp_m" value="'.$expiry_m.'">
    <input type="hidden" id="hidden_exp_d" value="'.$expiry_d.'">
    <input type="hidden" id="hidden_exp_y" value="'.$expiry_y.'">
    <input type="hidden" id="hidden_exp_h" value="'.$expiry_h.'">
    <input type="hidden" id="hidden_exp_i" value="'.$expiry_i.'">

    <input type="hidden" name="akp_expiry_date" id="akp_expiry_date" value="'.$expiry_value.'" />

    <p>
    <a href="#edit_expiry" class="save-expiry hide-if-no-js button">'.__('OK', 'akptext').'</a>
    <a href="#edit_expiry" class="cancel-expiry hide-if-no-js">'.__('Cancel', 'akptext').'</a>
    <a href="#edit_expiry" class="set-never-expiry hide-if-no-js button right">'.__('Set to Never', 'akptext').'</a>
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
    echo "<option value='image'>".__('Image', 'akptext')."</option>";
    echo "<option value='flash'".$flash.">".__('Flash', 'akptext')."</option>";
    echo "<option value='adsense'".$adsense.">".__('AdSense', 'akptext')."</option>";
    echo "<option value='text'".$text.">".__('Text', 'akptext')."</option>";
    echo "</select>";
}

function akp_image_box($object, $box) {
    global $post;
    $image_url = (get_post_meta( $post->ID, 'akp_image_url', true )) ? get_post_meta( $post->ID, 'akp_image_url', true ) : '';
    $image_alt = (get_post_meta( $post->ID, 'akp_image_alt', true )) ? get_post_meta( $post->ID, 'akp_image_alt', true ) : '';
    
    echo '<label for="akp_image_url">';
    echo '<input id="akp_image_url" type="text" size="36" name="akp_image_url" value="'.$image_url.'" />';
    echo '<input id="akp_image_url_button" class="button" type="button" value="'.__('Upload Image', 'akptext').'" />';
    echo '<br />'.__('Enter a URL or upload an image (You are seeing this box as you have disabled "post-thumbnails" support.)', 'akptext');
    echo '</label><br /><br />';
    echo '<label for="akp_image_alt">'.__('Banner description (this will be added to the alt tag on the image)', 'akptext').'</label>';
    echo '<br /><input id="akp_image_alt" type="text" size="36" name="akp_image_alt" value="'.$image_alt.'" />';
    echo '<br /><br />';
}

function akp_image_attrs_box($object, $box) {
    global $post;
    $image_alt = (get_post_meta( $post->ID, 'akp_image_alt', true )) ? get_post_meta( $post->ID, 'akp_image_alt', true ) : '';

    echo '<label for="akp_image_alt">'.__('Banner description (this will be added to the alt tag on the image)', 'akptext').'</label>';
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
    echo '<input id="akp_flash_url_button" class="button" type="button" value="'.__('Upload SWF File', 'akptext').'" />';
    echo '<br />'.__('Enter a URL or upload a SWF file', 'akptext');
    echo '</label><br /><br />';
    echo '<label for="akp_flash_width" style="width: 85px; display: block; float: left;">'.__('SWF Width', 'akptext').'</label><input type="text" name="akp_flash_width" value="'.$flash_width.'" style="width: 60px;" /><br />';
    echo '<label for="akp_flash_height" style="width: 85px; display: block; float: left;">'.__('SWF Height', 'akptext').'</label><input type="text" name="akp_flash_height" value="'.$flash_height.'" style="width: 60px;" /><br />';
}

function akp_adsense_box($object, $box) {
    global $post;
    $adsense_code = (get_post_meta( $post->ID, 'akp_adsense_code', true )) ? get_post_meta( $post->ID, 'akp_adsense_code', true ) : '';
    echo '<label for="akp_adsense_code">'.__('Enter the Ad Unit Code given from your Google AdSense account', 'akptext').'</label>';
    echo '<br /><textarea name="akp_adsense_code" style="width: 100%; height: 200px;">'.$adsense_code.'</textarea><br />';
    echo '<br /><strong>'.__('Please note that only impressions are tracked for these ads as the clicks are registers via AdSense', 'akptext').'</strong>';
}

function akp_text_box($object, $box) {
    global $post;
    $text = (get_post_meta( $post->ID, 'akp_text', true )) ? get_post_meta( $post->ID, 'akp_text', true ) : '';
    
    echo '<label for="akp_text">'.__('Enter the text you would like on the link that will be tracked', 'akptext').'</label>';
    echo '<br /><input type="text" name="akp_text" style="width: 100%;" value="'.$text.'" /><br />';
}

// Output stats for post
function akp_post_click_stats($object, $box) {
    global $wpdb, $post;
    $clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$object->ID'");
    echo __("This banner has had ", 'akptext').$clicks[0]->clicks.__(" since being published.", 'akptext')." <a href='".admin_url('/index.php?page=akp-detailed-stats')."'>".__("View Detailed Report", 'akptext')."</a>";
    echo '<br /><br /><a href="'.admin_url('admin.php?action=akpresetdata&post='.$post->ID).'" onclick="if(!confirm(\''.__("Are you sure you want to reset the tracking data back to 0? There is no reversing this action", 'akptext').'.\')){return false;}">'.__("Reset Tracking Data", 'akptext').'</a>';
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
    
    echo '<div class="misc-pub-section"><label for="akp_revenue_per_impression">'.__("Revenue Per Impression", "akptext").':</label>';
    echo '<input type="text" name="akp_revenue_per_impression" value="', $revenue_impression ? $revenue_impression : '0.00', '" style="width: 70px;float: right;margin-top: -3px;" />';
    echo '</div>';
    echo '<div class="misc-pub-section"><label for="akp_revenue_per_click">'.__("Revenue Per Click", 'akptext').':</label>';
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
    
    echo '<div class="misc-pub-section"><label for="akp_remove_url">'.__("Remove URL from link", 'akptext').':</label>';
    echo '<input type="checkbox" value="1" name="akp_remove_url" id="akp_remove_url"', $remove_url ? ' checked="checked"' : '', ' style="width: 70px;float: right;margin-top: -3px;" />';
    echo '</div>';
    
    echo "<div class='misc-pub-section'><label for='akp_target'>".__("Window Target", "akptext")."</label><select name='akp_target' style='width: 70px;float: right;margin-top: -3px;' >";
    echo "<option value='blank'>_blank</option>";
    echo "<option value='self'".$self.">_self</option>";
    echo "<option value='parent'".$parent.">_parent</option>";
    echo "<option value='top'".$top.">_top</option>";
    echo "<option value='none'".$none.">".__('none', 'akptext')."</option>";
    echo "</select></div>";
    
    echo '<div class="misc-pub-section"><label for="akp_nofollow">'.__('Add "nofollow" to link?', 'akptext').'</label><input type="hidden" name="akp_nofollow" value="0" /><input type="checkbox" value="1" name="akp_nofollow" id="akp_nofollow"', $nofollow ? ' checked="checked"' : '', ' style="width: 70px;float: right;margin-top: -3px;" /></div>';
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

// Columns in custom post types
function akp_edit_adverts_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'banner_id' => __( 'Banner ID', 'akptext' ),
        'impressions' => __( 'Impressions', 'akptext' ),
        'clicks' => __( 'Clicks', 'akptext' ),
        'title' => __( 'URL', 'akptext' ),
        'advert_type' => __( 'Advert Type', 'akptext'),
        'advert_image' => __( 'Advert Image', 'akptext'),
        'date' => __( 'Date', 'akptext' ),
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
                echo __('No Advert Types Assigned', 'akptext');
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
    if (akp_check_page($hook)) :
        wp_register_style( 'akp_jquery_ui', plugins_url('css/jquery-ui.css', dirname(__FILE__)), false, '1.9.2' );
        wp_register_style( 'akp_css', plugins_url('css/adkingpro-styles.css', dirname(__FILE__)), false, '1.0.0' );
        wp_register_style( 'fontawesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css', false, '3.2.1');

        wp_enqueue_style('akp_jquery_ui');
        wp_enqueue_style( 'fontawesome' );
        wp_enqueue_style( 'akp_css' );
        wp_enqueue_style( 'thickbox' );

        wp_enqueue_script( 'jquery-ui-datepicker');
        wp_register_script('akp_admin_js', plugins_url( '/js/adkingpro-admin-functions.js', dirname(__FILE__) ), array('jquery', 'jquery-ui-datepicker'), '1.0.0');
        wp_enqueue_script( 'akp_admin_js');
        wp_enqueue_script( 'thickbox' );

        // in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
        wp_localize_script( 'akp_admin_js', 'akp_ajax_object',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'akp_ajaxnonce' => wp_create_nonce( 'akpN0nc3' ) ) );
    endif;
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
                        <div class='stat'><h4><?= __('All Time', 'akptext') ?></h4><span title="<?= __('Impressions', 'akptext') ?>: <?= $all_impressions[0]->impressions ?>" alt="<?= __('Impressions', 'akptext') ?>: <?= $all_impressions[0]->impressions ?>"><?= $all_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __('This Month', 'akptext') ?></h4><span title="<?= $month_impressions[0]->impressions ?>" alt="<?= $month_impressions[0]->impressions ?>"><?= $month_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __('This Week', 'akptext') ?></h4><span title="<?= $week_impressions[0]->impressions ?>" alt="<?= $week_impressions[0]->impressions ?>"><?= $week_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __('Today', 'akptext') ?></h4><span title="<?= $today_impressions[0]->impressions ?>" alt="<?= $today_impressions[0]->impressions ?>"><?= $today_clicks[0]->clicks ?></span></div>
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

if (current_user_can(akp_allowed_cap())) {
    function akp_add_dashboard_widgets() {
            wp_add_dashboard_widget('akp_dashboard_widget', 'Ad King Pro - '.__('Banner Stats Summary', 'akptext'), 'akp_dashboard');	
    } 
    add_action('wp_dashboard_setup', 'akp_add_dashboard_widgets' );
}

// Add King Pro Plugins Section
if(!function_exists('find_kpp_menu_item')) {
  function find_kpp_menu_item($handle, $sub = false) {
    if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
      return false;
    }
    global $menu, $submenu;
    $check_menu = $sub ? $submenu : $menu;
    if(empty($check_menu)) {
      return false;
    }
    foreach($check_menu as $k => $item) {
      if($sub) {
        foreach($item as $sm) {
          if($handle == $sm[2]) {
            return true;
          }
        }
      } 
      else {
        if($handle == $item[2]) {
          return true;
        }
      }
    }
    return false;
  }
}

function akp_add_parent_page() {
  if(!find_kpp_menu_item('kpp_menu')) {
    add_menu_page('King Pro Plugins','King Pro Plugins', 'manage_options', 'kpp_menu', 'kpp_menu_page');
  }
//  if(!function_exists('remove_submenu_page')) {
//    unset($GLOBALS['submenu']['kpp_menu'][0]);
//  }
//  else {
//    remove_submenu_page('kpp_menu','kpp_menu');
//  }
  if (current_user_can(akp_allowed_cap())) {
    add_submenu_page('kpp_menu', 'Ad King Pro', 'Ad King Pro', 'manage_options', 'adkingpro', 'akp_settings_output');
    add_dashboard_page('Ad King Pro '.__('Detailed Stats', 'akptext'), 'Ad King Pro '.__('Stats', 'akptext'), 'read', 'akp-detailed-stats', 'akp_detailed_output');
  }
}
add_action('admin_menu', 'akp_add_parent_page');

if(!function_exists('kpp_menu_page')) {
    function kpp_menu_page() {
        include 'screens/kpp.php';
    }
}

function akp_settings_output() {
    include 'screens/settings.php';
}

function akp_detailed_output() {
    include 'screens/detailed.php';
} 
?>