<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Featured Services Widget

-----------------------------------------------------------------------------------*/

// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_featured_services_widgets' );

// Register widget.
function transfers_featured_services_widgets() {
	register_widget( 'transfers_Featured_Services_Widget' );
}

// Widget class.
class transfers_Featured_Services_Widget extends WP_Widget {

	/*-----------------------------------------------------------------------------------*/
	/*	Widget Setup
	/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_featured_services_widget', 'description' => esc_html__('Transfers: Featured Services', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 260, 'height' => 400, 'id_base' => 'transfers_featured_services_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_featured_services_widget', esc_html__('Transfers: Featured Services', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		
		global $transfers_plugin_globals, $transfers_services_post_type;
		
		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);
		
		extract( $args );
		
		/* Our variables from the widget settings. */
		
		$number_of_services = isset($instance['number_of_services']) ? (int)$instance['number_of_services'] : 4;
		$sort_by = isset($instance['sort_by']) ? $instance['sort_by'] : 'title';
		$sort_descending = isset($instance['sort_by']) && $instance['sort_descending'] == '1';
		$order = $sort_descending ? 'DESC' : 'ASC';
		$services_per_row = isset($instance['services_per_row']) ? (int)$instance['services_per_row'] : 4;
		global $display_mode;
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
		
		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		echo wp_kses($before_widget, $allowedtags);
		
		if ($display_mode == 'card') {
		?>
		<div class="services boxed white">
		<?php
		} else {
		?>
		<div class="services boxed white small-list">
		<?php
		}
		
		/* Display Widget */
				
		$results = $transfers_services_post_type->list_services(0, $number_of_services, $sort_by, $order);
		
		if ( count($results) > 0 && $results['total'] > 0 ) {
			foreach ($results['results'] as $result) {
				global $post;				
				$post = $result;
				setup_postdata( $post ); 
				global $post_class;
				if (isset($card_layout_classes[$services_per_row - 1]))
					$post_class = $card_layout_classes[$services_per_row - 1];
				else
					$post_class = 'one-fourth';
				get_template_part('includes/parts/service', 'item');
			}			
		}
		?></div><?php

		/* After widget (defined by themes). */
		echo wp_kses($after_widget, $allowedtags);
		
		// set back to default since this is a global variable
		$display_mode = 'card';
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['number_of_services'] = strip_tags( $new_instance['number_of_services']);
		$instance['sort_by'] = strip_tags( $new_instance['sort_by']);
		$instance['sort_descending'] = strip_tags( $new_instance['sort_descending']);
		$instance['display_mode'] = strip_tags( $new_instance['display_mode']);
		$instance['services_per_row'] = strip_tags( $new_instance['services_per_row']);
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
			
		/* Set up some default widget settings. */
		$defaults = array(
			'number_of_services' => '4',
			'sort_by' => 'title',
			'sort_descending' => '1',
			'display_mode' => 'card',
			'services_per_row' => 4
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_services' ) ); ?>"><?php esc_html_e('How many services do you want to display?', 'transfers') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_services' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_services' ) ); ?>">
				<?php for ($i=1;$i<13;$i++) { ?>
				<option <?php echo ($i == $instance['number_of_services'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>"><?php esc_html_e('What do you want to sort the services by?', 'transfers') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'sort_by' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_by') ); ?>">
				<option <?php echo 'title' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="title"><?php esc_html_e('Post Title', 'transfers') ?></option>
				<option <?php echo 'ID' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="ID"><?php esc_html_e('Post ID', 'transfers') ?></option>
				<option <?php echo 'rand' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="rand"><?php esc_html_e('Random', 'transfers') ?></option>
				<option <?php echo 'date' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="date"><?php esc_html_e('Publish Date', 'transfers') ?></option>
				<option <?php echo 'comment_count' == $instance['sort_by'] ? 'selected="selected"' : ''; ?> value="comment_count"><?php esc_html_e('Comment Count', 'transfers') ?></option>
			</select>
		</p>		

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>"><?php esc_html_e('Sort services in descending order?', 'transfers') ?></label>
			<input type="checkbox"  <?php echo ($instance['sort_descending'] == '1' ? 'checked="checked"' : ''); ?> class="checkbox" id="<?php echo esc_attr( $this->get_field_id( 'sort_descending' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sort_descending') ); ?>" value="1" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'transfers') ?></label>
			<select class="services_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
				<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('Small (usually sidebar)', 'transfers') ?></option>
				<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Card (usually in grid view)', 'transfers') ?></option>
			</select>
		</p>
		
		<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
			<label for="<?php echo esc_attr ( $this->get_field_id( 'services_per_row' ) ); ?>"><?php esc_html_e('How many services do you want to display per row?', 'transfers') ?></label>
			<select id="<?php echo esc_attr ( $this->get_field_id( 'services_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'services_per_row' ) ); ?>">
				<?php for ($i=1;$i<6;$i++) { ?>
				<option <?php echo ($i == $instance['services_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>		
	<?php
	}
}