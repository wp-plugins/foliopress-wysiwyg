=== Foliopress WYSIWYG ===
Contributors: FolioVision
Tags: wysiwyg,editor,foliopress,image,images,seo,lightbox
Requires at least: 2.2.3
Tested up to: 2.9.1
Stable tag: trunk

Foliopress WYSIWYG is the editor you were always hoping for, every time you installed a new content management system.

== Description ==

Foliopress WYSIWYG is the editor you were always hoping for, every time you installed a new content management system.

* Foliopress WYSIWYG is simple and correctly configured straight out of the box.
* Foliopress WYSIWYG handles images and text equally well.
* Foliopress WYSIWYG gives you **SEO ready images** (properly labelled with caption and alt and title tags).
* Foliopress WYSIWYG is simple enough to use that your clients will love it.
* Foliopress WYSIWYG has all the **extra control and flexibility** you want to be able to do advanced coding on your content pages.
* Foliopress WYSIWYG looks great in your browser window. No more eyesore when using an online text editor. We aren't living in the 90's anymore and our online text editors shouldn't look like WordStar.
* Foliopress WYSIWYG has easy and quick access to source code for experts so your programmers won't get frustrated and turn it off.
* Foliopress WYSIWYG produces **standards compliant html quickly and easily**.
* Foliopress WYSIWYG is forgiving: even if you make some terrible HTML/XHTML errors Foliopress WYSIWYG? will always give you or your clients it's best version of your document without erasing it.
* Foliopress WYSIWYG does **true WYSIWYG**. You can actually see the text in your edit box the same way it is in your content box (simple three step point and click configuration).
* Foliopress WYSIWYG will never go out of date: Foliopress WYSIWYG is assembled from best of breed open source projects so it will always be on the cutting edge of web design. The parts are carefully assembled as modules with no modifications to core code so you can always drop the latest version of the core libraries in for a seamless and instant upgrade.
* Foliopress WYSIWYG is so easy and fun to use, that you just might want to retire your word processor and write all your documents online.
* Uses FCKEditor (FCK) with upgrades, equivalent to CKEditor.
* Includes a fully extended toolbar just like tinyMCE Advanced.

[Support](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg) |
[Change Log](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/changelog)

== Installation ==

You can use the built in installer and upgrader, or you can install the plugin
manually.

== Screenshots ==

1. Foliopress WYSIWYG default toolbar
2. Image Management Window with the insert thumbnail options
3. Options screen

== Frequently Asked Questions ==

= Is Foliopress WYSIWYG able to work with the images already stored on the site? =

Yes it is. Depending on your PHP configuration, you might have to change the directory permissions according to [this guide](http://foliovision.com/seo-tools/wordpress/plugins/wysiwyg/prepare-ftp-files-for-seo-images) in order to make thumbnails work.

= What about my own toolbar? =

You can configure it in the Advanced options.

= What about some more buttons? =

You can customize the styling dropdown.

= Your plugin is not working, all I can see it this: =

> Parse error: syntax error, unexpected T_CONST, expecting T_OLD_FUNCTION or T_FUNCTION or T_VAR or '}' in {your web root}/wp-content/plugins/foliopress-wysiwyg/foliopress-wysiwyg-class.php on line 96

Contact your webhost to switch you to PHP 5, you are probably still running on obsolete PHP 4.

= I get a ugly message like this one: =

> Warning: mkdir() [function.mkdir]: Permission denied in /home/... ..../wp-content/plugins/foliopress-wysiwyg/fckeditor/editor/plugins/kfm/initialise.php on line 172

Make sure your /images directory has the 777 permissions. 755 can be enough too, depending on your PHP configuration (PHP Fast CGI).

= I get the paste as plain text dialogue box whenever I try to paste with Ctrl + V into a post. =

We are doing our best to protect your post from the bad HTML code in case you are pasting from programs like Microsoft Office or web sites.

However, if you still want to disable this dialog by default, do the following: 

1. Open this file: /wp-content/plugins/foliopress-wysiwyg/fckeditor/fckconfig.js
1. Change "FCKConfig.ForcePasteAsPlainText= true ;" to "FCKConfig.ForcePasteAsPlainText= false ;"

= How about different language versions? =

Here's how you can switch the language:

1. open foliopress-wysiwyg/fckeditor/fckconfig.js

2. search for FCKConfig.DefaultLanguage = 'en' ; and change it to
FCKConfig.DefaultLanguage = 'de' ;

3. search for FCKConfig.AutoDetectLanguage = true ; and change it to
FCKConfig.AutoDetectLanguage    = false ;

We will try to make this easier in one of the next versions. Also our
custom buttons and Image Managemetn won't be translated for now.