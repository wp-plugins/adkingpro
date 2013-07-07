<?php
/*
    Plugin Name: Ad King Pro
    Plugin URI: http://durham.net.au/wordpress/plugins/ad-king-pro/
    Description: Ad King Pro allows you to manage, display, document and report all of your custom advertising on your wordpress site.
    Version: 1.6
    Author: Ash Durham
    Author URI: http://durham.net.au/
    License: GPL2

    Copyright 2013  Ash Durham  (email : contact@durham.net.au)

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
    $akp_db_version = "1.6";

    function akp_install() {
       global $wpdb;
       global $akp_db_version;

       require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       
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
       
       $table_name = $wpdb->prefix . "terms";
       $sql = "INSERT INTO $table_name 
        (`name`, `slug`, `term_group`)
        VALUES ('Sidebar', 'sidebar', '0')";
       dbDelta($sql);
       
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

       add_option("apk_db_version", $apk_db_version);
    }
    
    // Register hooks at activation
    register_activation_hook(__FILE__,'akp_install');
    
    // END INSTALL
    
    if (get_option("apk_db_version") != $akp_db_version) {
        // Execute your upgrade logic here
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
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

        // Then update the version value
        update_option("apk_db_version", $akp_db_version);
    }
    
    require_once plugin_dir_path(__FILE__).'includes/widget.php';
    require_once plugin_dir_path(__FILE__).'includes/admin_area.php';
    require_once plugin_dir_path(__FILE__).'includes/output.php';
    require_once plugin_dir_path(__FILE__).'js/adkingpro-js.php';
    
?>