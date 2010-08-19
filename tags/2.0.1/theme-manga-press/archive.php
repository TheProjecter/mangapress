<?php
/**
 *	Template Name: Index.php
 *
 *	index.php
 *	starting here because single.-, category.- and page.php
 *	are going to be based off this file
 *
***/
?>
<?php get_header(); ?>

    <div id="wrapper-inner">
    
    <div id="posts" class="narrow">
	<?php if (have_posts()) :?>
       	<?php query_posts($query_string.'&cat=-'.wp_comic_category_id()) ?>
    	  <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
 	  <?php /* If this is a category archive */ if (is_category()) { ?>
		<h2 class="pagetitle">Archive for the &#8216;<?php single_cat_title(); ?>&#8217; Category</h2>
 	  <?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
		<h2 class="pagetitle">Posts Tagged &#8216;<?php single_tag_title(); ?>&#8217;</h2>
 	  <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>
 	  <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>
 	  <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
		<h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>
	  <?php /* If this is an author archive */ } elseif (is_author()) { ?>
		<h2 class="pagetitle">Author Archive</h2>
 	  <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
		<h2 class="pagetitle">Blog Archives</h2>
 	  <?php } ?>
	    <?php while (have_posts()) : the_post(); ?>
        
        	<div <?php post_class('the-post'); ?> id="post-<?php the_ID()?>">
                    <h2><a href="<?php the_permalink() ?>" title="<?php the_title(); ?>" rel="bookmark"><?php the_title() ?></a></h2>
                    <span class="post-meta"><img src="<?php bloginfo('template_url') ?>/images/date.png" alt="#" /> <?php the_date() ?> @ <?php the_time()?> by <?php the_author(); ?> 
                     <img src="<?php bloginfo('template_url') ?>/images/folder.png" alt="_\" />Filed under: <?php the_category(', ') ?>&nbsp;<?php the_tags('Tags: ', ', ', '<br />'); ?>&nbsp;&nbsp;
					<?php $add = "<img src=\"".get_bloginfo('template_url')."/images/comment_edit.png\" alt=\"#\" />"; ?>
                    <?php $one = "<img src=\"".get_bloginfo('template_url')."/images/comment.png\" alt=\"#\" />"; ?>
                    <?php $more = "<img src=\"".get_bloginfo('template_url')."/images/comments.png\" alt=\"#\" />"; ?> 
                    <?php comments_popup_link($add.' Add Comment &#187;', $one.' 1 Comment &#187;', $more.' % Comments &#187;'); ?></span>
    
                    <div class="the-content">
                        <?php the_excerpt();?>
                    </div>
                                    
            </div>

            <p class="post-sep clear"></p>
            
    	<?php endwhile;?>
            <div class="posts-nav">
				<?php posts_nav_link(); ?>
            </div>
	<?php else: ?>

   		<h2 class="center">Not Found</h2>
		<p class="center">Sorry, but you are looking for something that isn't here.</p>

    <?php endif;?>
	</div>


    <div id="sidebar">
	    <?php get_sidebar(); ?>
	</div>
	<br class="clear"/>
</div>
<?php get_footer(); ?>