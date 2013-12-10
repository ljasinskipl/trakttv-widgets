<?php

class ljpl_Trakt_Actions_Widget extends WP_Widget {
	
	function ljpl_Trakt_Actions_Widget() {
		// settings	
		$widget_ops = array( 
			'classname' => 'ljpl-trakttv', 
			'description' => 'This widget shows user\'s last actions in trakt.tv.' );

		/* Widget control settings. */
		$control_ops = array( 'id_base' => 'ljpl-trakttv-actions-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'ljpl-trakttv-actions-widget', 'TraktTV Actions Widget', $widget_ops, $control_ops );
	}
	
	/**
	 * print_show_poster
	 * Displays TV show's poster image
	 * @param $show array Array from TraktTV API
	 * @since 1.4
	 */
	private function print_show_poster($show) {
		echo '<img class="screen" src="'. $show['images']['poster'] .'" />';
	}
	
	/**
	 * print_movie_poster
	 * Displays movie's poster image
	 * @param $movie array Array from TraktTV API
	 * @since 1.4
	 */
	private function print_movie_poster($movie) {
		echo '<img class="screen" src="'. $movie['images']['poster'] .'" />';
	}
	
	/**
	 * print_episode_screen
	 * Displays TV episode's screenshot image
	 * @param $episode array from TraktTV API
	 * @since 1.4
	 */
	private function print_episode_screen( $episode ) {
		echo '<img class="screen" src="'. $episode['images']['screen'] .'" />';
	}
	
	/**
	 * print_movie_title
	 * Displays formatted movie title
	 * @param $episode array from TraktTV API
	 * @since 1.4
	 */
	private function print_movie_title( $movie ) {
		echo '<p class="title">
			Movie: 
			<a href="'. $movie['url'] . '" target="_blank">
				' . $movie['title'] . '
			</a>
		</p>';
	}
	
	/**
	 * print_movie_header
	 * Displays formated movie header
	 * @param $movie array from TraktTV API
	 * @param $i int counter
	 * @since 1.4
	 */
	private function print_movie_header( $movie, $i ) {
		$this->print_movie_title( $movie );
		if( $i == 0 ) {
			$this->print_movie_poster( $movie );
		}			
	}
	
	/**
	 * print_show_title
	 * Displays formatted tv show title
	 * @param $show array from TraktTV API
	 * @since 1.4
	 */
	private function print_show_title( $show ) {
		echo 'TV Show: ' . 
			'<a href="'. $show['url'] .'"target="_blank">'
				. $show['title'] . 
			'</a>';	
	}
	
	/**
	 * print_show_header
	 * Displays formatted tv show header (used for single episode actions)
	 * @param $show array from TraktTV API
	 * @param $episode array from TraktTV API
	 * @param $i int counter
	 * @since 1.4
	 */
	private function print_show_header( $show, $episode, $i ) {
		echo '<p class="title">';
		$this->print_show_title( $show );
		echo '<br />';
		$this->print_episode_title( $episode );		
		echo '</p>';
		if( $i == 0 ) {
			$this->print_episode_screen( $episode );
		}		
	}
	
	/**
	 * print_episode_title
	 * Displays formatted tv show's episode title
	 * @param $episode array from TraktTV API
	 * @since 1.4
	 */
	private function print_episode_title( $episode ) {
		echo 
			'S' . $this->zerofill( $episode['season'] ) . 
			'E' . $this->zerofill( $episode['episode'] ) . 
			' <a href="' . $episode['url'] . '"target="_blank">' . 
				$episode['title'] .
			'</a>';	
	}
	
	/**
	 * timestamp2human
	 * Displays time of action converted from timestamp
	 * @param $intro string intro text
	 * @param $timestamp int UNIX timestamp
	 * @since 1.4
	 */
	private function timestamp2human( $intro, $timestamp ) {
		echo '<p>' . $intro . ' on ' . date_i18n( get_option('date_format'), $timestamp ) . '</p>';
	}
	
	/**
	 * zerofill
	 * Fill number with leading zeros if it has to few digits
	 * @param $number int number to fill
	 * @param $positions int wanted number of digits
	 * @return string 
	 * @since 1.0
	 */
	private function zerofill($number, $positions=2) {
		for($i=1;$i<$positions;$i++)	{
			if($number < pow(10,$i))
				$number = '0' . $number;
		}
		return $number;
	}
	
	/**
	 * Echoes item for action 'rating'
	 * @param $data array data row from json
	 * @param $i int position on the list
	 * @since 1.4
	 */
	private function print_rating( $data, $i ) {
		echo '<li>';
		if( $data['type'] == 'show' ) {
			echo '<p class="title">';
			$this->print_show_title( $data['show'] );
			echo '</p>';
			if( $i == 0 ) {
				$this->print_show_poster($data['show']);
			}						
		} elseif ($data['type'] == 'movie' ) {			
			$this->print_movie_header( $data['movie'], $i );
			
		} elseif ($data['type'] == 'episode' ) {
			$this->print_show_header( $data['show'], $data['episode'], $i );	
		}
		else {
			return;
		}
		
		// -- ratings
		if( $data['use_rating_advanced'] ) {
			$intro = '<p>Rated: ' . $data['rating_advanced'] . '/10 ('. $data['rating'] .')';
		} else {
			$intro = '<p>Rated: '. $data['rating'] .'';			
		}
		$this->timestamp2human( $intro, $data['timestamp'] );
		echo '</li>';
	}

	/**
	 * Echoes item for action 'collection'
	 * @param $data array data row from json
	 * @param $i int position on the list
	 * @since 1.4
	 */
	private function print_collection( $data, $i ) {
		echo '<li>';
		if( $data['type'] == 'episode') {
			if( count ($data['episodes'] ) == 1 ) {
				$this->print_show_header($data['show'], $data['episodes'][0], $i);					
			} else {
				echo '<p class="title">';
				$this->print_show_title( $data['show'] );
				echo '</p>';
				if( $i == 0 ) {
					$this->print_show_poster($data['show']);
				}
				echo '<ul>';
				foreach ($data['episodes'] as $episode ) {
					echo '<li>';
					$this->print_episode_title( $episode );
					echo '</li>';
				}	
				echo '</ul>';			
			}
			
		} elseif ( $data['type'] == 'movie' ) {
			$this->print_movie_title( $data['movie'] );
			if( $i == 0 ) {
				$this->print_movie_poster( $data['movie'] );
			}			
		} else {
			return;
		}
		
		$this->timestamp2human( 'Added to collection', $data['timestamp'] );
		echo '</li>';
	}

	/**
	 * Echoes item for actions 'scrobble', 'checkin', 'seen', 'watching'
	 * @param $data array data row from json
	 * @param $i int position on the list
	 * @since 1.4
	 */	
	private function print_action( $data, $i ) {
		echo '<li>';
		if( $data['type'] == 'episode' ) {
			$this->print_show_header($data['show'], $data['episode'], $i);
		} elseif ($data['type'] == 'movie' ) {
			$this->print_movie_header( $data['movie'], $i );
		} else {
			return;
		}
		switch ( $data['action'] ) {
			case 'scrobble' :      $intro = 'Seen'; break;
			case 'checkin' :       $intro = 'Seen'; break;
			case 'seen' :          $intro = 'Marked as seen'; break;
			case 'watching' :      $intro = 'Watching'; break;
		}
		$this->timestamp2human($intro, $data['timestamp']);
		echo '</li>';
	}
	
	/**
	 * Displays widget
	 * @param $args array widget arguments
	 * @param $instance array widget instance settings
	 * @since 1.0
	 */ 
	function widget( $args, $instance ) {
		
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$username = $instance['username'];
		$apikey = $instance['apikey'];
		$transientname = 'cache-ljpl-traktwidget' . $this->number;
		if($instance['actionsAll'])
			$actions = '/all';
		else
			$actions = '/' . $instance['actionsList'];
			
		if($instance['typesAll'])
			$types = '/all';
		else
			$types = '/' . $instance['typesList'];
			
		$maxActions = $instance['maxActions'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		/* Podłączamy się do API TraktTV */
		if( $username && $apikey ) {
						
			// #################################################################
			// ##### Get user's actions
			
			// TODO: refactor $out
			$out = get_transient( $transientname );
			
			if( $out === false ) {
			
				$url = 'http://api.trakt.tv/activity/user/json/' . $apikey . '/' . $username . $types . $actions;
				$result = wp_remote_get( $url, array( 'timeout' => 20 ) );
				
				if( is_wp_error( $result) ) {
					return; 
					
				}
				
				if( $result['response']['code'] != 200 )
					return;	// TODO: proper error handling
				
				$out = json_decode($result['body'], TRUE);
				
				// -- cache results for 1h
				set_transient( $transientname, $out, 3600 ); // TODO: time settable in settings panel
			}
			print "<ul>";
			
			// simple check to avoid errors
			if(count($out['activity']) < $maxActions)
				$maxActions = count($out['activity']);
				
				
			for($i=0;$i<$maxActions;$i++) {
				if( $out['activity'][$i]['action'] == 'rating' ) {
					$this->print_rating($out['activity'][$i], $i);
				} elseif ($out['activity'][$i]['action'] == 'collection' ) {
					$this->print_collection($out['activity'][$i], $i);
				} else {
					$this->print_action( $out['activity'][$i], $i );
				}
			}
			print "</ul>";			
		}

		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	/**
	 * update
	 * Updates instance settings
	 * @param $new_instance array new settings
	 * @param $old_instance array old_settings
	 * @since 1.0
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['username'] = strip_tags( $new_instance['username'] );
		$instance['apikey'] = $new_instance['apikey'];
		$instance['lastupdate'] = $new_instance['lastupdate'];
		$instance['maxActions'] = intval($new_instance['maxActions']);
		if(!$instance['maxActions'])
			$instance['maxActions'] = 10;
		
		$instance['typesAll'] = ( isset( $new_instance['typesAll'] ) ? 1 : 0 );  
		$instance['typesList'] = $new_instance['typesList'];
		
		$instance['actionsAll'] = ( isset( $new_instance['actionsAll'] ) ? 1 : 0 );  
		$instance['actionsList'] = $new_instance['actionsList'];
		
		return $instance;
	}
	
	/**
	  * form()
	  * Displays form with options for widget instance
	  * @param mixed $instance Widget instance
	  * @since 1.0 
	  */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = Array (
			'title' => 'TraktTV Wordpress Widget',
			'username' => '',
			'apikey' => '',
			'maxActions' => 10,
			'typesAll' => 1,
			'typesList' => 'episode,show,movie',
			'actionsAll' => 1,
			'actionsList' => 'watching,scrobble,checkin,seen,collection,rating,watchlist,shout'	
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<!-- TraktTV login -->
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e('Login w serwisie trakt.tv', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" value="<?php echo $instance['username']; ?>" style="width:100%;" />
		</p>

		<!-- TraktTV API key -->
		<p>
			<label for="<?php echo $this->get_field_id( 'apikey' ); ?>"><?php _e('TraktTV API key:', 'example'); ?></label> 
			<input id="<?php echo $this->get_field_id( 'apikey' ); ?>" name="<?php echo $this->get_field_name( 'apikey' ); ?>" value="<?php echo $instance['apikey']; ?>" style="width:100%;" />	
		</p>

		<!-- Max Actions shown -->
		<p>
			<label for="<?php echo $this->get_field_id( 'maxActions' ); ?>"><?php _e('Max number of actions shown:', 'example'); ?></label> 
			<input id="<?php echo $this->get_field_id( 'maxActions' ); ?>" name="<?php echo $this->get_field_name( 'maxActions' ); ?>" value="<?php echo $instance['maxActions']; ?>" style="width:100%;" />
		</p>
		
		<!-- Types to show in widget -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['typesAll'], true ); ?> id="<?php echo $this->get_field_id( 'typesAll' ); ?>" name="<?php echo $this->get_field_name( 'typesAll' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'typesAll' ); ?>"><?php _e('Show all types?'); ?></label>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id( 'typesList' ); ?>"><?php _e('Types to show: (default: episode,show,movie)', 'example'); ?></label> 
			<input id="<?php echo $this->get_field_id( 'typesList' ); ?>" name="<?php echo $this->get_field_name( 'typesList' ); ?>" value="<?php echo $instance['typesList']; ?>" style="width:100%;" />
		</p>
		
		<!-- Actions to show in widget -->
		<p>
			<input class="checkbox" type="checkbox" <?php checked( (bool) $instance['actionsAll'], true ); ?> id="<?php echo $this->get_field_id( 'actionsAll' ); ?>" name="<?php echo $this->get_field_name( 'actionsAll' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'actionsAll' ); ?>"><?php _e('Show all actions?'); ?></label>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id( 'actionsList' ); ?>"><?php _e('Actions to show: (default: watching,scrobble,checkin,seen,collection,rating,watchlist,shout)', 'example'); ?></label> 
			<input id="<?php echo $this->get_field_id( 'actionsList' ); ?>" name="<?php echo $this->get_field_name( 'actionsList' ); ?>" value="<?php echo $instance['actionsList']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}