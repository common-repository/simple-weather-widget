<?php

/**
 * Plugin Name: widgets
 * Description: 
 * Version: 1.0
 */
 
 
add_action( 'widgets_init', 'widgets' );

function widgets() {
	register_widget( 'WP_widget' );
}

 
class WP_widget extends WP_Widget {
 
	function wordpress_widget() {
 		// settings array
		$widget_options = array(   
		 'classname' => 'si_widget', 
		 'description' => __('widget class.') );

	 
		$control_options = array( 
		'width' => 300, 
		'height' => 350, 
		'id_base' => 'wordpress_widget' );
	 
		$this->WP_Widget( 'WP_widget', 'WP widget', $widget_options, $control_options ); 		 
	}

 
	function widget( $args, $instance ) {
	   
	  if( !is_array( $args ) ) return trigger_error("argument exception", E_USER_WARNING );
	    //theme specifc tags
 
		extract( $args );  
      
		 
		$title = apply_filters('widget_title', $instance['title'] );
		
		$woeid =($instance['woeid'] != "")? $instance['woeid'] : 12799205;
 
		 
		echo $before_widget;

		 
		if ( $title )
		
			echo $before_title . $title . $after_title;
			
        echo "\n";
        
        $url = 'http://weather.yahooapis.com/forecastrss?w='.$woeid; 
           
		$xml = (string)file_get_contents($url); 
		
		if ($xml && !empty($xml))
		{
		
			$xml = simplexml_load_string($xml); 
			
			if ($xml && is_object($xml)){
			
				$node = $xml->channel->item; 
				
				if (is_object($node)){
				
					$children = $node->children('http://xml.weather.yahoo.com/ns/rss/1.0'); 
					
					$condition = $children->condition; 
					
					$attributes = $condition->attributes();
					
					$city = $xml->channel->children('yweather', TRUE)->location->attributes()->city;	
								 					
			    	$description = $node->description;    
			    	
			        $imgpattern = '/src="(.*?)"/i';
			        
			        preg_match($imgpattern, $description, $matches);
			
			         
			        $weather = $matches[1];
			      
 		   
			        $markup='<div style="border:1px dashed;display:block;background-color:#E6F1F6;">'."\n". 
					         $attributes['date']."\n".
					         $city."\n".
					        '<img src = "' . $weather . '" />'."\n".
					        'temperature'."\t".
					         $attributes['temp'] ."F"."\n".			
					         '</div>';
					echo nl2br($markup);
				}
			}
		}				
 		 
		echo $after_widget;
	}
 
	

 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;	 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['woeid'] = strip_tags( $new_instance['woeid'] );
		return $instance;
	}

 
	function form( $instance ) {
		 
		$defaults = array( 
		'title' => __('si_widget', 'si_widget'), 
		'woeid' => __('2490383', '2490383')
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		 
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','title'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

	 
		
		<p>
			<label for="<?php echo $this->get_field_id( 'woeid' ); ?>"><?php _e('woeid:', 'woeid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'woeid' ); ?>" name="<?php echo $this->get_field_name( 'woeid' ); ?>" value="<?php echo $instance['woeid']; ?>" style="width:100%;" />
		</p>

 

	<?php
	}
	
}
?>