<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Ad King Pro</h2>
    
    <div class="kpp_block filled">
        <h2>Connect</h2>
        <div id="kpp_social">
            <div class="kpp_social facebook"><a href="https://www.facebook.com/KingProPlugins" target="_blank"><i class="icon-facebook"></i> <span class="kpp_width"><span class="kpp_opacity">Facebook</span></span></a></div>
            <div class="kpp_social twitter"><a href="https://twitter.com/KingProPlugins" target="_blank"><i class="icon-twitter"></i> <span class="kpp_width"><span class="kpp_opacity">Twitter</span></span></a></div>
            <div class="kpp_social google"><a href="https://plus.google.com/b/101488033905569308183/101488033905569308183/about" target="_blank"><i class="icon-google-plus"></i> <span class="kpp_width"><span class="kpp_opacity">Google+</span></span></a></div>
        </div>
        <h4>Found an issue? Post your issue on the <a href="http://wordpress.org/support/plugin/adkingpro" target="_blank">support forums</a>. If you would prefer, please email your concern to <a href="mailto:plugins@kingpro.me">plugins@kingpro.me</a></h4>   
    </div>
    
    <div class="akp_tabs">
        <a class="akp_advert_settings active">Advert Settings</a>
        <a class="akp_howto">How-To</a>
        <a class="akp_faq">FAQ</a>
    </div>
    
    <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
    <div class="updated akp_notice">
        <p><?php _e( "Settings have been saved", 'akp_text' ); ?></p>
    </div>
    <?php elseif (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'false') : ?>
    <div class="error akp_notice">
        <p><?php _e( "Settings have <strong>NOT</strong> been saved. Please try again.", 'akp_text' ); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="akp_sections">
        <form method="post" action="options.php">
        <?php settings_fields('akp-options'); ?>
        <?php do_settings_sections('akp-options'); ?>
            
        <?php /******* ADVERT SETTINGS *******/ ?>
        <div id="akp_advert_settings" class="akp_section active">
            <?php submit_button('Save Settings', 'primary', 'submit', false, array('id'=>'akp_advert_settings_top_submit')); ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">Minimum Authorised Role</th>
                <td>
                    <?php $role = get_option('akp_auth_role'); ?>
                    <select name="akp_auth_role">
                        <option value="subscriber"<?= ($role == "subscriber") ? ' selected' : '' ?>>Subscriber</option>
                        <option value="administrator"<?= ($role == "administrator") ? ' selected' : '' ?>>Administrator</option>
                        <option value="editor"<?= ($role == "editor") ? ' selected' : '' ?>>Editor</option>
                        <option value="author"<?= ($role == "author") ? ' selected' : '' ?>>Author</option>
                        <option value="contributor"<?= ($role == "contributor") ? ' selected' : '' ?>>Contributor</option>
                    </select>
                </td>
                <td></td>
                </tr>

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
                            $folder = scandir(str_replace("includes/screens/","",plugin_dir_path(__FILE__)).'themes/');
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
            <?php submit_button('Save Settings', 'primary', 'submit', false, array('id'=>'akp_advert_settings_bottom_submit')); ?>
        </div>

        <?php /****** HOW-TO ******/ ?>
        <div id="akp_howto" class="akp_section">
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
            
        <?php /****** FAQ ******/ ?>
        <div id="akp_faq" class="akp_section">
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
        </form>
    </div>  
</div>

<script type="text/javascript">
    jQuery('.akp_tabs a').click(function() {
        jQuery(this).parent().children('a.active').removeClass('active');
        jQuery('.akp_sections').find('div.akp_section.active').removeClass('active');
        
        var active = jQuery(this).attr('class');
        jQuery(this).addClass('active');
        jQuery("#"+active).addClass('active');
    });
</script>