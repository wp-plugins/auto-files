<?php

// Stop direct call

if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {

				die('You are not allowed to call this page directly.');

}
//Metabox For Pages
add_action( 'add_meta_boxes','aa_meta_init');
add_action('save_post','aa_meta_save');

function aa_meta_init()
{
		add_meta_box('all_page_meta',  __('Show Auto Files?','autof'), 'aa_meta_page', 'page', 'side', 'high');
		add_meta_box('all_post_meta',  __('Show Auto Files?','autof'), 'aa_meta_post', 'post', 'side', 'high');
}


function aa_meta_page($post)
{
	$meta = get_post_meta($post->ID,'aa_post_meta',TRUE);
	?>
	<p><?php _e('Show Files Section','autof');?>
	<select id="aa_post_meta" name="aa_post_meta">
		<option value="yes" <?php selected( $meta, 'yes' ); ?>><?php _e('Yes','autof');?></option>
		<option value="no" <?php selected( $meta, 'no' ); ?>><?php _e('No','autof');?></option>
	</select></p>
	<?php
	echo '<input type="hidden" name="aa_meta_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
	echo '<input type="hidden" name="post_type" value="' .get_post_type( get_the_ID() ). '" />';
}

function aa_meta_post($post)
{
	$meta = get_post_meta($post->ID,'aa_post_meta',TRUE);
	?>
	<p><?php _e('Show Files Section','autof');?>
	<select id="aa_post_meta" name="aa_post_meta">
		<option value="yes" <?php selected( $meta, 'yes' ); ?>><?php _e('Yes','autof');?></option>
		<option value="no" <?php selected( $meta, 'no' ); ?>><?php _e('No','autof');?></option>
	</select></p>
	<?php
	// create a custom nonce for submit verification later
	echo '<input type="hidden" name="aa_meta_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
	echo '<input type="hidden" name="post_type" value="' .get_post_type( get_the_ID() ). '" />';
}

function aa_meta_save()
{
	global $post;
	// authentication checks
	// make sure data came from our meta box
	if (!wp_verify_nonce($_POST['aa_meta_nonce'],__FILE__)) return $post->ID;
	// check user permissions
	if ($_POST['post_type'] == 'page')
	{
		if (!current_user_can('edit_page', $post->ID)) return $post->ID;
	}
	else
	{
		if (!current_user_can('edit_post', $post->ID)) return $post->ID;
	}

	$current_data = get_post_meta($post->ID, 'aa_post_meta', TRUE);

	$new_data = $_POST['aa_post_meta'];

	my_meta_clean($new_data);

	if ($current_data)
	{
		if (is_null($new_data)) delete_post_meta($post->ID,'aa_post_meta');
		else update_post_meta($post->ID,'aa_post_meta',$new_data);
	}
	elseif (!is_null($new_data))
	{
		add_post_meta($post->ID,'aa_post_meta',$new_data,TRUE);
	}

	return $post->ID;
}


function my_meta_clean(&$arr)
{
	if (is_array($arr))
	{
		foreach ($arr as $i => $v)
		{
			if (is_array($arr[$i]))
			{
				my_meta_clean($arr[$i]);
				if (!count($arr[$i]))
				{
					unset($arr[$i]);
				}
			}
			else
			{
				if (trim($arr[$i]) == '')
				{
					unset($arr[$i]);
				}
			}
		}
		if (!count($arr))
		{
			$arr = NULL;
		}
	}
}
?>