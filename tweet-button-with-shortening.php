<?php
/*
Plugin Name: Tweet Button with Shortening
Plugin URI: http://tools.awe.sm/tweet-button/wordpress/
Description: Add an official Twitter tweet button to your blog and fully configure it through your WP admin, including the ability to use it with your favorite URL shortener (currently awe.sm, bit.ly, tinyurl, su.pr, digg). This plugin is based on the great BackType Tweetcount plugin.
Version: 0.2.1
Author: awe.sm
Author URI: http://totally.awe.sm/
*/

/*  Copyright 2010  Snowball Factory, Inc  (email : support+tbws@awe.sm)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if (is_admin()) {
	add_action('admin_menu', 'tbws_options');
	add_action('admin_init', 'tbws_init');
	register_activation_hook(__FILE__, 'tbws_activate');
}

add_filter('the_content', 'tbws_update');
add_filter('get_the_excerpt', 'tbws_remove_filter', 9);

add_action('profile_update', 'tweet_shortened_profile_update');
add_action('show_user_profile', 'tweet_shortened_user_profile');
add_action('edit_user_profile', 'tweet_shortened_user_profile');

function tbws_options() {
	add_options_page('Tweet Button Settings', 'Tweet Button with Shortening', 8, 'tweet-button-with-shortening', 'tbws_options_page');
}

// Register these variables (WP 2.7 & newer)
function tbws_init() {
	if (function_exists('register_setting')) {
		//register_setting('tbws-options', 'tbws_text');
		register_setting('tbws-options', 'tbws_via');
		register_setting('tbws-options', 'tbws_author');
		register_setting('tbws-options', 'tbws_size');
		register_setting('tbws-options', 'tbws_location');
		register_setting('tbws-options', 'tbws_style');
		register_setting('tbws-options', 'tbws_pages');
		register_setting('tbws-options', 'tbws_shortener');
		register_setting('tbws-options', 'tbws_api_key');
		register_setting('tbws-options', 'tbws_login');
	}    
}

// default options
function tbws_activate() {
	//add_option('tbws_text', '');
	add_option('tbws_via', '');
	add_option('tbws_author', '');
	add_option('tbws_size', '');
	add_option('tbws_location', 'top');
	add_option('tbws_style', 'float:left;margin-right:10px;');
	add_option('tbws_pages', '');
	add_option('tbws_shortener', '');
	add_option('tbws_api_key', '');
	add_option('tbws_login', '');
}

// The following two functions are adapted from the Twitter Publisher plugin
/**
 * Save extended profile attributes.
 *
 * @param int $userid ID of user
 */
function tweet_shortened_profile_update($userid) {
    $twitter_username = $_POST['twitter_username'];

    //if the twitter username starts with an @, remove it.
    if(substr($twitter_username, 0, 1) == '@') {
        $twitter_username = substr($twitter_username, 1);
    }

    //now save those values
    update_usermeta($userid, 'twitter_username', $twitter_username);
}

/**
 * Show extended profile attributes.
 */
function tweet_shortened_user_profile() {
    global $profileuser;

    echo '  <h3>'.__('Twitter', 'tweet-button-with-shortening').'</h3>
            <table class="form-table">
            <tr>
                <th><label for="twitter_username">'.__('Twitter username:', 'tweet-button-with-shortening').'</label></th>
                <td>
                    <input type="text" name="twitter_username" id="twitter_username" value="' . $profileuser->twitter_username .'" />
                    <span class="setting-description">'.__('Do not include the <code>@</code> at the beginning.', 'tweet-button-with-shortening').'</span>
                </td>
            </tr>
            </table>';
}


