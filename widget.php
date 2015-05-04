<?php
/* 
Plugin Name: Recent Categories
Plugin URI: http://zac.gorak.us
Description: Recent Categories
Author: @twodayslate
Version: 1.2
Author URI: http://zac.gorak.us
*/

// Block direct requests
if ( !defined('ABSPATH') )
	die('-1');
	
	
add_action( 'widgets_init', function(){
     register_widget( 'Recent_Categories_Widget' );
});	

/**
 * Adds My_Widget widget.
 */
class Recent_Categories_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */


        function __construct() {



		parent::__construct(
			'Recent_Categories_Widget', // Base ID
			__('Recent Categories', 'text_domain'), // Name
			array('description' => __( "You site's most recent categories.", 'text_domain' ),) // Args
		);

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		// get the excerpt of the required story
		if ( isset( $instance[ 'max_count' ] ) ) {
			$max_count = $instance[ 'max_count' ];
		}
		else {
			$max_count = 5;
		}

		if ( isset( $instance[ 'title' ] ) && !empty($instance['title'] )) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = "Recent Categories";
		}
		if ( isset( $instance[ 'display_date' ] ) ) {
			$display_date = $instance[ 'display_date' ] ? true : false;
		}
		if ( isset( $instance[ 'display_icon' ] ) ) {
			$display_icon = $instance[ 'display_icon' ] ? true : false;
		}
		if ( isset( $instance[ 'display_count' ] ) ) {
			$display_count = $instance[ 'display_count' ] ? true : false;
		}
		
		//if ( array_key_exists('before_widget', $args) ) echo $args['before_widget'];

		if (array_key_exists('before_widget', $args)) {

                echo str_replace('widget_recent_categories_widget', 'widget_recent_categories_widget widget_recent_entries', $args['before_widget']);
        
        }
		
		$args = array(
	    'numberposts' => $max_count,
	    'offset' => 0,
	    'orderby' => 'post_date',
	    'order' => 'DESC',
	    'post_type' => 'post',
	    'post_status' => 'publish',
	    'suppress_filters' => true );

	    $recent_posts = get_posts( $args );

	    echo '<h2 class="widget-title">'.$title.'</h2>';
	    echo "<ul>";
	    $listed_categories = array();
	    foreach ($recent_posts as $apost) {
	    	$categories = get_the_category($apost->ID);
			foreach ($categories as $acategory) {
				if(!array_key_exists($acategory->cat_ID, $listed_categories)){
					$listed_categories[$acategory->cat_ID] = array("name" => $acategory->cat_name, "count" => 1, "date" => $apost->ID);
				} else {
					$listed_categories[$acategory->cat_ID]["count"] = $listed_categories[$acategory->cat_ID]["count"] + 1;
				}
			}
	    }
		
		foreach($listed_categories as $key => $value) {
			echo '<li class="cat-item cat-item-'.$key.'">';
			echo '<a href="'.get_category_link($key).'">';
			if($display_icon) {
				if (function_exists('the_icon')) {
					echo the_icon(array('size' => 'small',
					'class' => 'icon'), $term_type = 'category',$id = $key, $use_term_id = null);
				}
			}
			echo $value["name"].'</a>';
			if($display_count) {
				echo ' ('.$value["count"].')';
			}
			if($display_date) {
				echo '<span class="post-date">'.get_the_date(get_option('date_format'),$value["date"]).'</span>';
			}
			echo "</li>";
		}
		
	    echo "</ul>";
			
		if ( array_key_exists('after_widget', $args) ) {
			echo $args['after_widget'];
		} else {
			echo "</aside>";
		}
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		if ( isset( $instance[ 'max_count' ] ) ) {
			$max_count = $instance[ 'max_count' ];
		}
		else {
			$max_count = 5;
		}

		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}

		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
				<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title ?>" />
			</label> 
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'max_count' ); ?>"><?php _e( 'Max number of categories to show:' ); ?>
				<input id="<?php echo $this->get_field_id( 'max_count' ); ?>" name="<?php echo $this->get_field_name( 'max_count' ); ?>" type="number" value="<?php echo $max_count ?>" size="3" />
			</label> 
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'display_date' ); ?>" name="<?php echo $this->get_field_name( 'display_date' ); ?>" type="checkbox" <?php checked($instance['display_date'], 'on'); ?> />
			<label for="<?php echo $this->get_field_id( 'display_date' ); ?>"><?php _e( 'Display category date?' ); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'display_count' ); ?>" name="<?php echo $this->get_field_name( 'display_count' ); ?>" type="checkbox" <?php checked($instance['display_count'], 'on'); ?> />
			<label for="<?php echo $this->get_field_id( 'display_count' ); ?>"><?php _e( 'Show post counts' ); ?></label>
		</p>
		<?php if (function_exists('the_icon')) { ?>
		<p>
			<input id="<?php echo $this->get_field_id( 'display_icon' ); ?>" name="<?php echo $this->get_field_name( 'display_icon' ); ?>" type="checkbox" <?php checked($instance['display_icon'], 'on'); ?> />
			<label for="<?php echo $this->get_field_id( 'display_icon' ); ?>"><?php _e( 'Display category icon?' ); ?></label>
		</p>
		<?php } ?>

		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		$instance['max_count'] = ( ! empty( $new_instance['max_count'] ) ) ? strip_tags( $new_instance['max_count'] ) : 5;
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display_date'] = $new_instance['display_date'] ;
		$instance['display_icon'] = $new_instance['display_icon'] ;
		$instance['display_count'] = $new_instance['display_count'] ;

		return $instance;
	}

} // class My_Widget
