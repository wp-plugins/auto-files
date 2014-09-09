<?php
/*
Plugin Name: Auto Files
Plugin URI: http://wpadami.com/cms-sistemleri/wordpress/auto-files-mini-bir-auto-attachments.html
Description: This plugin is minified version of Auto Attachments. Supported attachment types are Word, Excel, Pdf, PowerPoint, zip, rar, tar, tar.gz
Version: 0.6
Author: Serkan Algur
Author URI: http://www.wpadami.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Stop direct call
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
				die('You are not allowed to call this page directly.');
}
// CSS Style Loading

if (!is_admin()){
	wp_enqueue_style('autofilesstyle', plugins_url('/auto-files/autofiles.css'), __FILE__ );
}

function multilingual_af( ) {
				load_plugin_textdomain('autof', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('init', 'multilingual_af');

include('metaboxes.php');

function autof_show_files() {
$files = get_children(array( //do only if there are attachments of these qualifications
		'post_parent' => get_the_ID(),
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_mime_type' => array(
						"application/pdf",
						"application/rar",
						"application/msword",
						"application/vnd.ms-powerpoint",
						"application/vnd.ms-excel",
						"application/zip",
						"application/x-rar-compressed",
						"application/x-tar",
						"application/x-gzip",
						"application/vnd.oasis.opendocument.text",
						"application/vnd.oasis.opendocument.spreadsheet",
						"application/vnd.oasis.opendocument.formula",
						"text/plain",
						"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
						"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
						"application/vnd.openxmlformats-officedocument.presentationml.presentation",
						"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
						"application/x-compress",
						"application/mathcad",
						"application/postscript",
						"applicationvnd.ms-excel.sheet.macroEnabled.12",
		)));

	if ($files) {
		$filehtml .= '<div class="files section group">';
		foreach ($files as $file) //setup array for more than one file attachment
		{
			$file_link       = wp_get_attachment_url($file->ID); //get the url for linkage
			$file_name_array = explode("/", $file_link);
			$file_post_mime  = str_replace("/", "-", $file->post_mime_type);
			$file_post_mime  = str_replace(".", "-", $file_post_mime);
			$file_name       = array_reverse($file_name_array); //creates an array out of the url and grabs the filename
			$filehtml .= "<div class='col span_1_of_6 filein' id='$file->ID'>";
			$filehtml .= "<a href='$file_link' target='_blank'><i class='sfl-".$file_post_mime."'></i></a>";
			$filehtml .= "<a class='filetitle' href='$file_link'><strong>" . $file->post_title . "</strong></a>";
			$filehtml .= "</div>";
		}
		$filehtml .="</div>";
		$filehtml .="<div class='clear'> </div>";
		return $filehtml;
	}
}

add_filter('the_content', 'autof_insertintoContent');

function autof_insertintoContent($content) {
	global $post;
	$metapost = get_post_meta($post->ID,'aa_post_meta',TRUE);
		if (get_post_type() == 'post') {
			if (!post_password_required() && $metapost != "no"){
					$content .= autof_show_files();
					return $content;
			} else {
				return $content;
			}
		} else {
			return $content;
		}

		if (get_post_type() == 'page'){
			if (!post_password_required() && $metapost != "no"){
					$content .= autof_show_files();
					return $content;
			} else {
				return $content;
			}
		} else {
			return $content;
		}
}