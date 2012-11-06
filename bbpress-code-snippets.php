<?php
/*
Plugin Name: bbPress Code Snippets
Description: Automatically display HTML/PHP code posted in bbPress topics and replies.
Version: 1.0.1
Author: Jason Bobich
Author URI: http://jasonbobich.com
License: GPL2

    Copyright 2012  Jason Bobich

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 2,
    as published by the Free Software Foundation.

    You may NOT assume that you can use any other version of the GPL.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    The license for this software can likely be found here:
    http://www.gnu.org/licenses/gpl-2.0.html

*/

/**
 * Initiate plugin
 *
 * @since 1.0.0
 */
function themeblvd_bb_code_snippets(){
	
	// Allow normal user's to use <pre> tags.
	add_action( 'init', 'themeblvd_bb_add_pre_tags' );
	
	// Convert PHP open/close tags so they'll save safely.
	add_filter( 'bbp_edit_reply_pre_content', 'themeblvd_bb_save_php', 9 );
	add_filter( 'bbp_new_reply_pre_content', 'themeblvd_bb_save_php', 9 );
	add_filter( 'bbp_new_topic_pre_content', 'themeblvd_bb_save_php', 9 );
	add_filter( 'bbp_edit_topic_pre_content', 'themeblvd_bb_save_php', 9 );
	
	// Put non-executable PHP back for display.
	add_filter( 'bbp_get_form_reply_content', 'themeblvd_bb_display_php' );
	add_filter( 'bbp_get_form_topic_content', 'themeblvd_bb_display_php' );
	add_filter( 'bbp_get_reply_content', 'themeblvd_bb_display_php', 2 );
	add_filter( 'bbp_get_topic_content', 'themeblvd_bb_display_php', 2 );

	// Convert HTML entities between <pre> and <code> in replies/topics.
	add_filter( 'bbp_get_reply_content', 'themeblvd_bb_code', 2 );
	add_filter( 'bbp_get_topic_content', 'themeblvd_bb_code', 2 );

	// Remove bbPress wpautop on replies/topics
	remove_filter( 'bbp_get_reply_content', 'wpautop', 30 );
	remove_filter( 'bbp_get_topic_content', 'wpautop', 30 );
	
	// And now, loop through and put wpautop back on the chunks that 
	// are not in <pre> tags.
	add_filter( 'bbp_get_reply_content', 'themeblvd_bb_content_formatter' );
	add_filter( 'bbp_get_topic_content', 'themeblvd_bb_content_formatter' );
	
}
add_action( 'after_setup_theme', 'themeblvd_bb_code_snippets' );

/**
 * Convert PHP open/close tags so they'll save.
 *
 * @since 1.0.0
 *
 * @param string $content Content sent from reply/topic form
 * @return string #content Content after php open/close tags have been disabled
 */
function themeblvd_bb_save_php( $content ){
	$content = str_replace( '<?', '&lt;?', $content );
	$content = str_replace( '?>', '?&gt;', $content );
	return $content;
}

/**
 * Put non-executable PHP back for display
 * 
 * @since 1.0.0
 *
 * @param string $content Content retrieved for reply or topic
 * @return string $content Content after open/close php tags have been put back for display 
 */
function themeblvd_bb_display_php( $content ){
	$content = str_replace( '&lt;?', '<?', $content );
	$content = str_replace( '&amp;lt;?', '<?', $content );
	$content = str_replace( '?&gt;', "?>", $content );
	$content = str_replace( '?&amp;gt;', "?>", $content );
	return $content;
}

/**
 * Add filter to bbPress's Topics and Reply 
 * content to convert any HTML code inserted 
 * between <code> and <pre> tags.
 * 
 * @since 1.0.0
 *
 * @param string $content Content retrieved for reply or topic
 * @return string $content Content after HTML etities have been converted
 */
function themeblvd_bb_code( $content ){
	// Format HTML entities
	$content = preg_replace_callback('#<(code|pre)([^>]*)>(((?!</?\1).)*|(?R))*</\1>#si', 'themeblvd_bb_convert_html', $content );
	return $content;
}

/**
 * Callback for preg_replace_callback to 
 * convert HTML entities.
 *
 * @since 1.0.0
 *
 * @param array $matches Matches from preg_replace_callback
 * @return string $code_block Code block after HTML entities have been converted
 */
function themeblvd_bb_convert_html( $matches ) {  
    // Create code block
    $code_block = '<'.$matches[1].$matches[2].'>'.htmlspecialchars(substr(str_replace('<'.$matches[1].$matches[2].'>', '', $matches[0]), 0, -(strlen($matches[1]) + 3))).'</'.$matches[1].'>';
    return $code_block;
}  

/**
 * Format content for replies and topics assuming that 
 * wpautop has been removed.
 * 
 * @since 1.0.0
 *
 * @param string $content Content retrieved for reply or topic
 * @return string $new_content Final content chunks put back together
 */
function themeblvd_bb_content_formatter( $content ) {
	
	$new_content = '';
	
	// Patterns for <pre> tag
	$pattern_full = '{(<pre>.*?\</pre>)}is';
	$pattern_contents = '{<pre>(.*?)</pre>}is';
	
	// Split into pieces
	$pieces = preg_split( $pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE );
	
	// Loop through and put wpautop on standard content 
	// and put code back in code tags.
	foreach( $pieces as $piece ) {
		if( preg_match( $pattern_contents, $piece, $matches ) )
			$new_content .= '<pre>'.trim($matches[1]).'</pre>';
		else
			$new_content .= shortcode_unautop( wpautop( $piece ) );
	}
	
	return $new_content;
}

/** 
 * All <pre> tags in replies/topics
 *
 * @since 1.0.1
 */
function themeblvd_bb_add_pre_tags(){
	global $allowedtags;
	$allowedtags['pre'] = array();
}