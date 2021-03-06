=== Tweet Button with Shortening ===
Contributors: snowballfactory
Tags: twitter, tweets, retweets, tweet button, awe.sm, bit.ly, digg, su.pr, tinyurl, url shortener, counter, badge, count, tweet count, tweetcount, tweet, bit.ly pro, social, share, sharing
Requires at least: 2.7
Tested up to: 3.0.1
Stable tag: 1.0

Adds a fully configurable official Twitter tweet button to your blog, supports shortening via awe.sm, bit.ly, tinyurl, su.pr, and digg.

== Description ==
Adds a fully configurable official Twitter tweet button to your blog. You can also set it up to use awe.sm, bit.ly, tinyurl, su.pr, or digg to shorten the links shared through the tweet button.

Features:

* Configure the placement of the button on your posts: top; bottom; top & bottom; or manual
* Choose the type of Twitter tweet button you want: vertical count; horizontal count; or no count
* Optionally use [awe.sm](http://awe.sm), [bit.ly](http://bit.ly/), [su.pr](http://su.pr/), [digg.com](http://digg.com/), or [tinyurl](http://tinyurl.com/) to shorten the links shared through the tweet button (default is Twitter's t.co)
* Specify the via Twitter username to be included at the end of the tweet and in the recommended users to follow screen after the tweet 
* Optionally add the author of a given post to the recommended users to follow screen after the tweet (requires the author to enter their Twitter username in their WP profile)
* Disable the button on Pages
* Localize the button in the languages Twitter supports (currently English, French, German, Spanish, Japanese)
* Specify additional user accounts to recommend

This plugin is heavily based off the excellent [BackType Tweetcount](http://wordpress.org/extend/plugins/backtype-tweetcount/) and borrows from the also excellent [Twitter Publisher](http://wordpress.org/extend/plugins/twitter-publisher/). It is developed by the folks behind [awe.sm](http://totally.awe.sm).

== Installation ==

1. Download the plugin
2. Unzip the plugin and upload the `tweet-button-with-shortening` directory to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Configure the plugin through the 'Settings' &gt; 'Tweet Button with Shortening' menu option

== Frequently Asked Questions ==

= How does manual positioning work? =

To use manual positioning, `echo tweet_button();` where you would like the plugin to appear in your WordPress theme.

= How can I use a URL shortener other than the ones listed? =

Add a custom field to your post named `tbws_short_url` and enter the short URL you would like to use.

== Screenshots ==

1. Use 3rd-party URL shorteners, like awe.sm, and specify the via username
2. Add the author of a given post to the recommended users to follow after the tweet

== Support ==

If you're having issues with this plugin, please let us know at support+tbws [at] awe.sm

== Changelog ==

= 1.0 =
* Improved performance by embedding the frame directly instead of the Javascript.
* Switched to using a unique awe.sm link per tweet instead of caching it per post.

= 0.3 =
* Added support for tweet button localization (English, French, German, Spanish, Japanese)
* You can now customize the description of the author in the recommended users list (primarily for localization, default is still "Author of the post")
* You can now add a comma separated list of additional Twitter users to recommend (the button will accept up to 6 total and only show 2 at a time)
* Fixed a bug that prevented you from disabling the option to recommend the post author

= 0.2.1 =
* Corrected a bug with the option to display the button on Pages

= 0.2 =
* First public release
