<?php
/*
	Plugin Name: WC Vendors Vendor List Widget
	Description: Adds a widget containing a list of all WC Vendors vendors
	Version: 1.1.0
	Author: <a href="https://github.com/lkarinja">Leejae Karinja</a>
	License: GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
	Copyright 2017 Leejae Karinja

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
	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Prevents execution outside of core WordPress
if(!defined('ABSPATH')){
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit;
}

// Register and load the widget
function load_widget()
{
	register_widget('wc_vendors_vendor_list_widget');
}
add_action('widgets_init', 'load_widget');

// Widget class
class WC_Vendors_Vendor_List_Widget extends WP_Widget
{
	/**
	 * WordPress Widget Constructor
	 *
	 * Sets necessary fields for the plugin/widget
	 */
	function __construct()
	{
		parent::__construct(
			'wc_vendors_vendor_list_widget',
			__('WC Vendors Vendor List', 'wc_vendors_vendor_list_widget'),
			array(
				'description' => __('Adds a a dropdown of all WC Vendors vendors', 'wc_vendors_vendor_list_widget'),
			)
		);
	}

	/**
	 * Function called for displaying the Widget in the Frontend
	 */
	public function widget($args, $instance)
	{
		$title = apply_filters('widget_title', $instance['title']);
		$type = $instance['type'];

		echo $args['before_widget'];
		if(!empty($title)){
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Display the list of Vendors as a Dropdown
		if($type == 'Dropdown'){
			echo $this->get_vendor_list_as_dropdown();
		}
		// Display the list of Vendors as an Unordered List
		if($type == 'List'){
			echo $this->get_vendor_list_as_list();
		}

		echo $args['after_widget'];
	}

	/**
	 * Function called for displaying and configuring the Widget in the Backend
	 */
	public function form($instance)
	{
		if(isset($instance['title'])){
			$title = $instance['title'];
		}else{
			$title = __('Vendors', 'wc_vendors_vendor_list_widget');
		}

		if(isset($instance['type'])){
			$type = $instance['type'];
		}else{
			$type = __('Dropdown', 'wc_vendors_vendor_list_widget');
		}

		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat"
				id="<?php echo $this->get_field_id('title'); ?>"
				name="<?php echo $this->get_field_name('title'); ?>"
				type="text"
				value="<?php echo esc_attr($title); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Type:'); ?></label>
			<select class='widefat'
				id="<?php echo $this->get_field_id('type'); ?>"
				name="<?php echo $this->get_field_name('type'); ?>"
				type="text">
				<option value='Dropdown'<?php echo ($type=='Dropdown')?'selected':''; ?>>
					Dropdown
				</option>
				<option value='List'<?php echo ($type=='List')?'selected':''; ?>>
					List
				</option>
			</select>
		</p>

		<?php
	}

	/**
	 * Function called for updating the display of the Widget
	 */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['type'] = (!empty($new_instance['type'])) ? strip_tags($new_instance['type']) : '';
		return $instance;
	}

	/**
	 * Gets a dropdown list of WC Vendors vendors
	 *
	 * Original Author: WC Vendors (Jamie Madden http://github.com/digitalchild)
	 * Modified By: Leejae Karinja
	 * License: GPL2
	 */
	public function get_vendor_list_as_dropdown()
	{
		$html = '';

		// Arguments for DB Query
		$vendor_total_args = array(
			'role' => 'vendor',
			'order' => 'ASC',
		);

		// Query the WordPress Database for all Vendors
		$vendor_query = New WP_User_Query($vendor_total_args);
		$vendors = $vendor_query->get_results();

		// Create HTML dropdown list with redirects to Vendor Pages
		$html .= '<select onChange="window.location.href=this.value">';

		$html .= '<option value="javascript:void(0)" selected="selected">--Select Vendor--</option>';

		// For all vendors, add their display name to the dropdown list and a link to their shop page
		foreach($vendors as $vendor){
			$html .= '<option value="' . WCV_Vendors::get_vendor_shop_page($vendor->ID) . '">';
			$html .= $vendor->display_name;
			$html .= '</option>';
		}

		$html .= '</select>';

		return $html; 
	}

	/**
	 * Gets an unordered list of WC Vendors vendors
	 *
	 * Original Author: WC Vendors (Jamie Madden http://github.com/digitalchild)
	 * Modified By: Leejae Karinja
	 * License: GPL2
	 */
	public function get_vendor_list_as_list()
	{
		$html = '';

		// Arguments for DB Query
		$vendor_total_args = array(
			'role' => 'vendor',
			'order' => 'ASC',
		);

		// Query the WordPress Database for all Vendors
		$vendor_query = New WP_User_Query($vendor_total_args);
		$vendors = $vendor_query->get_results();

		// Create HTML unordered list with redirects to Vendor Pages
		$html .= '<ul>';

		// For all vendors, add their display name to the unordered list and a link to their shop page
		foreach($vendors as $vendor){
			$html .= '<li><a href="' . WCV_Vendors::get_vendor_shop_page($vendor->ID) . '">';
			$html .= $vendor->display_name;
			$html .= '</a></li>';
		}

		$html .= '</ul>';

		return $html; 
	}
}
