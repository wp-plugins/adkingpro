<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Ad King Pro</h2>
    
    <div class="kpp_block filled">
        <h2><?= __("Connect", 'akptext') ?></h2>
        <div id="kpp_social">
            <div class="kpp_social facebook"><a href="https://www.facebook.com/KingProPlugins" target="_blank"><i class="icon-facebook"></i> <span class="kpp_width"><span class="kpp_opacity"><?= __("Facebook", 'akptext') ?></span></span></a></div>
            <div class="kpp_social twitter"><a href="https://twitter.com/KingProPlugins" target="_blank"><i class="icon-twitter"></i> <span class="kpp_width"><span class="kpp_opacity"><?= __("Twitter", 'akptext') ?></span></span></a></div>
            <div class="kpp_social google"><a href="https://plus.google.com/b/101488033905569308183/101488033905569308183/about" target="_blank"><i class="icon-google-plus"></i> <span class="kpp_width"><span class="kpp_opacity"><?= __("Google+", 'akptext') ?></span></span></a></div>
        </div>
        <h4><?= __("Found an issue? Post your issue on the", 'akptext') ?> <a href="http://wordpress.org/support/plugin/adkingpro" target="_blank"><?= __("support forums", 'akptext') ?></a>. <?= __("If you would prefer, please email your concern to", 'akptext') ?> <a href="mailto:plugins@kingpro.me">plugins@kingpro.me</a></h4>   
    </div>
    
    <div class="akp_tabs">
        <a class="akp_advert_settings active"><?= __("Advert Settings", 'akptext') ?></a>
        <a class="akp_howto"><?= __("How-To", 'akptext') ?></a>
        <a class="akp_faq"><?= __("FAQ", 'akptext') ?></a>
    </div>
    
    <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') : ?>
    <div class="updated akp_notice">
        <p><?php __( "Settings have been saved", 'akptext' ); ?></p>
    </div>
    <?php elseif (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'false') : ?>
    <div class="error akp_notice">
        <p><?php __( "Settings have <strong>NOT</strong> been saved. Please try again.", 'akptext' ); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="akp_sections">
        <form method="post" action="options.php">
        <?php settings_fields('akp-options'); ?>
        <?php do_settings_sections('akp-options'); ?>
            
        <?php /******* ADVERT SETTINGS *******/ ?>
        <div id="akp_advert_settings" class="akp_section active">
            <?php submit_button(__('Save Settings', 'akptext'), 'primary', 'submit', false, array('id'=>'akp_advert_settings_top_submit')); ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row"><?= __("Minimum Authorised Role", 'akptext' ); ?></th>
                <td>
                    <?php $role = get_option('akp_auth_role'); ?>
                    <select name="akp_auth_role">
                        <option value="subscriber"<?= ($role == "subscriber") ? ' selected' : '' ?>><?= __("Subscriber", 'akptext' ); ?></option>
                        <option value="administrator"<?= ($role == "administrator") ? ' selected' : '' ?>><?= __("Administrator", 'akptext' ); ?></option>
                        <option value="editor"<?= ($role == "editor") ? ' selected' : '' ?>><?= __("Editor", 'akptext' ); ?></option>
                        <option value="author"<?= ($role == "author") ? ' selected' : '' ?>><?= __("Author", 'akptext' ); ?></option>
                        <option value="contributor"<?= ($role == "contributor") ? ' selected' : '' ?>><?= __("Contributor", 'akptext' ); ?></option>
                    </select>
                </td>
                <td></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?= __("Click Expiry Time Length (per IP)", 'akptext' ); ?></th>
                <td>
                    <?php $expiry = get_option('expiry_time'); ?>
                    <select name="expiry_time">
                        <option value="+0 hours"<?php if ($expiry == "+0 hours") : ?> selected<?php endif; ?>><?= __("None", 'akptext' ); ?></option>
                        <option value="+1 hour"<?php if ($expiry == "+1 hours") : ?> selected<?php endif; ?>><?= __("1 Hour", 'akptext' ); ?></option>
                        <option value="+2 hours"<?php if ($expiry == "+2 hours") : ?> selected<?php endif; ?>><?= __("2 Hours", 'akptext' ); ?></option>
                        <option value="+4 hours"<?php if ($expiry == "+4 hours") : ?> selected<?php endif; ?>><?= __("4 Hours", 'akptext' ); ?></option>
                        <option value="+6 hours"<?php if ($expiry == "+6 hours") : ?> selected<?php endif; ?>><?= __("6 Hours", 'akptext' ); ?></option>
                        <option value="+8 hours"<?php if ($expiry == "+8 hours") : ?> selected<?php endif; ?>><?= __("8 Hours", 'akptext' ); ?></option>
                        <option value="+10 hours"<?php if ($expiry == "+10 hours") : ?> selected<?php endif; ?>><?= __("10 Hours", 'akptext' ); ?></option>
                        <option value="+16 hours"<?php if ($expiry == "+16 hours") : ?> selected<?php endif; ?>><?= __("16 Hours", 'akptext' ); ?></option>
                        <option value="+24 hours"<?php if ($expiry == "+24 hours") : ?> selected<?php endif; ?>><?= __("24 Hours", 'akptext' ); ?></option>
                    </select>
                </td>
                <td></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?= __("Impression Expiry Time Length (per IP)", 'akptext' ); ?></th>
                <td>
                    <?php $expiry = get_option('impression_expiry_time'); ?>
                    <select name="impression_expiry_time">
                        <option value="+0 hours"<?php if ($expiry == "+0 hours") : ?> selected<?php endif; ?>><?= __("None", 'akptext' ); ?></option>
                        <option value="+1 hour"<?php if ($expiry == "+1 hours") : ?> selected<?php endif; ?>><?= __("1 Hour", 'akptext' ); ?></option>
                        <option value="+2 hours"<?php if ($expiry == "+2 hours") : ?> selected<?php endif; ?>><?= __("2 Hours", 'akptext' ); ?></option>
                        <option value="+4 hours"<?php if ($expiry == "+4 hours") : ?> selected<?php endif; ?>><?= __("4 Hours", 'akptext' ); ?></option>
                        <option value="+6 hours"<?php if ($expiry == "+6 hours") : ?> selected<?php endif; ?>><?= __("6 Hours", 'akptext' ); ?></option>
                        <option value="+8 hours"<?php if ($expiry == "+8 hours") : ?> selected<?php endif; ?>><?= __("8 Hours", 'akptext' ); ?></option>
                        <option value="+10 hours"<?php if ($expiry == "+10 hours") : ?> selected<?php endif; ?>><?= __("10 Hours", 'akptext' ); ?></option>
                        <option value="+16 hours"<?php if ($expiry == "+16 hours") : ?> selected<?php endif; ?>><?= __("16 Hours", 'akptext' ); ?></option>
                        <option value="+24 hours"<?php if ($expiry == "+24 hours") : ?> selected<?php endif; ?>><?= __("24 Hours", 'akptext' ); ?></option>
                    </select>
                </td>
                <td></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?= __("Week starts* (for stats)", 'akptext' ); ?></th>
                <td>
                    <?php $start = get_option('week_starts'); ?>
                    <select name="week_starts">
                        <option value="monday"<?php if ($start == "monday") : ?> selected<?php endif; ?>><?= __("Monday", 'akptext' ); ?></option>
                        <option value="tuesday"<?php if ($start == "tuesday") : ?> selected<?php endif; ?>><?= __("Tuesday", 'akptext' ); ?></option>
                        <option value="wednesday"<?php if ($start == "wednesday") : ?> selected<?php endif; ?>><?= __("Wednesday", 'akptext' ); ?></option>
                        <option value="thursday"<?php if ($start == "thursday") : ?> selected<?php endif; ?>><?= __("Thursday", 'akptext' ); ?></option>
                        <option value="friday"<?php if ($start == "friday") : ?> selected<?php endif; ?>><?= __("Friday", 'akptext' ); ?></option>
                        <option value="saturday"<?php if ($start == "saturday") : ?> selected<?php endif; ?>><?= __("Saturday", 'akptext' ); ?></option>
                        <option value="sunday"<?php if ($start == "sunday") : ?> selected<?php endif; ?>><?= __("Sunday", 'akptext' ); ?></option>
                    </select>
                </td>
                <td><?= __("* Week starts at midnight on the day chosen.", 'akptext' ); ?></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?= __("Revenue Currency Sign", 'akptext' ); ?></th>
                <td>
                    <?php $sign = get_option('revenue_currency'); ?>
                    <input type="text" name="revenue_currency" value="<?= $sign ?>" />
                </td>
                <td><?= __("* This sign will be used throughout the reporting section", 'akptext' ); ?></td>
                </tr>

                <tr valign="top">
                <th scope="row"><?= __("PDF Theme", 'akptext' ); ?></th>
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
                <td><?= __("* More themes can be downloaded from the", 'akptext' ); ?> <a href="http://kingpro.me/plugins/ad-king-pro/themes/" target="_blank">King Pro Plugins <?= __("website", 'akptext' ); ?></a></td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'akptext'), 'primary', 'submit', false, array('id'=>'akp_advert_settings_bottom_submit')); ?>
        </div>

        <?php /****** HOW-TO ******/ ?>
        <div id="akp_howto" class="akp_section">
            <h2><?= __("How To", 'akptext' ); ?></h2>
            <h3><?= __("Use Shortcodes", 'akptext' ); ?></h3>
            <p><?= __("Shortcodes can be used in any page or post on your site. By default", 'akptext' ); ?>:</p>
            <pre>[adkingpro]</pre>
            <p><?= __("is defaulting to the advert type 'Sidebar' and randomly chosing from that. You can define your own advert type and display the adverts attached to that type by", 'akptext' ); ?>:</p>
            <pre>[adkingpro type='your-advert-type-slug']</pre>
            <p><?= __("Alternatively, you can display a single advert by entering its \"Banner ID\" which can be found in the table under the Adverts section", 'akptext' ); ?>:</p>
            <pre>[adkingpro banner='{banner_id}']</pre>
            <p><?= __("Have a select few adverts that you'd like to show? No problem, just specify the ids separated by commas", 'akptext' ); ?>:</p>
            <pre>[adkingpro banner='{banner_id1}, {banner_id2}']</pre>
            <p><?= __("Want to output a few adverts at once? Use the 'render' option in the shortcode", 'akptext' ); ?>:</p>
            <pre>[adkingpro banner='{banner_id1}, {banner_id2}' render='2']</pre>
            <pre>[adkingpro type='your-advert-type-slug' render='2']</pre>
            <p><?= __("Only have a small space and what a few adverts to display? Turn on the auto rotating slideshow!", 'akptext' ); ?>:</p>
            <pre>[adkingpro type="your-advert-type-slug" rotate='true']</pre>
            <p><?= __("There are also some settings you can play with to get it just right", 'akptext' ); ?>:</p>
            <ul>
                <li><?= __("Effect: \"fade | slideLeft | none\" Default - fade", 'akptext' ); ?></li>
                <li><?= __("Pause Speed: \"Time in ms\" Default - 5000 (5s)", 'akptext' ); ?></li>
                <li><?= __("Change Speed: \"Time in ms\" Default - 600 (0.6s)", 'akptext' ); ?></li>
            </ul>
            <p><?= __("Use one or all of these settings", 'akptext' ); ?>:</p>
            <pre>[adkingpro rotate='true' effect='fade' speed='5000' changespeed='600']</pre>
            <p><?= __("To add this into a template, just use the \"do_shortcode\" function", 'akptext' ); ?>:</p>
            <pre>&lt;?php 
        if (function_exists('adkingpro_func'))
            echo do_shortcode("[adkingpro type='sidebar']");
    ?&gt;</pre>
            <h3><?= __("Install PDF Themes", 'akptext' ); ?></h3>
            <p><?= __("Download themes from the", 'akptext' ); ?> <a href="http://kingpro.me/plugins/ad-king-pro/themes/" target="_blank">King Pro Plugins <?= __("page", 'akptext' ); ?></a>. <?= __("Locate the themes folder in the adkingpro plugin folder, generally located", 'akptext' ); ?>:</p>
            <pre>/wp-content/plugins/adkingpro/themes/</pre>
            <p><?= __("Unzip the downloaded zip file and upload the entire folder into the themes folder mentioned above.", 'akptext' ); ?></p>
            <p><?= __("Once uploaded, return to this page and your theme will be present in the PDF Theme dropdown to the left. Choose the theme and save the options. Next time you generate a report, the theme you have chosen will be used.", 'akptext' ); ?></p>
            <p><?= __("The ability to upload the zip file straight from here will be added soon", 'akptext' ); ?></p>
        </div>
            
        <?php /****** FAQ ******/ ?>
        <div id="akp_faq" class="akp_section">
            <h2><?= __("FAQ", 'akptext' ); ?></h2>
            <h4><?= __("Q. After activating this plugin, my site has broken! Why?", 'akptext' ); ?></h4>
            <p><?= __("Nine times out of ten it will be due to your own scripts being added above the standard area where all the plugins are included. ", 'akptext' ); ?>
                <?= __("If you move your javascript files below the function, \"wp_head()\" in the \"header.php\" file of your theme, it should fix your problem.", 'akptext' ); ?></p>
            <h4><?= __("Q. I want to track clicks on a banner that scrolls to or opens a flyout div on my site. Is it possible?", 'akptext' ); ?></h4>
            <p><?= __("Yes. Enter a '#' in as the URL for the banner when setting it up. At output, the banner is given a number of classes to allow for styling, one being \"banner{banner_id}\",", 'akptext' ); ?>
                <?= __("where you would replace the \"{banner_id}\" for the number in the required adverts class.", 'akptext' ); ?>
                <?= __("Use this in a jquery click event and prevent the default action of the click to make it do the action you require", 'akptext' ); ?>:</p>
            <pre>$(".adkingprobanner.banner{banner_id}").click(
            function(e) {
            &nbsp;&nbsp;&nbsp;&nbsp;e.preventDefault();
            &nbsp;&nbsp;&nbsp;&nbsp;// Your action here
            });</pre>
            <h4><?= __("I get an error saying the PDF can't be saved due to write permissions on the server. What do I do?", 'akptext' ); ?></h4>
            <p><?= __("The plugin needs your permission to save the PDFs you generate to the output folder in the plugins folder. To do this, you are required to", 'akptext' ); ?>
            <?= __("update the outputs permissions to be writable. Please see", 'akptext' ); ?> <a href="http://codex.wordpress.org/Changing_File_Permissions" target="_blank"><?= __("the wordpress help page", 'akptext' ); ?></a> <?= __("to carry this out.", 'akptext' ); ?></p>
            <br />
            <h4><?= __("Found an issue? Post your issue on the", 'akptext' ); ?> <a href="http://wordpress.org/support/plugin/adkingpro" target="_blank"><?= __("support forums", 'akptext' ); ?></a>. <?= __("If you would prefer, please email your concern to", 'akptext' ); ?> <a href="mailto:plugins@kingpro.me">plugins@kingpro.me</a></h4>
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