function tbws_update($content) {
	global $post;
	
	if (get_option('tbws_location') == 'manual') {
		return $content;
	}
	
	if (is_feed()) {
		return $content;
	}
	
	if (is_page() and (get_option('tbws_pages') != 'true')) {
		return $content;
	}
	
	if (get_post_meta($post->ID, 'tbws', true) == '') {
		$tweet_button = tweet_button();
		switch (get_option('tbws_location')) {
			case 'topbottom':
				return $tweet_button . $content . $tweet_button;
			break;
			case 'top':
				return $tweet_button . $content;
			break;
			case 'bottom':
				return $content . $tweet_button;
			break;
			default:
				return $tweet_button . $content;
			break;
		}
	} else {
		return $content;
	}
}

function tbws_remove_filter($content) {
	remove_action('the_content', 'tbws_update');
	return $content;
}

function tbws_options_page() {
	echo '<div class="wrap">';
	if (function_exists('screen_icon')) { screen_icon(); }
	echo'<h2>Tweet Button with Shortening</h2>';
	echo '<form method="post" action="options.php">';
	wp_nonce_field('update-options');
	echo '<table class="form-table">';
	echo '<tr valign="top"><th scope="row">Twitter Username for Attribution</th><td><input type="text" name="tbws_via" value="' . get_option('tbws_via') . '" /> <span class="setting-description">Will be appended as a via at the end of all tweets and included in the recommended users after the tweet is sent (do NOT include the leading @ in the username)</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Recommend Post Author</th><td><select name="tbws_author"><option value="">enabled</option><option value="false"' . ((get_option('tbws_author')=='false')?' selected':'') . '>disabled</option></select> <span class="setting-description">If the author has added a Twitter username to their profile, enabling this will add the author of a given post to the recommended users after the tweet is sent</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Count Placement</th><td><select name="tbws_size"><option value="vertical">vertical</option><option value="horizontal"' . ((get_option('tbws_size')=='horizontal')?' selected':'') . '>horizontal</option><option value="none"' . ((get_option('tbws_size')=='none')?' selected':'') . '>none</option></select></td></tr>';
	echo '<tr valign="top"><th scope="row">Location</th><td><select name="tbws_location"><option value="top">top</option><option value="bottom"' . ((get_option('tbws_location')=='bottom')?' selected':'') . '>bottom</option><option value="topbottom"' . ((get_option('tbws_location')=='topbottom')?' selected':'') . '>top &amp; bottom</option><option value="manual"' . ((get_option('tbws_location')=='manual')?' selected':'') . '>manual</option></select> <span class="setting-description">For manual positioning, echo tweet_button(); where you would like the button to appear</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Wrapper Style</th><td><input type="text" name="tbws_style" value="' . get_option('tbws_style') . '" /> <span class="setting-description">CSS for positioning, margins, etc</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Show Button on Pages</th><td><input type="checkbox" value="true" name="tbws_pages"' . ((get_option('tbws_pages')=='true')?' checked':'true') . ' /> <span class="setting-description">Show the button on Pages as well as Posts</span></td></tr>';
	echo '</table><p>The following options allow you to choose which URL shortener you would like to use:</p><table class="form-table">';
	echo '<tr valign="top"><th scope="row">Shortener</th><td><select name="tbws_shortener"><option value="">none</option><option value="awesm"' . ((get_option('tbws_shortener')=='awesm')?' selected':'') . '>awe.sm (custom)</option><option value="bitly"' . ((get_option('tbws_shortener')=='bitly')?' selected':'') . '>bit.ly</option><option value="tinyurl"' . ((get_option('tbws_shortener')=='tinyurl')?' selected':'') . '>tinyurl.com</option><option value="supr"' . ((get_option('tbws_shortener')=='supr')?' selected':'') . '>su.pr</option><option value="digg"' . ((get_option('tbws_shortener')=='digg')?' selected':'') . '>digg</option></select></td></tr>';
	echo '<tr valign="top"><th scope="row">API Key</th><td><input type="text" name="tbws_api_key" value="' . get_option('tbws_api_key') . '" /> <span class="setting-description">Required: bit.ly, awe.sm, optional: su.pr</span></td></tr>';
	echo '<tr valign="top"><th scope="row">Login</th><td><input type="text" name="tbws_login" value="' . get_option('tbws_login') . '" /> <span class="setting-description">Required: bit.ly, optional: su.pr</span></td></tr>';
	echo '</table>';
	echo '<input type="hidden" name="action" value="update" /><input type="hidden" name="page_options" value="tbws_text,tbws_via,tbws_links,tbws_size,tbws_pages,tbws_location,tbws_style,tbws_background,tbws_border,tbws_text,tbws_shortener,tbws_api_key,tbws_login" /><p class="submit"><input type="submit" class="button-primary" value="Save Changes" /></p></form></div>';
}

