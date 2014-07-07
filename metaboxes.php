<?php
// Stop direct call
if (preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) {
				die('You are not allowed to call this page directly.');
}


//Metabox For Pages
add_action('admin_init','aa_meta_init');

function aa_meta_init()
{
		add_meta_box('all_page_meta',  __('Show Auto Files?','autof'), 'aa_meta_page', 'page', 'side', 'high');
		add_meta_box('all_post_meta',  __('Show Auto Files?','autof'), 'aa_meta_post', 'post', 'side', 'high');

		add_action('save_post','aa_meta_save');
}

function aa_meta_page()
{
	global $post;

	$meta = get_post_meta($post->ID,'aa_page_meta',TRUE);
	if (isset($meta['show'])){$show = $meta['show'];}

	?>
	<p><?php _e('Show Files Section','autof');?>
	<select id="aa_post_meta" name="aa_page_meta[show]">
		<option value="yes" <?php if ($gshow == "yes") { _e('selected'); } ?>><?php _e('Yes','autof');?></option>
		<option value="no" <?php if ($gshow == "no") { _e('selected'); } ?>><?php _e('No','autof');?></option>
	</select></p>
	<?php
	echo '<input type="hidden" name="my_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function aa_meta_post()
{
	global $post;
	$meta = get_post_meta($post->ID,'aa_post_meta',TRUE);
	if (isset($meta['show'])){$gshow = $meta['show'];}
	?>
	<p><?php _e('Show Files Section','autof');?>
	<select id="aa_post_meta" name="aa_post_meta[show]">
		<option value="yes" <?php if ($gshow == "yes") { _e('selected'); } ?>><?php _e('Yes','autof');?></option>
		<option value="no" <?php if ($gshow == "no") { _e('selected'); } ?>><?php _e('No','autof');?></option>
	</select></p>
	<?php
	// create a custom nonce for submit verification later
	echo '<input type="hidden" name="my_meta_noncename" value="' . wp_create_nonce(__FILE__) . '" />';
}

function aa_meta_save($post_id)
{
	// authentication checks
	// make sure data came from our meta box
	if (!wp_verify_nonce($_POST['my_meta_noncename'],__FILE__)) return $post_id;
	// check user permissions
	if ($_POST['post_type'] == 'page')
	{
		if (!current_user_can('edit_page', $post_id)) return $post_id;
		$current_data = get_post_meta($post_id, 'aa_page_meta', TRUE);
		$new_data = $_POST['aa_page_meta'];
		my_meta_clean($new_data);
		if ($current_data)
		{
			if (is_null($new_data)) delete_post_meta($post_id,'aa_page_meta');
			else update_post_meta($post_id,'aa_page_meta',$new_data);
		}
		elseif (!is_null($new_data))
		{
			add_post_meta($post_id,'aa_page_meta',$new_data,TRUE);
		}
		return $post_id;
	}
	elseif($_POST['post_type'] == 'post')
	{
		if (!current_user_can('edit_post', $post_id)) return $post_id;

		$current_data = get_post_meta($post_id, 'aa_post_meta', TRUE);
		$new_data = $_POST['aa_post_meta'];
		my_meta_clean($new_data);
		if ($current_data)
		{
			if (is_null($new_data)) delete_post_meta($post_id,'aa_post_meta');
			else update_post_meta($post_id,'aa_post_meta',$new_data);
		}
		elseif (!is_null($new_data))
		{
			add_post_meta($post_id,'aa_post_meta',$new_data,TRUE);
		}
		return $post_id;

	}
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