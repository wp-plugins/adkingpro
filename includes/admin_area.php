<?php

// Default Options
add_option( 'expiry_time', '+6 hours' );
add_option( 'impression_expiry_time', '+0 hours' );
add_option( 'week_starts', 'monday' );
add_option( 'revenue_currency', '$' );
add_option( 'pdf_theme', 'default' );

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

function akp_widget_registration() {
    register_widget( 'AdKingPro_Widget' );
}

add_action( 'widgets_init', 'akp_widget_registration');

// Add scripts to page
function akp_my_scripts_method() {
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script(
        'adkingpro-js',
        plugins_url('js/adkingpro-functions.js', dirname(__FILE__)),
        array('jquery')
    );
    wp_localize_script( 'adkingpro-js', 'AkpAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'ajaxnonce' => wp_create_nonce( 'akpN0nc3' ), ) );
}
add_action('wp_enqueue_scripts', 'akp_my_scripts_method');

// Update title field to become URL field
function akp_title_text_input( $title ){
    global $post;
    if($post->post_type == 'adverts_posts') 
        return $title = 'Advert URL ie http://durham.net.au/wp-plugins/adkingpro';
    return $title;
}
add_filter( 'enter_title_here', 'akp_title_text_input' );

// Update Feature Image to become Advert Image
function akp_change_meta_boxes()
{
    remove_meta_box( 'postimagediv', 'adverts_posts', 'side' );
    add_meta_box('postimagediv', __('Advert Image'), 'post_thumbnail_meta_box', 'adverts_posts', 'normal', 'high');
    add_meta_box('postremoveurllink', __('Remove Link from Advert?'), 'akp_remove_url_link', 'adverts_posts', 'advanced', 'high');
    add_meta_box('postclickstatsdiv', __('Advert Stats'), 'akp_post_click_stats', 'adverts_posts', 'advanced', 'low');
    add_meta_box('revenuevaluesdiv', __('Advert Revenue'), 'akp_revenue_values', 'adverts_posts', 'side', 'low');
}
add_action('do_meta_boxes', 'akp_change_meta_boxes');

// Output stats for post
function akp_post_click_stats($object, $box) {
    global $wpdb;
    $clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$object->ID'");
    echo "This banner has had ".$clicks[0]->clicks." since being published. <a href='#'>View Detailed Report</a>";
}

// Add checkbox to remove URL Link off advert
function akp_remove_url_link($object, $box) {
    global $post;
    $remove_url = get_post_meta( $post->ID, 'akp_remove_url', true );
    // Use nonce for verification
    echo '<input type="hidden" name="akp_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
    echo '<input type="checkbox" value="1" name="akp_remove_url" id="akp_remove_url"', $remove_url ? ' checked="checked"' : '', ' />';
}

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

// Process the custom metabox fields
function akp_save_custom_fields( ) {
	global $post;	
        
        // verify nonce
        if (!wp_verify_nonce($_POST['akp_meta_box_nonce'], basename(__FILE__))) {
            return;
        }
	
	if( $_POST ) {
            
            if (isset($_POST['akp_remove_url']))
                update_post_meta( $post->ID, 'akp_remove_url', $_POST['akp_remove_url'] );
            else
                update_post_meta( $post->ID, 'akp_remove_url', 0 );
            
            update_post_meta( $post->ID, 'akp_revenue_per_impression', $_POST['akp_revenue_per_impression'] );
            update_post_meta( $post->ID, 'akp_revenue_per_click', $_POST['akp_revenue_per_click'] );
	}
}

add_action( 'save_post', 'akp_save_custom_fields' );

// Process the custom metabox fields
function akp_return_fields( $id = NULL ) {
	global $post;
        if (is_null($id)) $id = $post->ID;
	$output = array();
        $output['akp_remove_url'] = get_post_meta( $id, 'akp_remove_url' );
        $output['akp_revenue_per_impression'] = get_post_meta( $id, 'akp_revenue_per_impression' );
        $output['akp_revenue_per_click'] = get_post_meta( $id, 'akp_revenue_per_click' );
        
        return $output;
}

// Remove the Permalinks
function akp_perm($return, $id, $new_title, $new_slug){
    global $post;
    if($post->post_type == 'adverts_posts') return '';
    return $return;
}
add_filter('get_sample_permalink_html', 'akp_perm', '', 4);

// Change text labels
function akp_swap_featured_image_metabox($translation, $text, $domain) {
	global $post;
	$translations = get_translations_for_domain( $domain);
	switch( $post->post_type ){
            case 'adverts_posts':
                if ( $text == 'Set featured image')
                    return $translations->translate( 'Set Advert Image' );
                if ( $text == 'Remove featured image')
                    return $translations->translate( 'Remove Advert Image' );
                break;
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
            $post_featured_image = akp_get_featured_image($post_ID);
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
            if ($revenue[0] == '') $revenue[0] = '0.00';
            $sign = get_option('revenue_currency');
            $impressions = $wpdb->get_results("SELECT COUNT(*) as impressions FROM ".$wpdb->prefix."akp_impressions_log WHERE post_id = '$ID'");
            echo $sign.$revenue[0]." x ".$impressions[0]->impressions;
            break;
        
        case 'clicks' :
            global $wpdb;
            $revenue = get_post_meta($ID, 'akp_revenue_per_click');
            if ($revenue[0] == '') $revenue[0] = '0.00';
            $sign = get_option('revenue_currency');
            $clicks = $wpdb->get_results("SELECT COUNT(*) as clicks FROM ".$wpdb->prefix."akp_click_log WHERE post_id = '$ID'");
            echo $sign.$revenue[0]." x ".$clicks[0]->clicks;
            break;
    }
}
add_action('manage_adverts_posts_posts_custom_column', 'akp_columns', 10, 2); 

// GET FEATURED IMAGE
function akp_get_featured_image($post_ID) {
    $post_thumbnail_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_id) {
        $post_thumbnail_img = wp_get_attachment_image_src($post_thumbnail_id, 'custom_thumbnail');
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
function akp_admin_register_head() {
    ?>
    <link rel='stylesheet' type='text/css' href='http://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css' />
    <link rel='stylesheet' type='text/css' href='<?= plugins_url('css/adkingpro-styles.css', dirname(__FILE__)) ?>' />
    <?php
}
add_action('admin_head', 'akp_admin_register_head');

function akp_enqueue($hook) {
        
	wp_enqueue_script( 'jquery-ui', plugins_url( '/js/jquery-ui.js', dirname(__FILE__) ), array('jquery'));
        wp_enqueue_script( 'akp-admin', plugins_url( '/js/adkingpro-admin-functions.js', dirname(__FILE__) ), array('jquery', 'jquery-ui'));

	// in javascript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'akp-admin', 'akp_ajax_object',
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

function register_akp_options() {
  register_setting( 'akp-options', 'expiry_time' );
  register_setting( 'akp-options', 'impression_expiry_time' );
  register_setting( 'akp-options', 'week_start' );
  register_setting( 'akp-options', 'revenue_currency' );
  register_setting( 'akp-options', 'pdf_theme' );
}
add_action( 'admin_init', 'register_akp_options' );

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
        <td>* More themes can be downloaded from <a href="http://durham.net.au/wordpress/plugins/ad-king-pro/" target="_blank">my website</a></td>
        </tr>
    </table>
<?php submit_button(); ?>
</form>
</div>
<div class="akp_faq_help">
    <div class="akp_faq">
        <h2>How To</h2>
        <h3>Use Shortcodes</h3>
        <p>Shortcodes can be used in any page or post on your site. By default:</p>
        <pre>[adkingpro]</pre>
        <p>is defaulting to the advert type 'Sidebar' and randomly chosing from that. You can define your own advert type and display the adverts attached to that type by:</p>
        <pre>[adkingpro type="your-advert-type-slug"]</pre>
        <p>Alternatively, you can display a single advert by entering its "Banner ID" which can be found in the table under the Adverts section:</p>
        <pre>[adkingpro banner="{banner_id}"]</pre>
        <p>To add this into a template, just use the "do_shortcode" function:</p>
        <pre>&lt;?= do_shortcode("[adkingpro]"); ?&gt;</pre>
        <h3>Install PDF Themes</h3>
        <p>Download themes from <a href="http://durham.net.au/wordpress/plugins/ad-king-pro/" target="_blank">my plugin page</a>. Locate the themes folder in the adkingpro plugin folder, generally located:</p>
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
        <br />
        <h4>Found an issue? Please email your concern to <a href="mailto:contact@durham.net.au">contact@durham.net.au</a></h4>
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
        
        $all_impression_cost = $dets['akp_revenue_per_impression'][0];
        $all_click_cost = $dets['akp_revenue_per_click'][0];
        
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
        
        $month_impression_cost = $dets['akp_revenue_per_impression'][0];
        $month_click_cost = $dets['akp_revenue_per_click'][0];
        
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

        $week_impression_cost = $dets['akp_revenue_per_impression'][0];
        $week_click_cost = $dets['akp_revenue_per_click'][0];
        
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
        
        $today_impression_cost = $dets['akp_revenue_per_impression'][0];
        $today_click_cost = $dets['akp_revenue_per_click'][0];
        
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