<?php 
error_reporting ( 0 );

add_action( "widgets_init", array( 'AllegratoMyAuctionsAllegro', 'register' ) );

register_activation_hook( __FILE__, array('AllegratoMyAuctionsAllegro', 'activate'));
register_deactivation_hook( __FILE__, array('AllegratoMyAuctionsAllegro', 'deactivate'));

class AllegratoMyAuctionsAllegro {
	
	function activate()
  	{
	    $data = array( 
	    	'title' => __( 'Allegro auctions', 'allegrato' ),
	    	'limit_on_page' => '1',
	    	'auto' => '3000',
		    'speed' => '1000',
	        'circular' => 'on',	
	    );
	    
	    if ( !get_option( 'allegrato_my_auctions_allegro' ) )
	    {
			add_option( 'allegrato_my_auctions_allegro' , $data );
	    } 
	    else 
	    {
	      	update_option( 'allegrato_my_auctions_allegro' , $data );
	    }
  	}
  
	function deactivate()
	{
    	delete_option( 'allegrato_my_auctions_allegro' );
  	}

	function widget( $args )
  	{
		try
		{
			$auctions = allegratoFetchMyAuctionsAllegro();
			$sites = allegratoFetchSitesAllegro();
		}
		catch( Exception $e )
		{	
			echo '';
			//TODO: loging exceptions
		}

		$data = get_option( 'allegrato_my_auctions_allegro' );
		
		
    	echo $args['before_widget'];
       	echo $args['before_title'] . '<b>' . $data['title'] . '</b>' . $args['after_title'];
        	if( isset( $auctions['user-item-count'] ) && $auctions['user-item-count'] > 0 ):
    	    	if( $auctions['user-item-count'] > 1 ):
    		    	wp_enqueue_style( 'jcarousellite', '/wp-content/plugins/allegrato/js/news-ticker/style.css' );
    		        wp_enqueue_script( 'jcarousellite', '/wp-content/plugins/allegrato/js/news-ticker/jcarousellite_1.0.1c4.js', array('jquery'), '', true );
    		        wp_head();
    		
    				echo "<script type='text/javascript'>";
    		    	echo 'jQuery(function() {';
    				echo 'jQuery(".newsticker-jcarousellite").jCarouselLite({';
    				echo 'vertical: true,';
    				echo 'hoverPause: true,';
    				echo 'visible: ' . $data["limit_on_page"] . ',';
    				echo 'auto: ' . $data["auto"] . ',';
    			    echo 'btnNext: ".allegrato_my_allegro_auctions_next",';
    			    echo 'btnPrev: ".allegrato_my_allegro_auctions_previous",';
    			    if( isset( $data['circular'] ) &&  $data['circular'] == 'on' && ( $auctions['user-item-count'] > 1 ) ):
    			        echo "circular: true,";
    			    else:
    			    
    			        echo "circular: false,";
    			    endif;
    				echo "speed:{$data['speed']}";
    				echo '});';
    				echo '});';
    				echo '</script>';    	
    			endif;

    			$userLogin = get_option( 'allegrato_webapi_login_allegro' );
    			echo "<div>";
    			
    			if( $auctions['user-item-count'] == 0 ):
    				printf( __( "%s's has no auctions.", 'allegrato' ), $userLogin );
    			else:
    				printf( __( "%s's auctions (%d):", 'allegrato' ), $userLogin, $auctions['user-item-count'] );	
    			endif;
    			echo "</div>";
    			
    			if( $auctions['user-item-count'] > 0 ):
    
    				if( isset( $auctions['user-item-list'] ) ):
    				    if( $auctions['user-item-count'] > 1 ):
    				        echo "<div><span class='allegrato_my_allegro_auctions_previous'>" . __( 'previous', 'allegrato' ) . "</span>|";
    				        echo "<span class='allegrato_my_allegro_auctions_next'>" . __( 'next', 'allegrato' ) . "</span></div>";
    				    endif;
    				        
    				    echo '<div class="newsticker-jcarousellite">';
    					foreach ( $auctions['user-item-list'] as $userItem ):
    						$itemName = 'it-name';
    						$itemThumbUrl = 'it-thumb-url'; 
    						$itemPrice = 'it-price';
    						$itemTimeLeft = 'it-time-left';
    						$itemBidCount = 'it-bid-count';
    						$itemBuyNowPrice = 'it-buy-now-price';
    						$itemIsBoldTitle = 'it-is-bold-title';
    						$itemId = 'it-id';
    						$siteUrl = 'site-url';
    	                    $siteName = 'site-name';
    						
    						foreach( $sites['sites-info-list'] as $site ):
    						    if( $site->$siteName == get_option( 'allegrato_webapi_site_allegro' ) ):
    						        $itemUrl = "{$site->$siteUrl}/show_item.php?item={$userItem->$itemId}";
    						    endif;
    						endforeach;						
    						
    						
    							echo "<div class='allegrato_element_name'>";
    							if( $userItem->$itemIsBoldTitle == 1 ):
    								echo "<span><a href='{$itemUrl}'>{$userItem->$itemName}</a></span>";				
    							else:
    								echo "<span><b><a href='$itemUrl'>{$userItem->$itemName}</a></b></span>";
    							endif;
    							echo "</div>";
    						echo "<div><span>" . __( 'Bids', 'allegrato' ) . ": {$userItem->$itemBidCount}.</span></div>";
    						//TODO: uzależnić od zdalnego systemu
    						$currency = 'zł';
    						
    						if ( $userItem->$itemPrice ):
    							echo "<div><span>" . __( 'Price', 'allegrato' ) . ": {$userItem->$itemPrice} {$currency}</span></div>";
    						endif;
    		
    						if ( $userItem->$itemBuyNowPrice ):
    							echo "<div><span>" . __( 'Buy now price', 'allegrato' ) . ": {$userItem->$itemBuyNowPrice}{$currency}</span></div>";
    						endif;
    	
    	                    echo "<div class='thumbnail'>";					
    						if ( $userItem->$itemThumbUrl ):
    							echo "<img src='{$userItem->$itemThumbUrl}' />";
    						endif;
    						
    						if( $userItem->$itemThumbUrl != '' ):
    						    echo "</div><div class='info'>";
    						else:
    						    echo "</div><div>";
    						endif;
    	
    						if ( version_compare(PHP_VERSION, '5.3.0') >= 0 ): 
    	
    							$diff = date_diff(new DateTime("now"), new DateTime( date( "Y-m-d H:i:s",  time() + $userItem->$itemTimeLeft ) ) );
    		
    							$timeLeft = "<br />";
    							if( $diff->y != 0 ):
    								$timeLeft .=  " {$diff->y} " . __( 'years', 'allegrato' ) . "<br />";
    							endif;
    							if( $diff->m != 0 ):
    								$timeLeft .= " {$diff->m} " . __( 'months', 'allegrato' ) . "<br />";
    							endif;
    							if( $diff->d != 0 ):
    								$timeLeft .= " {$diff->d} " . __( 'days', 'allegrato' ) . "<br />";
    							endif;
    							if( $diff->h != 0 ):
    								$timeLeft .= " {$diff->h} " . __( 'houres', 'allegrato' ) . "<br />";
    							endif;
    							if( $diff->i != 0 ):
    								$timeLeft .= " {$diff->i} " . __( 'minutes', 'allegrato' ) . "<br />";
    							endif;
    							
    							if ( $userItem->$itemTimeLeft ):
    								echo "<div><span>" . __( 'Time left', 'allegrato' ) . ": {$timeLeft}</span></div>";
    							endif;
    						endif;
    						
    						echo "</div>";
    					endforeach;
    				endif;
    			endif;	
    			echo "</div>";
    		else:
                $userLogin = get_option( 'allegrato_webapi_login_allegro' );    		
                printf( __( "%s's has no auctions.", 'allegrato' ), $userLogin );    		
    		endif;    	
		
		echo $args['after_widget'];
  	}
  	
