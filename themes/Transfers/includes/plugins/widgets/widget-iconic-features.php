<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Transfers Iconic Features Widget

-----------------------------------------------------------------------------------*/


// Add function to widgets_init that'll load our widget.
add_action( 'widgets_init', 'transfers_iconic_features_widgets' );

// Register widget.
function transfers_iconic_features_widgets() {
	register_widget( 'transfers_Iconic_Features_Widget' );
}

// Widget class.
class transfers_iconic_features_widget extends WP_Widget {


/*-----------------------------------------------------------------------------------*/
/*	Widget Setup
/*-----------------------------------------------------------------------------------*/
	
	function __construct() {
	
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'transfers_iconic_features_widget', 'description' => esc_html__('Transfers: Iconic Features Widget', 'transfers') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 550, 'id_base' => 'transfers_iconic_features_widget' );

		/* Create the widget. */
		parent::__construct( 'transfers_iconic_features_widget', esc_html__('Transfers: Iconic Features Widget', 'transfers'), $widget_ops, $control_ops );
	}


/*-----------------------------------------------------------------------------------*/
/*	Display Widget
/*-----------------------------------------------------------------------------------*/
	
	function widget( $args, $instance ) {

		extract( $args );
	
		global $transfers_theme_globals;
		$iconic_features_icon_classes = $transfers_theme_globals->get_iconic_features_icon_classes();
		
		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);
		
		/* Our variables from the widget settings. */
		$number_of_features = isset($instance['number_of_features']) ? (int)$instance['number_of_features'] : 9;		
		$features_per_row = isset($instance['features_per_row']) ? (int)$instance['features_per_row'] : 3;		

		if (isset($card_layout_classes[$features_per_row - 1]))
			$feature_class = $card_layout_classes[$features_per_row - 1];
		else
			$feature_class = 'one-third';
		
		global $display_mode;
		
		$display_mode = isset($instance['display_mode']) ? $instance['display_mode'] : 'card';
		
		$widget_features = isset($instance['features']) ? $instance['features'] : $widget_default_features;
		
		$allowedtags = transfers_get_allowed_widgets_tags_array();
		
		/* Before widget (defined by themes). */
		echo wp_kses($before_widget, $allowedtags);

		/* Display Widget */
		?>
		
		<!-- Services iconic -->
		<?php if ($display_mode == 'card') { ?>
		<div class="services iconic white">
		<?php } else { ?>
		<div class="services iconic white small-list">
		<?php } ?>
			<div class="wrap">
				<div class="row">
<?php
					$i = 1;
					foreach ($widget_features as $widget_feature) {
						$delay = ($i % 3) * 1.5;
						?>
						<!-- Item -->
						<div class="<?php echo esc_attr($feature_class); ?>">
							<span class="circle"><span class="icon <?php echo esc_attr(trim($widget_feature['class'])); ?>"></span></span>
							<h3><?php echo esc_html($widget_feature['title']); ?></h3>
							<p>
								<?php
								$allowed_tags = transfers_get_allowed_content_tags_array();
								echo wp_kses($widget_feature['text'], $allowed_tags); 
								?>
							</p>
						</div>
						<!-- //Item -->
						<?php
						$i++;
						$i = $i == 3 ? 0 : $i;
					}
?>				
				
				</div>
			</div>
		</div>
		<!-- //Services iconic -->

		<?php
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
		$instance['number_of_features'] = (int)$new_instance['number_of_features'];
		$instance['features_per_row'] = (int)$new_instance['features_per_row'];
		$instance['display_mode'] = strip_tags( $new_instance['display_mode']);
        $instance['features'] = $new_instance['features'];		
		
		return $instance;
	}
	

/*-----------------------------------------------------------------------------------*/
/*	Widget Settings
/*-----------------------------------------------------------------------------------*/
	 
	function form( $instance ) {
	
		global $transfers_theme_globals;
		$iconic_features_icon_classes = $transfers_theme_globals->get_iconic_features_icon_classes();
		$iconic_features_icon_classes = explode("\n", $iconic_features_icon_classes);

		/* Set up some default widget settings. */
		
		$defaults = array(
			'number_of_features' => '9',
			'display_mode' => 'card',
			'features_per_row' => '3',
			'features' => array()
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); 

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_features' ) ); ?>"><?php esc_html_e('How many features do you want to display?', 'transfers') ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'number_of_features' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_features' ) ); ?>">
				<?php for ($i=1;$i<13;$i++) { ?>
				<option <?php echo ($i == $instance['number_of_features'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>
		
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>"><?php esc_html_e('Display mode?', 'transfers') ?></label>
			<select class="posts_widget_display_mode" id="<?php echo esc_attr( $this->get_field_id( 'display_mode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'display_mode') ); ?>">
				<option <?php echo 'small' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="small"><?php esc_html_e('Small (usually sidebar)', 'transfers') ?></option>
				<option <?php echo 'card' == $instance['display_mode'] ? 'selected="selected"' : ''; ?> value="card"><?php esc_html_e('Card (usually in grid view)', 'transfers') ?></option>
			</select>
		</p>
		
		<p class="cards" <?php echo ( $instance['display_mode'] != 'card' ? 'style="display:none"' : '' ); ?>>
			<label for="<?php echo esc_attr ( $this->get_field_id( 'features_per_row' ) ); ?>"><?php esc_html_e('How many features do you want to display per row?', 'transfers') ?></label>
			<select id="<?php echo esc_attr ( $this->get_field_id( 'features_per_row' ) ); ?>" name="<?php echo esc_attr ( $this->get_field_name( 'features_per_row' ) ); ?>">
				<?php for ($i=1;$i<6;$i++) { ?>
				<option <?php echo ($i == $instance['features_per_row'] ? 'selected="selected"' : ''); ?> value="<?php echo esc_attr ( $i ); ?>"><?php echo esc_html($i); ?></option>
				<?php } ?>
			</select>
		</p>
		
		<?php 
		$features = $instance['features'];
		
		for ($i=0;$i<$instance['number_of_features'];$i++) { 
			$feature = isset($features[$i]) ? $features[$i] : null;
			$class = isset($feature) && isset($feature['class']) ? trim($feature['class']) : '';
			$title = isset($feature) && isset($feature['title']) ? trim($feature['title']) : '';
			$text = isset($feature) && isset($feature['text']) ? trim($feature['text']) : ''; ?>
			<p class="feature">
				<h3><?php echo sprintf(__("Feature %d", 'transfers'), $i+1); ?></h3>
				<p>
					<div class="select-wrap">
						<label><?php esc_html_e("Feature icon class", 'transfers'); ?></label>
						<select id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][class]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][class]') ?>">
							<?php foreach ($iconic_features_icon_classes as $icon_class) { ?>
							<option <?php echo trim($icon_class) == $class ? 'selected' : ''; ?> value="<?php echo esc_attr ( $icon_class ); ?>"><?php echo esc_html($icon_class); ?></option>
							<?php } ?>
						</select>
					</div>
				</p>
				<p>
					<label><?php esc_html_e("Feature title", 'transfers'); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][title]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][title]') ?>" value="<?php echo esc_attr($title); ?>" />
				</p>
				<p>
					<label><?php esc_html_e("Feature text", 'transfers'); ?></label>
					<input type="text" class="widefat" id="<?php echo esc_attr($this->get_field_id('features') . '[' . $i . '][text]') ?>" name="<?php echo esc_attr($this->get_field_name('features') . '[' . $i . '][text]') ?>" value="<?php echo esc_attr($text); ?>" />
				</p>			
			</p>		
		<?php 
		} 
	}
}