function tweet_button($src=null, $via=null, $author=null, $size=null, $style=null, $shortener=null, $api_key=null, $login=null) {
	global $post;
	$url = '';
	$cnt = null;
	
	// let users override these vars when calling manually
	//$text = ($text === null) ? get_option('tbws_text') : $text;
	$via = ($via === null) ? get_option('tbws_via') : $via;
	$author = ($author === null) ? get_option('tbws_author') : $via;
	$size = ($size === null) ? get_option('tbws_size') : $size;
	$style = ($style === null) ? get_option('tbws_style') : $style;
	$shortener = ($shortener === null) ? get_option('tbws_shortener') : $shortener;
	$api_key = ($api_key === null) ? get_option('tbws_api_key') : $api_key;
	$login = ($login === null) ? get_option('tbws_login') : $login;
	
	if (get_post_status($post->ID) == 'publish') {
		$url = get_permalink();
		$title = $post->post_title;
		
		if ((function_exists('curl_init') || function_exists('file_get_contents')) && function_exists('unserialize')) {

			if ($shortener && get_post_meta($post->ID, 'tbws_short_url', true) == '') {
				$short_url = null;
				switch ($shortener) {
					case 'awesm':
						$short_url = tbws_shorten_awesm($url, $api_key);
					break;
					case 'bitly':
						$short_url = tbws_shorten_bitly($url, $api_key, $login);
					break;
					case 'tinyurl':
						$short_url = tbws_shorten_tinyurl($url);
					break;
					case 'digg':
						$short_url = tbws_shorten_digg($url);
					break;
					case 'supr':
						$short_url = tbws_shorten_supr($url, $api_key, $login);
					break;
				}
				if ($short_url) {
					add_post_meta($post->ID, 'tbws_short_url', $short_url);
				}
			}
		}
	}


	if ($style !== '') {
		$tweet_button = '<div style="' . $style . '">';
	} else {
		$tweet_button = '';
	}

	$tweet_button .= '<a href="http://twitter.com/share" class="twitter-share-button"';
	
	if ($shortener && get_post_meta($post->ID, 'tbws_short_url', true) != '') {
		$tweet_button .= ' data-url="' . wp_specialchars(get_post_meta($post->ID, 'tbws_short_url', true), '1') . '"';
	} else {
		$tweet_button .= ' data-url="' . wp_specialchars($url, '1') . '"';
	}

    $tweet_button .= ' data-counturl="' . wp_specialchars($url, '1') . '"';
	
	$tweet_button .= ' data-text="' . wp_specialchars($title, '1') . '"'; 
	
	$tweet_button .= ' data-count="' . wp_specialchars($size, '1') . '"'; 
	
	if ($via && $via != '') {
		//if the twitter username starts with an @, remove it.
    	if(substr($via, 0, 1) == '@') {
        	$via = substr($via, 1);
    	}
		$tweet_button .= ' data-via="' . wp_specialchars($via, '1') . '"';
	}
	
	if ($author != 'false') {
		//get author
    	$author = get_userdata($post->post_author);
		//check if author's twitter username has been filled in
        if(!empty($author->twitter_username)) {
			$tweet_button .= ' data-related="' . wp_specialchars($author->twitter_username, '1') . ':Author of the post"';
        } 		
	} 
	
	$tweet_button .= '>Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
	
	if ($style !== '') {
		$tweet_button .= '</div>';
	}
			 
	return $tweet_button;
}

function tbws_urlopen($url) {
	if (function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	} else {
		return file_get_contents($url);
	}
}


