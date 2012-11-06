=== bbPress Code Snippets ===
Author URI: http://www.jasonbobich.com
Contributors: themeblvd
Tags: bbpress, code, pre, snippets, topics, replies
Stable Tag: 1.0.1

Automatically display HTML/PHP code posted in bbPress topics and replies.

== Description ==

No fancy, complicated syntax highlighting here. The goal of this plugin is to allow your bbPress users to post code snippets in their forum topics and replies without having to learn anything new. Basically, any raw HTML/PHP posted within `<pre>` or `<code>` tags will automatically be converted and displayed in the forum as you'd expect it would. 

And additionally, anything posted within `<pre>` tags will be automatically escaped from bbPress applying wpautop. This will allow for your forum users' code snippets to keep their whitespace as they intend.

== Installation ==

1. Upload `bbpress-code-snippets` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

= 1.0.1 =

* Added `<pre>` tag to WordPress's global $allowedtags so non-admin users can actually post with the `<pre>` tag.

= 1.0.0 =

* This is the first release.