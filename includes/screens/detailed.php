<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Ad King Pro <?= __("Detailed Stats", 'akptext') ?></h2>
    <div class="akp_detailed_stats">
        <?php
        $using_GA = get_option('akp_ga_intergrated');

        if (!$using_GA) :

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
                        <h2><?= __("Summary", 'akptext') ?></h2>
                        <div class='stat'><h4><?= __("All Time", 'akptext') ?></h4><span title="<?= __("Impressions", 'akptext') ?>: <?= $all_impressions[0]->impressions ?>" alt="<?= __("Impressions", 'akptext') ?>: <?= $all_impressions[0]->impressions ?>"><?= $all_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __("This Month", 'akptext') ?></h4><span title="<?= __("Impressions", 'akptext') ?>: <?= $month_impressions[0]->impressions ?>" alt="<?= __("Impressions", 'akptext') ?>: <?= $month_impressions[0]->impressions ?>"><?= $month_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __("This Week", 'akptext') ?></h4><span title="<?= __("Impressions", 'akptext') ?>: <?= $week_impressions[0]->impressions ?>" alt="<?= __("Impressions", 'akptext') ?>: <?= $week_impressions[0]->impressions ?>"><?= $week_clicks[0]->clicks ?></span></div>
                        <div class='stat'><h4><?= __("Today", 'akptext') ?></h4><span title="<?= __("Impressions", 'akptext') ?>: <?= $today_impressions[0]->impressions ?>" alt="<?= __("Impressions", 'akptext') ?>: <?= $today_impressions[0]->impressions ?>"><?= $today_clicks[0]->clicks ?></span></div>
                    </div>
                    <div class='detailed'>
                        <h2><?= __("Detailed", 'akptext') ?></h2>
                        <div class="detailed_menu">
                            <a class="active akp_detailed" rel="all"><?= __("View all clicks", 'akptext') ?></a>
                            <a class="akp_detailed" rel="month"><?= __("View this month clicks", 'akptext') ?></a>
                            <a class="akp_detailed" rel="week"><?= __("View this week clicks", 'akptext') ?></a>
                            <a class="akp_detailed" rel="day"><?= __("View todays clicks", 'akptext') ?></a>
                            <a class="akp_detailed" rel="date"><?= __("View date range clicks", 'akptext') ?></a>
                        </div>
                        <div class="detailed_details">
                            <div class="akp_detailed_all_details" style="display: block;">
                                <br />
                                <table>
                                    <tr>
                                        <td></td>
                                        <th class="center"><?= __("Count", 'akptext') ?></th>
                                        <th class="center"><?= __("Cost Per", 'akptext') ?></th>
                                        <th class="center"><?= __("Total", 'akptext') ?></th>
                                    </tr>
                                    <tr>
                                        <td><?= __("Impressions", 'akptext') ?></td>
                                        <td class="center"><?= $all_impressions[0]->impressions ?></td>
                                        <td class="right"><?= $all_per_impression ?></td>
                                        <td class="right"><?= $all_impression_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __("Clicks", 'akptext') ?></td>
                                        <td class="center"><?= $all_clicks[0]->clicks ?></td>
                                        <td class="right"><?= $all_per_click ?></td>
                                        <td class="right"><?= $all_click_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="right bold"><?= __("TOTAL", 'akptext') ?></td>
                                        <td class="right bold"><?= $all_total_made_output ?></td>
                                    </tr>
                                </table>
                                <br />
                                <div class="akp_reporting">
                                    <strong><?= __("Download report", 'akptext') ?>: </strong> <a class="akp_csv" rel="all/<?= $post_id ?>"><?= __("CSV", 'akptext') ?></a> <a class="akp_pdf" rel="all/<?= $post_id ?>"><?= __("PDF", 'akptext') ?></a>
                                </div>
                                <br />
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
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
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="akp_detailed_month_details">
                                <br />
                                <table>
                                    <tr>
                                        <td></td>
                                        <th class="center"><?= __("Count", 'akptext') ?></th>
                                        <th class="center"><?= __("Cost Per", 'akptext') ?></th>
                                        <th class="center"><?= __("Total", 'akptext') ?></th>
                                    </tr>
                                    <tr>
                                        <td><?= __("Impressions", 'akptext') ?></td>
                                        <td class="center"><?= $month_impressions[0]->impressions ?></td>
                                        <td class="right"><?= $month_per_impression ?></td>
                                        <td class="right"><?= $month_impression_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __("Clicks", 'akptext') ?></td>
                                        <td class="center"><?= $month_clicks[0]->clicks ?></td>
                                        <td class="right"><?= $month_per_click ?></td>
                                        <td class="right"><?= $month_click_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="right bold"><?= __("TOTAL", 'akptext') ?></td>
                                        <td class="right bold"><?= $month_total_made_output ?></td>
                                    </tr>
                                </table>
                                <br />
                                <div class="akp_reporting">
                                    <strong><?= __("Download report", 'akptext') ?>: </strong> <a class="akp_csv" rel="month/<?= $post_id ?>"><?= __("CSV", 'akptext') ?></a> <a class="akp_pdf" rel="month/<?= $post_id ?>"><?= __("PDF", 'akptext') ?></a>
                                </div>
                                <br />
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
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
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="akp_detailed_week_details">
                                <br />
                                <table>
                                    <tr>
                                        <td></td>
                                        <th class="center"><?= __("Count", 'akptext') ?></th>
                                        <th class="center"><?= __("Cost Per", 'akptext') ?></th>
                                        <th class="center"><?= __("Total", 'akptext') ?></th>
                                    </tr>
                                    <tr>
                                        <td><?= __("Impressions", 'akptext') ?></td>
                                        <td class="center"><?= $week_impressions[0]->impressions ?></td>
                                        <td class="right"><?= $week_per_impression ?></td>
                                        <td class="right"><?= $week_impression_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __("Clicks", 'akptext') ?></td>
                                        <td class="center"><?= $week_clicks[0]->clicks ?></td>
                                        <td class="right"><?= $week_per_click ?></td>
                                        <td class="right"><?= $week_click_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="right bold"><?= __("TOTAL", 'akptext') ?></td>
                                        <td class="right bold"><?= $week_total_made_output ?></td>
                                    </tr>
                                </table>
                                <br />
                                <div class="akp_reporting">
                                    <strong><?= __("Download report", 'akptext') ?>: </strong> <a class="akp_csv" rel="week/<?= $post_id ?>"><?= __("CSV", 'akptext') ?></a> <a class="akp_pdf" rel="week/<?= $post_id ?>"><?= __("PDF", 'akptext') ?></a>
                                </div>
                                <br />
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
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
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="akp_detailed_day_details">
                                <br />
                                <table>
                                    <tr>
                                        <td></td>
                                        <th class="center"><?= __("Count", 'akptext') ?></th>
                                        <th class="center"><?= __("Cost Per", 'akptext') ?></th>
                                        <th class="center"><?= __("Total", 'akptext') ?></th>
                                    </tr>
                                    <tr>
                                        <td><?= __("Impressions", 'akptext') ?></td>
                                        <td class="center"><?= $today_impressions[0]->impressions ?></td>
                                        <td class="right"><?= $today_per_impression ?></td>
                                        <td class="right"><?= $today_impression_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td><?= __("Clicks", 'akptext') ?></td>
                                        <td class="center"><?= $today_clicks[0]->clicks ?></td>
                                        <td class="right"><?= $today_per_click ?></td>
                                        <td class="right"><?= $today_click_total_output ?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td class="right bold"><?= __("TOTAL", 'akptext') ?></td>
                                        <td class="right bold"><?= $today_total_made_output ?></td>
                                    </tr>
                                </table>
                                <br />
                                <div class="akp_reporting">
                                    <strong><?= __("Download report", 'akptext') ?>: </strong> <a class="akp_csv" rel="today/<?= $post_id ?>"><?= __("CSV", 'akptext') ?></a> <a class="akp_pdf" rel="today/<?= $post_id ?>"><?= __("PDF", 'akptext') ?></a>
                                </div>
                                <br />
                                <table>
                                    <thead>
                                        <tr>
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
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
                                            <th><?= __("Date/Time", 'akptext') ?></th>
                                            <th><?= __("IP Address", 'akptext') ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="akp_detailed_date_details">
                                <div class="choose_custom_date">
                                    <h4><?= __("Choose your date range", 'akptext') ?>:</h4>
                                    <label><?= __("From", 'akptext') ?>: </label><input type="text" class="akp_datepicker from_adkingpro_date" />
                                    <label><?= __("To", 'akptext') ?>: </label><input type="text" class="akp_datepicker to_adkingpro_date" />
                                    <a class="akp_custom_date" rel="<?= $post_id ?>"><?= __("Search", 'akptext') ?></a>
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
        else:
            ?>
        
            <section id="auth-button"></section>
            <section id="view-selector"></section>
            <section id="timeline"></section>

            <script>
            (function(w,d,s,g,js,fjs){
              g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(cb){this.q.push(cb)}};
              js=d.createElement(s);fjs=d.getElementsByTagName(s)[0];
              js.src='https://apis.google.com/js/platform.js';
              fjs.parentNode.insertBefore(js,fjs);js.onload=function(){g.load('analytics')};
            }(window,document,'script'));
            </script>

            <script>
            gapi.analytics.ready(function() {

              // Step 3: Authorize the user.

              var CLIENT_ID = '492828431152-r55e51ifna6b2j0ecdmg2to75976e1lf.apps.googleusercontent.com';

              gapi.analytics.auth.authorize({
                container: 'auth-button',
                clientid: CLIENT_ID,
              });

              // Step 4: Create the view selector.

              var viewSelector = new gapi.analytics.ViewSelector({
                container: 'view-selector'
              });

              // Step 5: Create the timeline chart.

              var timeline = new gapi.analytics.googleCharts.DataChart({
                reportType: 'ga',
                query: {
                  'dimensions': 'ga:date',
                  'metrics': 'ga:sessions',
                  'start-date': '30daysAgo',
                  'end-date': 'yesterday',
                },
                chart: {
                  type: 'LINE',
                  container: 'timeline'
                }
              });

              // Step 6: Hook up the components to work together.

              gapi.analytics.auth.on('success', function(response) {
                viewSelector.execute();
              });

              viewSelector.on('change', function(ids) {
                var newIds = {
                  query: {
                    ids: ids
                  }
                }
                timeline.set(newIds).execute();
              });
            });
            </script>
            <?php
        endif;
        ?>
    </div>
</div>

<?php 
    /* REPORTING SETUP INSTRUCTIONS 
     *
     *      1. Navigate to https://console.developers.google.com and create a 'project' for your site if you haven't already
     *      2. Once in your project, we need to "Enable an API" which is under APIs & Auth -> APIs
     *      3. Browse for APIs and search for Analytics API. Turn this on.
     *      4. Under APIs & Auth in the menu, click on Credentials
     *      5. Click on create a Client ID. If you are prompted to complete the consent screen, do so and return to the start of this step.
     *      6. When prompted, choose "Web Application" as the Application type
     *      7. 
     * 
     */
?>