// Code added by @jhstrauss
function tbws_shorten_awesm($url, $api_key) {
	if (function_exists('curl_init')) {
		$awesm_vars = array(
			"version" => "1",
			"target" => $url,
			"share_type" => "twitter",
			"create_type" => "tweet-button",
			"campaign" => "static",
			"api_key" => $api_key
		);

		$awesm_create = curl_init();
		curl_setopt($awesm_create, CURLOPT_URL, "http://create.awe.sm/url.txt");
		curl_setopt($awesm_create, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($awesm_create, CURLOPT_TIMEOUT, 30);
		curl_setopt($awesm_create, CURLOPT_POST, 1); 
		curl_setopt($awesm_create, CURLOPT_POSTFIELDS, $awesm_vars);
		$awesm_create_response = curl_exec($awesm_create);
		$awesm_create_response_code = curl_getinfo($awesm_create, CURLINFO_HTTP_CODE);
		curl_close($awesm_create);
		// Verify HTTP response code received
		if ($awesm_create_response_code == 200) {
			return $awesm_create_response;
		}		
	}
	return false;
}

// Code added by @michaelmontano
function tbws_shorten_bitly($url, $api_key, $login='') {
	if ($api_key && function_exists('json_decode')) {
		$bitly_url = 'http://api.bit.ly/shorten';
		$bitly_version = '2.0.1';
		$bitly_vars = '?version=' . $bitly_version . '&longUrl=' . urlencode($url) .
						'&login=' . $login . '&apiKey=' .$api_key;
						
		$response =  tbws_urlopen($bitly_url . $bitly_vars);
		if ($response) {
			$data = json_decode($response, true);
			if (isset($data['results'])) {
				$keys = array_keys($data['results']);
				if (isset($data['results'][$keys[0]]['shortCNAMEUrl'])) {
					return $data['results'][$keys[0]]['shortCNAMEUrl'];
				} elseif (isset($data['results'][$keys[0]]['shortUrl'])) {
					return $data['results'][$keys[0]]['shortUrl'];
				}
			}
		}
	}
	return false;
}

// Code added by @michaelmontano
function tbws_shorten_digg($url) {
	if (function_exists('curl_init')) {
		class DiggAPIShortURLs {};
		class DiggAPIShortURL {};
		
		$digg_url = 'http://services.digg.com/url/short/create';
		$digg_vars = '?type=php&url=' . urlencode($url) . '&appkey=http%3A%2F%2Ftools.awe.sm%2Ftweet-button%2Fwordpress';
		$req_url = $digg_url . $digg_vars;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $req_url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'tweet-button-with-shortening');
		$response = curl_exec($ch);
		curl_close($ch);
		
		if ($response) {
			$data = unserialize($response);
			if (isset($data->shorturls[0]->short_url)) {
				return $data->shorturls[0]->short_url;
			}
		}
	}
	return false;
}

// Code added by @michaelmontano
function tbws_shorten_tinyurl($url) {
	$tinyurl_url = 'http://tinyurl.com/api-create.php';
	$tinyurl_vars = '?url=' . urlencode($url);
	
	$response = tbws_urlopen($tinyurl_url . $tinyurl_vars);
	if ($response) {
		return $response;
	}
	return false;
}

// Code added by @appdevnet
function tbws_shorten_supr($url, $api_key='', $login='') {
	$su_url = 'http://su.pr/api';
	$su_vars = '?url=' . urlencode($url) . '&login=' . $login . '&apiKey=' .$api_key;
	$req_url = $su_url . $su_vars;

	$curl_handle = curl_init();
	curl_setopt($curl_handle, CURLOPT_URL, $req_url);
	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handle, CURLOPT_HTTPGET, 1); 
	$buffer = curl_exec($curl_handle);
	curl_close($curl_handle);
	$su_short_url = $buffer;
	// uncomment if hosting off own domain
	//$su_short_url = str_replace('su.pr/', '', $buffer);

	return $su_short_url;
}
