<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Company Contact

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_contact_widgets' );

// Register widget.
function transfers_contact_widgets() {
	register_widget( 'transfers_Contact_Widget' );
}

// Widget class.
class transfers_contact_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_contact_widget', 'description' => esc_html__('Transfers: Contact Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_contact_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_contact_widget', esc_html__('Transfers: Contact Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$sub_title = $instance['sub_title'];
		$company_phone = $instance['company_phone'];
		$company_email = $instance['company_email'];	

		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);

		/* Display Widget */
		/* Display the widget title if one was input (before and after defined by themes). */
		?>
			<article class="transfers_contact_widget one-fourth">
				<?php
				if ( $title )
					echo wp_kses(($before_title . $title . $after_title), $allowedtags);
				if ( $sub_title )
					echo '<p>' . esc_html($sub_title) . '</p>';
					
				if (!empty($company_phone)) { ?>
				<p class="contact-data">
					<span class="icon icon-themeenergy_call"></span> <?php echo esc_html($company_phone); ?>
				</p>	
				<?php 
				} 
				if (!empty($company_email)) { ?>
				<p class="contact-data">
					<span class="icon icon-themeenergy_mail-2"></span> <a href="mailto:<?php echo esc_attr($company_email); ?>"><?php echo esc_html($company_email); ?></a>
				</p>
				<?php } ?>
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

		/* Strip tags to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['sub_title'] = strip_tags( $new_instance['sub_title'] );
		$instance['company_phone'] = strip_tags( $new_instance['company_phone'] );
		$instance['company_email'] = strip_tags( $new_instance['company_email'] );

		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
		'title' => esc_html__('Need help?', 'transfers'),
		'sub_title' => esc_html__('Contact us via phone or email:', 'transfers'),
		'company_phone' => '1-555-555-5555',
		'company_email' => 'info@transfers.com'
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e('Title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'sub_title' ) ); ?>"><?php esc_html_e('Sub title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'sub_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'sub_title' ) ); ?>" value="<?php echo esc_attr( $instance['sub_title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_phone' ) ); ?>"><?php esc_html_e('Company phone:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_phone' ) ); ?>" value="<?php echo esc_attr( $instance['company_phone'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'company_email' ) ); ?>"><?php esc_html_e('Company email:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'company_email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'company_email' ) ); ?>" value="<?php echo esc_attr( $instance['company_email'] ); ?>" />
		</p>		
		
	<?php
	}
}