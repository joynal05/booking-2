<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Hero Unit Widget

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_hero_unit_widgets' );

// Register widget.
function transfers_hero_unit_widgets() {

	register_widget( 'transfers_Hero_Unit_Widget' );
	add_action('transfers_enqueue_admin_scripts_styles', 'transfers_hero_unit_enqueue');
}

function transfers_hero_unit_enqueue() {

	$screen = get_current_screen();
	if ($screen && isset($screen->id) && $screen->id == 'widgets' )	 {
		wp_enqueue_media();
		wp_enqueue_script('transfers-hero-unit-widget', transfers_get_file_uri ('/includes/plugins/widgets/widget-hero-unit.js'), array('jquery', 'thickbox'), '1.0', true );
	}
}

// Widget class.
class transfers_hero_unit_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_hero_unit_widget', 'description' => esc_html__('Transfers: Hero Unit Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_hero_unit_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_hero_unit_widget', esc_html__('Transfers: Hero Unit Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$hero_unit_title = isset($instance['hero_unit_title']) ? $instance['hero_unit_title'] : __("Need a ride?", 'transfers');
		$hero_unit_sub_title = isset($instance['hero_unit_sub_title']) ? $instance['hero_unit_sub_title'] : __("You've come to the right place.", 'transfers');	
		$button_1_url = isset($instance['button_1_url']) ? $instance['button_1_url'] : '#services';
		$button_1_text = isset($instance['button_1_text']) ? $instance['button_1_text'] : __('Our services', 'transfers');
		$button_2_url = isset($instance['button_2_url']) ? $instance['button_2_url'] : '#booking';
		$button_2_text = isset($instance['button_2_text']) ? $instance['button_2_text'] : __('Make a booking', 'transfers');
		$hero_unit_image_uri = isset($instance['hero_unit_image_uri']) ? $instance['hero_unit_image_uri'] : '';

		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);

		/* Display Widget */
		$background_style = '';
		if (!empty($hero_unit_image_uri)) {
			$background_style = 'style="background-image:url(' . esc_url($hero_unit_image_uri) . ');"';
		}
		?>
		<!-- Intro -->
		<div class="intro" <?php echo wp_kses($background_style, array('style' => array())); ?>>
			<div class="wrap">
				<div class="textwidget">
					<h1 class="wow fadeInDown"><?php echo esc_html($hero_unit_title); ?></h1>
					<h2 class="wow fadeInUp"><?php echo esc_html($hero_unit_sub_title); ?></h2>
					<div class="actions">
						<?php if (!(empty($button_1_url) && empty($button_1_text))) { ?>
						<a href="<?php echo esc_url($button_1_url); ?>" title="<?php echo esc_attr($button_1_text); ?>" class="btn large white wow fadeInLeft anchor"><?php echo esc_html($button_1_text); ?></a>
						<?php } ?>
						<?php if (!(empty($button_2_url) && empty($button_2_text))) { ?>
						<a href="<?php echo esc_url($button_2_url); ?>" title="<?php echo esc_attr($button_2_text); ?>" class="btn large color wow fadeInRight anchor"><?php echo esc_html($button_2_text); ?></a>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
		<!-- //Intro -->
		<?php
		/* After widget (defined by themes). */
		echo wp_kses($after_widget, $allowedtags);
	}


/*-----------------------------------------------------------------------------------*/
/*	Update Widget
/*-----------------------------------------------------------------------------------*/
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'br' => array()
		);
		
		/* Strip tags to remove HTML (important for text inputs). */
		$instance['hero_unit_title'] = wp_kses( $new_instance['hero_unit_title'], $allowed_tags );
		$instance['hero_unit_sub_title'] = wp_kses( $new_instance['hero_unit_sub_title'], $allowed_tags );
		$instance['button_1_url'] = strip_tags( $new_instance['button_1_url'] );
		$instance['button_1_text'] = strip_tags( $new_instance['button_1_text'] );
		$instance['button_2_url'] = strip_tags( $new_instance['button_2_url'] );
		$instance['button_2_text'] = strip_tags( $new_instance['button_2_text'] );
		$instance['hero_unit_image_uri'] = strip_tags( $new_instance['hero_unit_image_uri'] );
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {

		/* Set up some default widget settings. */
		
		$defaults = array(
			'hero_unit_title' => esc_html__("Need a ride?", 'transfers'),
			'hero_unit_sub_title' => esc_html__("You've come to the right place.", 'transfers'),
			'button_1_url' => '#services',
			'button_1_text' => esc_html__('Our services', 'transfers'),
			'button_2_url' => '#booking',
			'button_2_text' => esc_html__('Make a booking', 'transfers'),
			'hero_unit_image_uri' => '',
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hero_unit_title' ) ); ?>"><?php esc_html_e('Hero unit title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hero_unit_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hero_unit_title' ) ); ?>" value="<?php echo esc_attr( $instance['hero_unit_title'] ); ?>" />
		</p>
		
        <p>
            <label for="<?php echo esc_attr($this->get_field_name( 'hero_unit_image_uri' )); ?>"><?php esc_html_e('Hero unit image:', 'transfers') ?></label>
            <input name="<?php echo esc_attr($this->get_field_name( 'hero_unit_image_uri' )); ?>" id="<?php echo esc_attr($this->get_field_id( 'hero_unit_image_uri' )); ?>" class="widefat" type="text" size="36"  value="<?php echo esc_url( $instance['hero_unit_image_uri'] ); ?>" />
            <input class="upload_image_button button button-primary" type="button" value="<?php esc_html_e('Select image', 'transfers') ?>" />
        </p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'hero_unit_sub_title' ) ); ?>"><?php esc_html_e('Hero unit sub title:', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'hero_unit_sub_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hero_unit_sub_title' ) ); ?>" value="<?php echo esc_attr( $instance['hero_unit_sub_title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_1_url' ) ); ?>"><?php esc_html_e('Button 1 url', 'transfers') ?></label>
			<input type="text" placeholder="http://" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_1_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_1_url' ) ); ?>" value="<?php echo esc_attr( $instance['button_1_url'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_1_text' ) ); ?>"><?php esc_html_e('Button 1 text', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_1_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_1_text' ) ); ?>" value="<?php echo esc_attr( $instance['button_1_text'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_2_url' ) ); ?>"><?php esc_html_e('Button 2 url', 'transfers') ?></label>
			<input type="text" placeholder="http://" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_2_url' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_2_url' ) ); ?>" value="<?php echo esc_attr( $instance['button_2_url'] ); ?>" />
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'button_2_text' ) ); ?>"><?php esc_html_e('Button 2 text', 'transfers') ?></label>
			<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'button_2_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'button_2_text' ) ); ?>" value="<?php echo esc_attr( $instance['button_2_text'] ); ?>" />
		</p>
		
	<?php
	}
}