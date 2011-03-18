<?php
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage Comments_Template
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 *
 * @todo Markup additions and styling.
 */
?>
<div id="comments">
<?php if (have_comments()) : ?>

    <ol>
        <?php wp_list_comments(); ?>
    </ol>

<?php else: ?>
    No comments have been posted.
<?php endif; ?>

<?php comment_form(); ?>
</div>