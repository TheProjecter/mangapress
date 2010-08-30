<?php
if ($_GET['action'] == 'update_thumbnail'
        && wp_verify_nonce($_GET['_wpnonce'], 'mangapress-thumbnails-update')) {

    $msg = $this->comics->update_thumbnails();
    
    // do some stupid shit...
    if ($msg['status'])
        update_option('mangapress_thumbnails_updated', 'yes');
}

if ($_GET['action'] == 'hide_thumbnail_page'
        && wp_verify_nonce($_GET['_wpnonce'], 'mangapress-thumbnails-hide-page')) {
    
    update_option('mangapress_thumbnails_updated', 'yes');
    
}
?>

<div class="wrap">
    <div class="icon32" id="icon-upload"><br /></div>
    <h2>Update Comic Thumbnails</h2>
    <?php if ( isset($msg['message']) ) : ?>
    <div class="updated settings-error" id="setting-error-settings_updated">
    <p><strong><?php echo $msg['message'] ?></strong></p></div>
    <?php endif; ?>

    <h3>Comic Posts Thumbnails</h3>
    <p id="message">
        In order to take advantage of new features in Manga+Press 3.0, your comic 
        posts must have thumbnails associated with them. Just run the utility by
        clicking on the link below. Once you've updated your thumbnails, this page
        will disappear and you will be able to use post thumbnails with your comic posts.
        
    </p>
    <h3>Update Thumbnails</h3>
    <p id="update-thumbs">        
        Note: this options does two things if your theme supports post thumbnails. It creates the new thumbnails sizes that you specified in Manga+Press Options if the post doesn't already have them; and it also <em>adds</em> a thumbnail to your Comic post if it doesn't have one.
    </p>
    <strong><a class="button-primary" href="<?php echo $_SERVER['REQUEST_URI'] . '&amp;action=update_thumbnail&amp;_wpnonce=' . wp_create_nonce('mangapress-thumbnails-update') ?>">Add Post Thumbnails to Your Comic posts!</a></strong>
    <h3>Hide This Page</h3>
    <p>        
        If you wish to proceed without adding post thumbnails to your comic posts or if your comic posts already have thumbnails, you can simply hide this page. If you need this page in the future, you can always unhide it by going to Manga+Press Options and clicking "Unhide Update Comic Thumbnails Page."
    </p>
    <strong><a class="button-primary" href="<?php echo $_SERVER['REQUEST_URI'] . '&amp;action=hide_thumbnail_page&amp;_wpnonce=' . wp_create_nonce('mangapress-thumbnails-hide-page') ?>">Yes, Hide This Page.</a></strong>
</div>