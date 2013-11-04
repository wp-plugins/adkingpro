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
                            <label>From: </label><input type="text" class="akp_datepicker from_adkingpro_date" />
                            <label>To: </label><input type="text" class="akp_datepicker to_adkingpro_date" />
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