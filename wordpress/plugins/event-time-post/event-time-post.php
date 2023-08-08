<?php
/*
Plugin Name: Event End Time Plugin
Description: Adds an event end time field to posts.
Version: 1.0
Author: Mahdi Najafzadeh
*/

// Add custom meta box
function event_end_time_meta_box() {
	add_meta_box(
		'event_end_time_meta_box',
		'Event End Time',
		'event_end_time_meta_box_callback',
		'post',
		'side'
	);
}
add_action('add_meta_boxes', 'event_end_time_meta_box');

// Callback function for the meta box
function event_end_time_meta_box_callback($post) {
	wp_nonce_field('event_end_time_nonce', 'event_end_time_nonce');
	$timestamp_value = get_post_meta($post->ID, '_event_end_timestamp', true);
	$date_value = '';
	$time_value = '';
	if ($timestamp_value) {
		$timestamp = explode(' ', $timestamp_value);
		$date_value = $timestamp[0];
		$time_value = $timestamp[1];
	}
	?>
    <table>
        <tr>
            <td>
                <label for="event_end_date">Date</label>
                <input type="date" id="event_end_date" name="event_end_date"
                       value="<?php echo esc_attr($date_value); ?>">
            </td>
        </tr>
        <tr>
            <td>
                <label for="event_end_time">Time</label>
                <input type="time" id="event_end_time" name="event_end_time"
                       value="<?php echo esc_attr($time_value); ?>">
            </td>
        </tr>
    </table>
	<?php
}

// Save meta box data
function save_event_end_time_meta_box($post_id) {
	if (!isset($_POST['event_end_time_nonce']) || !wp_verify_nonce($_POST['event_end_time_nonce'], 'event_end_time_nonce')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (isset($_POST['event_end_date']) && isset($_POST['event_end_time'])) {
		$event_end_date = sanitize_text_field($_POST['event_end_date']);
		$event_end_time = sanitize_text_field($_POST['event_end_time']);
		$timestamp = strtotime("$event_end_date $event_end_time");
		if ($timestamp) {
			update_post_meta($post_id, '_event_end_timestamp', date('Y-m-d H:i:s', $timestamp));
		}
	}
}
add_action('save_post', 'save_event_end_time_meta_box');

// Enqueue JavaScript to pre-fill date and time values
function enqueue_event_end_time_script() {
	if (is_admin()) {
		wp_enqueue_script('event-end-time-script', plugin_dir_url(__FILE__) . 'event-end-time-script.js', array('jquery'), '1.0', true);
	}
}
add_action('admin_enqueue_scripts', 'enqueue_event_end_time_script');


include_once "custom_api.php";
