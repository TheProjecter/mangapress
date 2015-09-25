## Description: ##

Generates an unordered list of links for navigating between comic posts. Should be used inside a loop.

## Usage: ##

`<?php mangapress_comic_navigation(); ?>`

## Parameters: ##

  * **$query** (WP\_Query) WP\_Query object.
  * **$args** (args) Navigation output arguments
  * **$echo** (boolean) Specifies whether to echo comic navigation or return it as a string. Defaults to true.

## $args Parameters: ##

  * **$container**: Wrapper tag for comic navigation. Defaults to nav. Use false for no container.
  * **$container\_attr**: Attributes for container tag. Can be used for setting custom IDs or classes. Defaults to array(‘id’ => ‘comic-navigation’).
  * **$items\_wrap**: Navigation items wrapper. Defaults to `<ul%1$s>%2$s</ul>`. Can be useful for those situations when you don’t want the navigation to be an unordered list.
  * **$items\_wrap\_attr**: Same as $container\_attr.
  * **$link\_wrap**: Wrapper tag for navigation link. Defaults to li.
  * **$link\_before**: Content before navigation link (but inside tag specified by $link\_wrap).
  * **$link\_after**: Content after navigation link (but inside tag specified by $link\_wrap).