<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers About Widget

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_about_widgets' );

// Register widget.
function transfers_about_widgets() {
	register_widget( 'transfers_About_Widget' );
}

// Widget class.
class transfers_about_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_about_widget', 'description' => esc_html__('Transfers: About Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_about_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_about_widget', esc_html__('Transfers: About Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$about_text = wpautop($instance['about_text']);

		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);

		/* Display Widget */
		?>
			<article class="about_widget clearfix one-half">
				<?php
				if ( $title )
					echo wp_kses(($before_title . $title . $after_title), $allowedtags);
			
				echo wp_kses($about_text, $allowedtags);
				?>
			</article>
		<?php

		/* After widget (defined by themes). */
		echo wp_kses($after_widget, $allowedtags);
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$text_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
		);
		
		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['about_text'] = wp_kses( $new_instance['about_text'], $text_tags);

		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => esc_html__('About Transfers Community', 'transfers'),
		'about_text' => esc_html__('Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci.', 'transfers'),
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'about_text' ) ); ?>"><?php esc_html_e('About text:', 'transfers') ?></label>
			<textarea rows="5" cols="20" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'about_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'about_text' ) ); ?>"><?php echo esc_attr( $instance['about_text'] ); ?></textarea>
		</p>
		
	<?php
	}
}