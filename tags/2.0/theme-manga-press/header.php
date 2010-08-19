<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<meta name="Description" content="<?php bloginfo('description'); ?>" />
<meta name="keywords" content="manga wordpress comic manager webcomic cms plugin theme" />
<title>
<?php bloginfo('name'); ?>
<?php wp_title(); ?>
</title>
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/reset.css" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php bloginfo('atom_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<?php wp_get_archives('type=monthly&format=link'); ?>
<?php wp_enqueue_script('jquery');?>
<?php wp_head() ?>
</head>
<body>
<div id="wrapper">
    <div id="header" title="<?php bloginfo('name');  ?>">
    </div>
    <ul class="horizontal-menu">
        <li class="page_item<?php if (is_home()) {?> current_page_item<? }?>"><a href="<?php bloginfo('url')?>" title="Home">Home</a></li>
        <?php wp_list_pages('title_li=') ?>
    </ul>
    
