<?php

global $wpdb;
include "includes/admin_area.php";
if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
}
check_admin_referer( 'bulk-plugins' );

if (get_option('akp_clear_on_delete') == 1) {

    unregister_akp_options();
    delete_akp_options();

    $table_names = array();
    $table_names[] = $wpdb->prefix . "akp_impressions_log";
    $table_names[] = $wpdb->prefix . "akp_impressions_expire";
    $table_names[] = $wpdb->prefix . "akp_click_log";
    $table_names[] = $wpdb->prefix . "akp_click_expire";
    foreach ($table_names as $table_name) {
        $sql = "DROP TABLE IF EXISTS $table_name";
        $wpdb->query($sql);
    }
    
    $table_name = $wpdb->prefix . "term_taxonomy";
    $sql = "SELECT * FROM $table_name WHERE taxonomy = 'advert_types'";
    $taxonomy_terms = $wpdb->get_results($sql);
    if (!empty($taxonomy_terms)) {
        foreach ($taxonomy_terms as $term) {
            $table_name = $wpdb->prefix . "terms";
            $sql = "DELETE FROM $table_name WHERE term_id = '{$term->term_id}'";
            $wpdb->query($sql);
        }
    }

    $table_name = $wpdb->prefix . "term_taxonomy";
    $sql = "DELETE FROM $table_name WHERE taxonomy = 'advert_types'";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix . "posts";
    $sql = "DELETE FROM $table_name WHERE post_type = 'adverts_posts'";
    $wpdb->query($sql);
}