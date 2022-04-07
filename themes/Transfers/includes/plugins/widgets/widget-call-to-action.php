<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Call-To-Action Widget

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_call_to_action_widgets' );

// Register widget.
function transfers_call_to_action_widgets() {
	register_widget( 'transfers_Call_To_Action_Widget' );
}

// Widget class.
class transfers_call_to_action_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_call_to_action_widget', 'description' => esc_html__('Transfers: Call-To-Action Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_call_to_action_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_call_to_action_widget', esc_html__('Transfers: Call-To-Action Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$call_to_action_text = isset($instance['call_to_action_text']) ? $instance['call_to_action_text'] : __('Like what you see? Are you ready to stand out? You know what to do!', 'transfers');
		$button_text = isset($instance['button_text']) ? $instance['button_text'] : __('Purchase theme', 'transfers');	
		$button_url = isset($instance['button_url']) ? $instance['button_url'] : '#';	
		$reverse_color_scheme = isset($instance['reverse_color_scheme']) ? (bool)$instance['reverse_color_scheme'] : false;	

		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);
		
		$outer_class = 'color';
		$button_class = 'black';
		if ((isset($widget_reverse_color_scheme) && $widget_reverse_color_scheme) || $reverse_color_scheme) {
			$outer_class = 'black';
			$button_class = 'color';
		}

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
		<!-- Call to action -->
		<div class="<?php echo esc_attr($outer_class); ?> cta">
			<div class="wrap">
				<p class="fadeInLeft">
					<?php
					$allowed_tags = transfers_get_allowed_content_tags_array();
					echo wp_kses($call_to_action_text, $allowed_tags); 
					?>
				</p>
				<a href="<?php echo esc_url($button_url); ?>" class="btn huge <?php echo esc_attr($button_class); ?> right fadeInRight"><?php echo esc_html($button_text); ?></a>
			</div>
		</div>
		<!-- //Call to action -->
		<?php
		/* After widget (defined by themes). */
		echo wp_kses($after_widget, $allowedtags);
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['call_to_action_text'] = strip_tags( $new_instance['call_to_action_text'] );
		$instance['button_text'] = strip_tags( $new_instance['button_text'] );
		$instance['button_url'] = strip_tags( $new_instance['button_url'] );
		$instance['reverse_color_scheme'] = strip_tags( $new_instance['reverse_color_scheme'] );

		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		
		$defaults = array(
			'call_to_action_text' => esc_html__('Like what you see? Are you ready to stand out? You know what to do!', 'transfers'),
			'button_text' => esc_html__('Purchase theme', 'transfers'),
			'button_url' => '#',
			'reverse_color_scheme' => false
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'call_to_action_text' ) ); ?>"><?php esc_html_e('Call to action text:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'call_to_action_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'call_to_action_text' ) ); ?>" value="<?php echo esc_attr( $instance['call_to_action_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>"><?php esc_html_e('Button text', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_text' ) ); ?>" value="<?php echo esc_attr( $instance['button_text'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>"><?php esc_html_e('Button url', 'transfers') ?></label>
			<input type="text" placeholder="http://" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_url' ) ); ?>" value="<?php echo esc_attr( $instance['button_url'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>"><?php esc_html_e('Reverse color scheme?', 'transfers') ?></label>
			<input type="checkbox" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'reverse_color_scheme' ) ); ?>" value="1" <?php echo isset($instance['reverse_color_scheme']) && $instance['reverse_color_scheme'] ? 'checked="checked"' : ''; ?> />
		</p>
		
	<?php
	}
}