# Manga+Press Template Tags #

A list of available Manga+Press Template Tags

# Introduction #

Add your content here.

## Conditional Template Tags ##

  * **is\_comic()**: Returns true is post contains a comic. Used to detect comic posts from regular posts.
  * **is\_comic\_page()**: Returns true if page is the Latest Comic Page.
  * **is\_comic\_archive\_page()**: Returns true if page is the Comic Archives Page.


## Comic Template Tags ##

  * **[mangapress\_comic\_navigation()](mangapress_comic_navigation.md)**: Comic navigation function. Used for outputting comic navigation in posts.

## Manga+Press Image Sizes ##
These are meant to be used with [the\_post\_thumbnail](http://codex.wordpress.org/Function_Reference/the_post_thumbnail) function. The sizes are defined on the Manga+Press Options page, under the Comic Page Options tab.

  * **comic-banner** Creates a "banner image" from the uploaded comic image.
  * **comic-page** Creates a "Comic Page" image from the uploaded comic. This image size would be used to display your comic.
  * **comic-admin-thumb** Creates an 80x60 thumbnail for the Comics page in the WordPress admin. This is a private image size.

## Manga+Press Custom Hooks and Filters (for developers) ##

  * **mangapress\_option\_fields**: can be used for modifying array of options fields. Must be run on admin\_init.
  * **mangapress\_option\_section**: can be used to change the available sections/tabs on the Manga+Press Options page (not tested).
  * **template\_include\_single\_comic**: Use to add a template to the template-stack for the Single Comic page.