<?php
/**
 * @package Manga_Press
 * @subpackage MangaPress_Bundled_Theme
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
/**
 * @package MangaPress_Bundled_Theme
 * @subpackage Comments_Template
 * @version $Id$
 * @author Jess Green <jgreen@psy-dreamer.com>
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
<?php
$fields = array(
    'author' => '<p class="comment-form-author">' . '<label for="author">' . ( $req ? '<span class="required">*</span> ' : '' ) . __( 'Name' ) . '</label>' .
            '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
    'email'  => '<p class="comment-form-email"><label for="email">' . ( $req ? '<span class="required">*</span> ' : '' ) . __( 'Email' ) . '</label>' .
            '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
    'url'    => '<p class="comment-form-url"><label for="url">' . __( 'Website' ) . '</label>' .
            '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>',
);

comment_form(array('fields' =>$fields), $post->ID);
?>
</div>