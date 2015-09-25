# Changelog #

### 2.7 ###
  * 2.7.1
    1. Fixed undefined index notices (WP\_DEBUG turned on)

  * 2.7 RC 1
    1. Moved partial templates to sub-directory inside templates.
    1. Corrected issues in comic-specific conditional functions.
    1. Changed Ajax hooks to be admin-specific.

  * 2.7 Beta 3
    1. Fixed missing template issues.
    1. Fixed issues with "Use theme template" settings.

  * 2.7 Beta 2
    1. Corrected issue with framework paths which prevented the Manga+Press Options forms from displaying properly.
    1. Added closing PHP tags for servers that have short open tags disabled.

  * 2.7 Beta
    1. Eliminated "Insert Banner" and Comic Update codes. These features may return in future versions.
    1. Added custom taxonomies, and post thumbnail support.
    1. Eliminated TimThumb.

### 2.6 ###
  * 2.6.2
    1. Introduced Spanish language support.

  * 2.6.1
    1. Corrected Static page issue. Also changed mpp\_filter\_latest\_comicpage() so that Post title is included in output.

  * 2.6
    1. Fixed bugs that were present in 2.5. Manga+Press options page now located under Settings, Post New Comic page has been moved to Posts and Uninstall Manga+Press is located under Plugins.

  * 2.6b
    1. Changed handling of plugin options so that they are compatible with Wordpress 2.8 and higher. They are now stored in one entry in the options table instead of being spread out over multiple entries. Moved Manga+Press options page to Settings, Uninstall to Plugins, and Post New Comic to Posts. Removed /admin, /css, /js as they were no longer necessary for the plugin to function.

### 2.5 ###
  * 2.1/2.5
    1. 2.1 renamed to 2.5. Eliminated the banner skin option and all functions attached. Feature can be duplicated with a little CSS positioning. Option for creating a banner from uploaded comic or uploading a seperate banner still remains, as well as the option to set banner width & height. Removed both the Manga+Press help and Template Tag pages. Will be hosted in a help wiki on the Manga+Press website. Made changes to the Post Comic page. Also reworded the "New Version" text. Created options to have the comic banner & navigation included at the top of The Loop on the home page, as well automatically filtering comic categories from the front page and automatically modifying The Loop for the latest comic page. Removed the make banner option.

  * 2.0.1-beta
    1. Corrected a minor bug in update\_options. Banner skin wouldn't be uploaded even if "use banner skin" option were checked and user had selected an image for upload. Also corrected a jQuery UI Tabs bug in the user admin area that is present when Manga+Press is used with Wordpress 2.8

### 2.0 ###
  * 2.0-beta
    1. Major reworking of code in mangapress-classes.php and mangapress-functions.php
    1. Reworked code of add\_comic() function so it is compatible with the Wordpress post db and Media Library
    1. removed create directory for series option
    1. added wp\_sidebar\_comic()

### 1.0 ###
  * 1.0 RC2.5
    1. Found a major bug involving directory/file permissions. Has been corrected, but I'm keeping my eye on this one for future reference. See website for a fix.

  * 1.0 RC2
    1. Modified add\_comic(), add\_footer\_info()

  * 1.0 RC1
    1. General maintenance, fixing up look-and-feel of admin side. Putting together companion theme.