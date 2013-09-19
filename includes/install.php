<?php

    global $akp_db_version;
    $akp_db_version = "1.0";

    function akp_install() {
       global $wpdb;
       global $akp_db_version;

       require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
       
//       $table_name = $wpdb->prefix . "akp_ads";
//
//       $sql = "CREATE TABLE $table_name (
//      id mediumint(9) NOT NULL AUTO_INCREMENT,
//      post_id datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//      UNIQUE KEY id (id)
//        );";
//       dbDelta($sql);
       
       $sql = "CREATE TABLE IF NOT EXISTS `wp_akp_click_log` (
  `post_id` int(11) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `timestamp` int(11) NOT NULL,
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
       dbDelta($sql);
       
       $sql = "CREATE TABLE IF NOT EXISTS `wp_akp_click_expire` (
  `post_id` int(11) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `expire` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
       dbDelta($sql);

       add_option("apk_db_version", $apk_db_version);
    }
    
    // Register hooks at activation
    register_activation_hook(__FILE__,'akp_install');

//    function akp_install_data() {
//       global $wpdb;
//       $welcome_name = "Mr. WordPress";
//       $welcome_text = "Congratulations, you just completed the installation!";
//
//       $rows_affected = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text ) );
//    }

?>
