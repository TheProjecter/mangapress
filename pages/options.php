<?php
/**
 * @package Manga_Press
 * @subpackage Pages
 * @version $Id$
 * @author Jessica Green <jgreen@psy-dreamer.com>
 */
/**
 * @package Pages
 * @subpackage Options
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF']))
    die('You are not allowed to call this page directly.');

if ( ! current_user_can('manage_options') )
    wp_die(
        __(
            'You do not have sufficient permissions '
            . 'to manage options for this blog.',
            'mangapress'
        )
    );
?>

<h2>Manga+Press Options</h2>
<div class="wrap">
  <form action="options.php" method="post" id="basic_options_form">
    <div id="basic_options">
      <h3><?php _e('Basic Options', 'mangapress'); ?></h3>
      <p class="description"><?php _e('This section sets the main options for Manga+Press.', 'mangapress'); ?></p>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Update Options', 'mangapress'); ?> &raquo;" />
      </p>
      <?php settings_fields('mangapress-options'); ?>
      <table class="form-table">
        <tr>
          <th scope="col"><?php _e('Navigation CSS:', 'mangapress'); ?></th>
          <td>
            <select name="mangapress_options[nav_css]">
                <option value="default_css" <?php selected(($mp_options['nav_css']=='default_css'));?>><?php _e('Default', 'mangapress'); ?></option>
                <option value="custom_css" <?php selected(($mp_options['nav_css']=='custom_css')); ?>><?php _e('Custom', 'mangapress'); ?></option>
            </select>
            <?php _e('Turn this off. You know you want to!', 'mangapress'); ?></td>
        </tr>
        <tr>
          <th scope="col"></th>
          	<td><?php _e('Copy and paste this code into the <code>style.css</code> file of your theme.', 'mangapress'); ?>
<code style="display: block; width: 80%;"><pre>

    /* comic navigation */
    .comic-navigation { text-align:center; margin: 5px 0 10px 0; }
    .comic-nav-span { padding: 3px 10px; ext-decoration: none; }
    ul.comic-nav  { margin: 0; padding: 0; white-space: nowrap; }
    ul.comic-nav li { display: inline; list-style-type: none; }
    ul.comic-nav a { text-decoration: none; padding: 3px 10px; }
    ul.comic-nav a:link, ul.comic-nav a:visited { color: #ccc; text-decoration: none; }
    ul.comic-nav a:hover { text-decoration: none; }
    ul.comic-nav li:before{ content: ""; }

</pre></code>
            </td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Order by:', 'mangapress'); ?></th>
          <td>
            <select name="mangapress_options[order_by]">
                <option value="post_date" <?php selected('post_date', $mp_options['order_by']); ?>><?php _e('Date', 'mangapress'); ?></option>
                <option value="post_id" <?php selected('post_id', $mp_options['order_by']); ?> ><?php _e('Post ID', 'mangapress'); ?></option>
            </select>
          </td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Comic Navigation:'); ?></th>
          <td><label for="insert_nav">
              <input type="checkbox" name="mangapress_options[insert_nav]" id="insert_nav" value="1" <?php checked( '1', $mp_options['insert_nav'] ); ?>/>
              <?php _e('Automatically insert comic navigation code into comic posts.', 'mangapress'); ?></label>
          </td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Group Comics:'); ?></th>
          <td><label for="group_comics">
              <input type="checkbox" name="mangapress_options[group_comics]" id="group_comics" value="1" <?php checked( '1', $mp_options['group_comics'] ); ?> />
              <?php _e('Group comics by series.', 'mangapress'); ?></label></td>
        </tr>
        <tr>
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Comic Category:', 'mangapress'); ?></th>
          <td><select name="mangapress_options[latestcomic_cat]" id="latestcomic_cat">
              <option value="">&nbsp;</option>
          <?php
            $categories2  = get_categories('hide_empty=0');
            $current_cat2 = $mp_options['latestcomic_cat'];
            foreach ($categories2 as $cat) {
                $sel = selected($current_cat2, $cat->cat_ID, false);

                $option = "\t\t\t<option value=\"{$cat->cat_ID}\" $sel>";
                $option .= $cat->cat_name;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description"><?php _e('This category is used to separate all comic posts from regular posts in your blog.', 'mangapress'); ?></span>
          </td>
        </tr>
        <tr>
          <th scope="col">&nbsp;</th>
          <td><label for="exclude_comic_cat">
              <input type="checkbox" name="mangapress_options[comic_front_page]" id="exclude_comic_cat" value="1" <?php checked('1', $mp_options['comic_front_page']); ?> />
              <?php _e('Exclude comic category from front page.', 'mangapress'); ?></label></td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Latest Comic Page', 'mangapress'); ?></th>
          <td><select name="mangapress_options[latestcomic_page]">
              <option value="">&nbsp;</option>
              <?php

            $pages = get_pages();
            $current_page = $mp_options['latestcomic_page'];
            foreach ($pages as $page) {
                $sel = selected($current_page, $page->ID, false);
                $option = "\t\t\t\t<option value=\"{$page->ID}\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description"><?php _e('Sets a page for displaying the most recent comic.', 'mangapress'); ?></span>
          </td>
        </tr>
        <tr>
          <th scope="col" class="th-full"><?php _e('Comic Archive Page', 'mangapress'); ?></th>
          <td><select name="mangapress_options[comic_archive_page]">
              <option value="">&nbsp;</option>
              <?php

            $pages = get_pages();
            $current_page = $mp_options['comic_archive_page'];
            foreach ($pages as $page) {
                $sel = selected($current_page, $page->ID, false);
                $option = "\t\t\t\t<option value=\"{$page->ID}\" $sel>";
                $option .= $page->post_title;
                $option .= "</option>\n";
                echo $option;
            }
         ?>
            </select>
            <span class="description"><?php _e('Sets a page for displaying the comic archive page. CANNOT be the same as your Latest Comic page.', 'mangapress'); ?></span></td>
        </tr>
      </table>
    </div>

    <div id="image_options">
      <h3><?php _e('Image Options', 'mangapress'); ?></h3>
      <p class="description"><?php _e('This section controls banner and thumbnail generation for comic pages.', 'mangapress'); ?></p>
      <table class="form-table">
        <tr>
          <th class="th-full"><label for="make_thumb">
              <input type="checkbox" name="mangapress_options[make_thumb]" id="make_thumb" value="1" <?php checked( '1', $mp_options['make_thumb'] ); ?> />
              <?php _e('Generate Banner Image for Comic Page <span class="description">For <code>add_image_size()</code> and <code>the_post_thumbnail()</code>. Theme must support post thumbnails!</span>', 'mangapress'); ?></label></th>
        </tr>
        <tr>
          <th class="th-full"><label for="generate_comic_page">
              <input type="checkbox" name="mangapress_options[generate_comic_page]" id="generate_comic_page" value="1" <?php checked( '1', $mp_options['generate_comic_page']); ?> />
              <?php _e('Generate a comic page for use with <code>the_post_thumbnail()</code>.', 'mangapress'); ?>
            <span class="description"><?php _e('Creates a new image size for displaying comics in posts.', 'mangapress'); ?></span></label></th>
        </tr>
      </table>
      <h4><?php _e('Set Banner Width and Height', 'mangapress'); ?></h4>
      <p class="description"><?php _e('Sets the size of the comic banner displayed on the front page. Remember to adjust any CSS sizing used to the values below!', 'mangapress'); ?></p>
      <table class="form-table">
        <tr>
          <th><label for="banner_width"><?php _e('Banner Width:', 'mangapress'); ?></label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[banner_width]" id="banner_width" value="<?php echo $mp_options['banner_width']?>" />
              pixels</label></td>
        </tr>
        <tr>
          <th><label for="banner_height"><?php _e('Banner Height:', 'mangapress'); ?></label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[banner_height]" id="banner_height" value="<?php echo $mp_options['banner_height']?>" />
              pixels</label></td>
        </tr>
      </table>
      <h4><?php _e('Set Comic Width and Height', 'mangapress'); ?></h4>
      <p class="description"><?php _e('Sets the size of the comic page displayed using <code>the_post_thumbnail(\'comic-page\')</code>', 'mangapress'); ?></p>
      <table class="form-table">
        <tr>
          <th><label for="comic_width"><?php _e('Comic Page Width:', 'mangapress'); ?></label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[comic_width]" id="comic_width" value="<?php echo $mp_options['comic_width']?>" />
              pixels</label></td>
        </tr>
        <tr>
          <th><label for="comic_height"><?php _e('Comic Page Height:', 'mangapress'); ?></label></th>
          <td><label>
              <input type="text" size="6" name="mangapress_options[comic_height]" id="comic_height" value="<?php echo $mp_options['comic_height']?>" />
              pixels</label></td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Update Options', 'mangapress'); ?> &raquo;" />
      </p>
    </div>
      
    <div id="update_notif">
      <h3><?php _e('Comic Updates Notification', 'mangapress'); ?></h3>
      <p class="description"><?php _e("This section is for custom code that you wish to insert into your comic post. For example, the custom html comments that <a href=\"http://www.onlinecomics.net/\">OnlineComics.net</a> requires for its PageScan comic updates service.", 'mangapress'); ?></p>
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Update Options', 'mangapress'); ?> &raquo;" />
      </p>
      <table class="form-table">
        <tr>
          <th colspan="2"><h4><?php _e("TheWebComicList.com Code:", 'mangapress'); ?></h4></th>
        </tr>
        <tr>
          <td colspan="2"><span class="description"><?php _e("This options inserts an html comment which contains the date of the most recent comic near the beginning of the content section (usually The Loop). This is sometimes needed when TWC has a hard time detecting the status of the comic.", 'mangapress'); ?></span></td>
        </tr>
        <tr>
          <td colspan="2"><label>
              <input name="mangapress_options[twc_code_insert]" type="checkbox" id="enable_twc_date_code" value="1" <?php checked('1', $mp_options['twc_code_insert']); ?>/>
              <?php _e("Enable TWC date stamp comment", 'mangapress'); ?></label></td>
        </tr>
        <tr>
          <th colspan="2"><h4><?php _e("OnlineComics.net Code:", 'mangapress'); ?> </h4></th>
        </tr>
        <tr>
          <td colspan="2"><span class="description"><?php _e("This option is to be used with comics that are listed in the OnlineComics.net directory <em>and</em> have the PageScan option enabled.", 'mangapress'); ?></span></td>
        </tr>
        <tr>
          <td colspan="2"><label for="enable_onlinecomics_code">
              <input type="checkbox" name="mangapress_options[oc_code_insert]" id="enable_onlinecomics_code" value="1" <?php checked('1', $mp_options['oc_code_insert']); ?>/>
              <?php _e('Enable OnlineComics.net PageScan codes.', 'mangapress'); ?></label></td>
        </tr>
        <tr>
          <th><label for="ocn_comic_ID"><?php _e('OnlineComics.net Comic ID:', 'mangapress'); ?> </label></th>
          <td><input type="text" name="mangapress_options[oc_comic_id]" id="ocn_comic_ID" size="6" onkeyup="jQuery('.ocn_ID').html(this.value)" value="<?php echo $mp_options['oc_comic_id']?>" /></td>
        </tr>
        <tr>
          <th>Opening Tag:</th>
          <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#063"><?php echo $mp_options['oc_comic_id']?></span> <code>start --&gt;</code></td>
        </tr>
        <tr>
          <th>Closing Tag:</th>
          <td><code>&lt;!-- OnlineComics.net</code> <span class="ocn_ID" style="color:#F00"><?php echo $mp_options['oc_comic_id']?></span> <code>end --&gt;</code></td>
        </tr>
        <tr>
        	<td colspan="2">&nbsp;</td>
        </tr>
      </table>
      </div>
  </form>
</div>
