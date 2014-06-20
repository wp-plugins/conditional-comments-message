=== Conditional Comments Message ===
Tags: comments, message, after, conditional, automatically close
Requires at least: 3.5
Tested up to: 3.9
Contributors: jp2112
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7EX9NB9TLFHVW
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a message when blog comments are set to automatically close.

== Description ==

This plugin lets you display a conditional message when you have comments set to close automatically. After you publish a post, and while comments are open, it will show how long comments are open. After the comments section automatically closes, it will say "This article is closed to future comments."

Use it for:

<ul>
<li>Displaying how long comments are open</li>
<li>Show a different message depending on whether comments are open or closed</li>
</ul>

You can also add additional text that will display only when comments are open.


== Installation ==

1. Upload plugin file through the WordPress interface.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings &raquo; Conditional Comments Message, configure plugin.
4. Go to Settings &raquo; Discussion and tick the "Automatically close comments on articles older than [ ] days" checkbox, and put a value there
4. Check a post or page that allows comments and one that doesn't.

== Frequently Asked Questions ==

= How do I use the plugin? =

Go to Settings &raquo; Conditional Comments Message and enter the text you want to see below the comment form. Make sure the "enabled" checkbox is checked. 

= I entered some text but don't see anything on the page. =

Is the page/post cached?

Did you set the number of days comments are set to automatically close? See the Installation page for instructions.

= How can I style the output? =

The text is wrapped in div tags with class name "ccm-conditional-msg". Use this class in your local stylesheet to style the output how you want.

= I don't want the admin CSS. How do I remove it? =

Add this to your functions.php:

`remove_action('admin_head', 'insert_ccm_admin_css');`

== Screenshots ==

1. Plugin settings page

== Changelog ==

= 0.0.3 =
- code fix

= 0.0.2 =
- updated support tab

= 0.0.1 =
- created

== Upgrade Notice ==

= 0.0.6 =
- code fix

= 0.0.2 =
- updated support tab

= 0.0.1 =
created