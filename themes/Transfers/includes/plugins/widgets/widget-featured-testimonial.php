<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Featured Testimonial Widget

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_featured_testimonial_widgets' );

// Register widget.
function transfers_featured_testimonial_widgets() {
	register_widget( 'transfers_Featured_Testimonial_Widget' );
}

// Widget class.
class transfers_featured_testimonial_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_featured_testimonial_widget', 'description' => esc_html__('Transfers: Featured Testimonial Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_featured_testimonial_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_featured_testimonial_widget', esc_html__('Transfers: Featured Testimonial Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$featured_testimonial_title = isset($instance['featured_testimonial_title']) ? $instance['featured_testimonial_title'] : __('Wow, this theme is outstanding!', 'transfers');
		$featured_testimonial_text = isset($instance['featured_testimonial_text']) ? $instance['featured_testimonial_text'] : __('Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.', 'transfers');
		$featured_testimonial_author = isset($instance['featured_testimonial_author']) ? $instance['featured_testimonial_author'] : __('-John Doe, ThemeForest', 'transfers');
		$reverse_color_scheme = isset($instance['reverse_color_scheme']) ? (bool)$instance['reverse_color_scheme'] : false;	
		
		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);
		
		$outer_class = 'white';
		if ((isset($widget_reverse_color_scheme) && $widget_reverse_color_scheme) || $reverse_color_scheme) {
			$outer_class = 'black';
		}

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
		<!-- Testimonials -->
		<div class="testimonials center <?php echo esc_attr($outer_class); ?>">
			<div class="wrap">
				<h6 class="wow fadeInDown"><?php echo esc_html($featured_testimonial_title); ?></h6>
				<p class="wow fadeInUp">
					<?php
					$allowed_tags = transfers_get_allowed_content_tags_array();
					echo wp_kses($featured_testimonial_text, $allowed_tags); 
					?>
				</p>
				<p class="meta wow fadeInUp"><?php echo esc_html($featured_testimonial_author); ?></p>
			</div>
		</div>
		<!-- //Testimonials -->
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
		$instance['featured_testimonial_title'] = strip_tags( $new_instance['featured_testimonial_title'] );
		$instance['featured_testimonial_text'] = $new_instance['featured_testimonial_text'];
		$instance['featured_testimonial_author'] = strip_tags( $new_instance['featured_testimonial_author'] );
		$instance['reverse_color_scheme'] = strip_tags( $new_instance['reverse_color_scheme'] );
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		
		$defaults = array(
			'featured_testimonial_title' => esc_html__('Wow, this theme is outstanding!', 'transfers'),
			'featured_testimonial_text' => esc_html__('Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat.', 'transfers'),
			'featured_testimonial_author' => esc_html__('-John Doe, ThemeForest', 'transfers'),
			'reverse_color_scheme' => false
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_title' ) ); ?>"><?php esc_html_e('Testimonial title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured_testimonial_title' ) ); ?>" value="<?php echo esc_attr( $instance['featured_testimonial_title'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_text' ) ); ?>"><?php esc_html_e('Testimonial text', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured_testimonial_text' ) ); ?>" value="<?php echo esc_attr( $instance['featured_testimonial_text'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_author' ) ); ?>"><?php esc_html_e('Testimonial author', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'featured_testimonial_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'featured_testimonial_author' ) ); ?>" value="<?php echo esc_attr( $instance['featured_testimonial_author'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>"><?php esc_html_e('Reverse color scheme?', 'transfers') ?></label>
			<input type="checkbox" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'reverse_color_scheme' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'reverse_color_scheme' ) ); ?>" value="1" <?php echo isset($instance['reverse_color_scheme']) && $instance['reverse_color_scheme'] ? 'checked="checked"' : ''; ?> />
		</p>
		
	<?php
	}
}