<?php
mangapress_comic_navigation(); ?>

<?php var_dump($latest_comic_query); ?>
<h2><?php the_title(); ?></h2>

<p>
    <?php the_post_thumbnail(get_the_ID(), $thumbnail_size);?>
</p>

<?php the_content(); ?>