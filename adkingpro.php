<?php
/*
    Plugin Name: Ad King Pro
    Plugin URI: http://kingpro.me/plugins/ad-king-pro/
    Description: Ad King Pro allows you to manage, display, document and report all of your custom advertising on your wordpress site.
    Version: 1.9.17
    Author: Ash Durham
    Author URI: http://durham.net.au/
    License: GPL2
    Text Domain: akptext

    Copyright 2013  Ash Durham  (email : plugins@kingpro.me)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    // INSTALL

    global $akp_db_version;
    $akp_db_version = "1.9.17";

    function akp_install() {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        
        global $wpdb;
        global $akp_db_version;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        akp_check_db_tables();

        $table_name = $wpdb->prefix . "terms";
        $sql = "INSERT INTO $table_name 
         (`name`, `slug`, `term_group`)
         VALUES ('Sidebar', 'sidebar', '0')";
        dbDelta($sql);

        $term_id = mysql_insert_id();
        $table_name = $wpdb->prefix . "term_taxonomy";
        $sql = "INSERT INTO $table_name 
         (`term_id`, `taxonomy`, `description`, `parent`, `count`)
         VALUES ('".$term_id."', 'advert_types', '', '0', '0')";
        dbDelta($sql);

        add_option("apk_db_version", $akp_db_version);

        // Register AKP capabilities to all users
        $role = get_role( 'subscriber' );
        $role->add_cap( 'akp_edit_one' ); 

        $role = get_role( 'contributor' );
        $role->add_cap( 'akp_edit_one' ); 
        $role->add_cap( 'akp_edit_two' );

        $role = get_role( 'author' );
        $role->add_cap( 'akp_edit_one' ); 
        $role->add_cap( 'akp_edit_two' );
        $role->add_cap( 'akp_edit_three' );

        $role = get_role( 'editor' );
        $role->add_cap( 'akp_edit_one' ); 
        $role->add_cap( 'akp_edit_two' );
        $role->add_cap( 'akp_edit_three' );
        $role->add_cap( 'akp_edit_four' );

        $role = get_role( 'administrator' );
        $role->add_cap( 'akp_edit_one' ); 
        $role->add_cap( 'akp_edit_two' );
        $role->add_cap( 'akp_edit_three' );
        $role->add_cap( 'akp_edit_four' );
        $role->add_cap( 'akp_edit_five' );
    }
    
    // Register hooks at activation
    register_activation_hook(__FILE__,'akp_install');
    
    // END INSTALL
    
    // DEACTIVATE
    
    function akp_deactivate() {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        check_admin_referer( "deactivate-plugin_{$plugin}" );
    }
    
    register_deactivation_hook( __FILE__, 'akp_deactivate' );
    
    // END DEACTIVATE
    
    function akp_languages_init() {
        load_plugin_textdomain('akptext', false, basename( dirname( __FILE__ ) ) . '/languages' );
    }
    add_action('plugins_loaded', 'akp_languages_init');
    
    if (get_option("apk_db_version") != $akp_db_version) {
        // Execute your upgrade logic here
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        akp_check_db_tables();

        // Then update the version value
        update_option("apk_db_version", $akp_db_version);
    }
    
    function akp_check_db_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . "akp_impressions_log";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             `post_id` int(11) NOT NULL,
             `ip_address` varchar(20) NOT NULL,
             `timestamp` int(11) NOT NULL
           );";
        dbDelta($sql);

        $table_name = $wpdb->prefix . "akp_impressions_expire";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             `post_id` int(11) NOT NULL,
             `ip_address` varchar(20) NOT NULL,
             `expire` int(11) NOT NULL
           );";
        dbDelta($sql);

        $table_name = $wpdb->prefix . "akp_click_log";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             `post_id` int(11) NOT NULL,
             `ip_address` varchar(20) NOT NULL,
             `timestamp` int(11) NOT NULL
           );";
        dbDelta($sql);

        $table_name = $wpdb->prefix . "akp_click_expire";
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
             `post_id` int(11) NOT NULL,
             `ip_address` varchar(20) NOT NULL,
             `expire` int(11) NOT NULL
           );";
        dbDelta($sql);
    }
    
    function akp_settings_link($action_links,$plugin_file){
            if($plugin_file==plugin_basename(__FILE__)){
                    $akp_settings_link = '<a href="admin.php?page=' . dirname(plugin_basename(__FILE__)) . '">' . __("Settings", 'akptext') . '</a>';
                    array_unshift($action_links,$akp_settings_link);
            }
            return $action_links;
    }
    add_filter('plugin_action_links','akp_settings_link',10,2);
    
    require_once plugin_dir_path(__FILE__).'includes/widget.php';
    require_once plugin_dir_path(__FILE__).'includes/admin_area.php';
    require_once plugin_dir_path(__FILE__).'includes/output.php';
    require_once plugin_dir_path(__FILE__).'js/adkingpro-js.php';
    
?>