  	function register()
  	{
  	    $text = __( 'My allegro auctions', 'allegrato' );
    	wp_register_sidebar_widget( 'allegrato_my_auctions_allegro', $text, array( 'AllegratoMyAuctionsAllegro', 'widget' ) );
    	wp_register_widget_control( 'allegrato_my_auctions_allegro', $text, array( 'AllegratoMyAuctionsAllegro', 'control' ) );
  	}
  
	function control()
	{
 
	$limitChoices = array(
		'1' => __( 'one auction', 'allegrato' ),
    //TODO: Fix scrolling when there is only one auction and then uncomment.	
//		'3' => __( 'two auctions', 'allegrato' ),
//		'5' => __( 'five auctions', 'allegrato' ),
//		'10' => __( 'ten auctions', 'allegrato' ),
	);
		
	$speedChoices = array(
		'500' => __( '0.5 s.', 'allegrato' ),
		'1000' => __( '1 s.', 'allegrato' ),
		'2000' => __( '2 s.', 'allegrato' ),
		'3000' => __( '3 s.', 'allegrato' ),
	);

	$autoChoices = array(
		'null' => __( 'no autoscroll', 'allegrato' ),
		'1000' => __( '1 s.', 'allegrato' ),
		'3000' => __( '3 s.', 'allegrato' ),
		'5000' => __( '5 s.', 'allegrato' ),
		'10000' => __( '10 s.', 'allegrato' ),
		'30000' => __( '30 s.', 'allegrato' ),
		'60000' => __( '60 s.', 'allegrato' ),
    );		
	

	  $data = get_option( 'allegrato_my_auctions_allegro' );
	  	echo '<p><label>' . __( 'Title', 'allegrato' ) . ': <input name="allegrato_my_auctions_allegro_title" type="text" value="' . $data['title'] . '" /></label></p>';
	  	
	  	echo '<p><label>' . __( 'Limit', 'allegrato' ) .  
	  	': <select name="allegrato_my_auctions_allegro_limit_on_page" >';
	  	foreach( $limitChoices as $limitChoiceKey => $limitChoiceValue ):
	  		echo $data['limit_on_page'] == $limitChoiceKey ? 
	  			'<option value="' . $limitChoiceKey . '" selected="selected">' . $limitChoiceValue . '</option>' : 
	  			'<option value="' . $limitChoiceKey . '">' . $limitChoiceValue . '</option>'; 
		endforeach;
	  	echo '</select></label></p>';

	  	echo '<p><label>' . __( 'Speed', 'allegrato' ) .  
	  	': <select name="allegrato_my_auctions_allegro_speed" >';
	  	foreach( $speedChoices as $speedChoiceKey => $speedChoiceValue ):
	  		echo $data['speed'] == $speedChoiceKey ? 
	  			'<option value="' . $speedChoiceKey . '" selected="selected">' . $speedChoiceValue . '</option>' : 
	  			'<option value="' . $speedChoiceKey . '">' . $speedChoiceValue . '</option>'; 
		endforeach;
	  	echo '</select></label></p>';

	  	echo '<p><label>' . __( 'Auto scroll', 'allegrato' ) .  
	  	': <select name="allegrato_my_auctions_allegro_auto" >';
	  	foreach( $autoChoices as $autoChoiceKey => $autoChoiceValue ):
	  		echo $data['auto'] == $autoChoiceKey ? 
	  			'<option value="' . $autoChoiceKey . '" selected="selected">' . $autoChoiceValue . '</option>' : 
	  			'<option value="' . $autoChoiceKey . '">' . $autoChoiceValue . '</option>'; 
		endforeach;
	  	echo '</select></label></p>';
	  	
	  	if( isset( $data['circular'] ) && $data['circular'] == 'on' ):
	  		$checked = "checked='checked' ";
	  	else:
	  		$checked = "";
	  	endif;
	  	echo '<p><label><input ' . $checked . 'type="checkbox" name="allegrato_my_auctions_allegro_circular" />';
	  	echo  __( 'circular', 'allegrato' ) . ' </label></p>';
	  	
		if ( isset( $_POST['allegrato_my_auctions_allegro_title'] ) || isset( $_POST['allegrato_my_auctions_allegro_limit_on_page'] ) )
	   	{
	   		$data['title'] = attribute_escape( $_POST['allegrato_my_auctions_allegro_title'] );
	    	$data['limit_on_page'] = attribute_escape( $_POST['allegrato_my_auctions_allegro_limit_on_page'] );
	    	$data['auto'] = attribute_escape( $_POST['allegrato_my_auctions_allegro_auto'] );
	    	$data['speed'] = attribute_escape( $_POST['allegrato_my_auctions_allegro_speed'] );
	    	$data['circular'] = attribute_escape( $_POST['allegrato_my_auctions_allegro_circular'] );
	    	
	    	update_option( 'allegrato_my_auctions_allegro', $data );
  		}
	}  
}