<?php
/**
 * @package allegrato
 * @version 0.0.1
 *
/*
Plugin Name: Allegrato
Plugin URI: http://blog.smentek.eu/allegrato-wordpress-plugin
Description: Takes information from auction and publication systems and displays it on widget.
Author: Paweł Graczyk
Version: 0.0.1
Author URI: http://blog.smentek.eu
*/
error_reporting ( 0 );

/*** "Settings" link in plugins view ***/
if ( !function_exists( 'addLinksOnPluginsView' ) )
{
	function addLinksOnPluginsView( $links )
	{
		$new_links = array();
		$new_links[] = '<a href="options-general.php?page=allegrato.php">' . __( 'Settings', 'allegrato' ) . '</a>';
		$new_links = array_merge( $links, $new_links );	
		return $new_links;
	};
}

add_action( 'plugin_action_links_' . plugin_basename(__FILE__), 'addLinksOnPluginsView' );	

/*** Activation/Deactivation process ***/
if ( !class_exists( 'AllegratoActivator' ) ) 
{
	$path = dirname( __FILE__ );
      if ( file_exists( "$path/allegrato_activator.php" ) ) 
      {
			require_once("$path/allegrato_activator.php");      	
      }
}

if ( !function_exists( 'allegratoActivatePlugin' ) )
{
	function allegratoActivatePlugin()
	{
		$activator = new AllegratoActivator();
		$activator->activate();
	}
}

if ( !function_exists( 'allegratoDeactivatePlugin' ) )
{
	function allegratoDeactivatePlugin()
	{
		$activator = new AllegratoActivator();
		$activator->deactivate();
	}
}

register_activation_hook( plugin_basename(__FILE__), 'allegratoActivatePlugin' );	
register_deactivation_hook( plugin_basename(__FILE__), 'allegratoDeactivatePlugin' );

/*** Options Page ***/
if ( !function_exists( 'addPluginOptionsPage' ) )
{
	function addPluginOptionsPage()
	{
	    if ( function_exists( 'add_options_page' ) )
	    {
			add_options_page( 'Allegrato', 'Allegrato', 'edit_pages', 'allegrato.php', 'callShowOptionsPage' );
	    }
	}
}


if ( !function_exists( 'callShowOptionsPage' ) )
{
	function callShowOptionsPage()
	{
		$allegratoWebapiKeyAllegro = get_option( 'allegrato_webapi_key_allegro' );
		$allegratoWebapiLoginAllegro = get_option( 'allegrato_webapi_login_allegro' );
		$allegratoWebapiPasswordAllegro = get_option( 'allegrato_webapi_password_allegro' );
		$allegratoWebapiSiteAllegro = get_option( 'allegrato_webapi_site_allegro' );
		$allegratoWebapiCountryAllegro = get_option( 'allegrato_webapi_country_allegro' );

		$resultSitesInfo = array();
		$resultCountries = array();
		    
		
		if( $allegratoWebapiKeyAllegro != false )
		{
		    if( $allegratoWebapiCountryAllegro == false )
		    {
		        $allegratoWebapiCountryAllegro = AllegratoAllegroSoapClient::COUNTRY_PL;
		    }
		    
		    try 
	        {
	            $accessData = array(
					'login' => get_option( 'allegrato_webapi_login_allegro' ),
					'password' => get_option( 'allegrato_webapi_password_allegro' ),
					'webapi_key' => get_option( 'allegrato_webapi_key_allegro' ),
					'country' => $allegratoWebapiCountryAllegro,
			    );
	            
			    $client = new AllegratoAllegroSoapClient( $accessData );
			    $resultSitesInfo = $client->doGetSitesInfo();
			    $resultCountries = $client->doGetCountries();
	        }
	        catch( Exception $e)
	        {
	            $errorMessages = $e->getMessage();
	            echo "<div class='updated settings-error' id='setting-error-settings_updated'><p><strong>{$errorMessages}</strong></p></div>";
	        }		    
		}
		else
		{
            echo "<div class='updated settings-error' id='setting-error-settings_updated'><p><strong>" . __( 'Please fill in access data for your account. First you will have to save your WebApi key.', 'allegrato' ) . "</strong></p></div>";		    
		}
		
		$disabled = '';
		if( $allegratoWebapiKeyAllegro == false ):
		$disabled = "disabled='disabled' ";
		endif;
		

		echo "<span id='allegrato_ajax_test_result'></span>";
		echo "<div class='allegrato_settings'>";
		echo __( 'To be able to use allegro widgets you will have to gain ', 'allegrato' );
		echo "<a href='http://allegro.pl/help_item.php?tid=122&amp;item=755&amp;zoom=N'>" . __( 'allegro WebApi key', 'allegrato' ) . "</a>. ";
		echo __( 'If you don\'t have it use ', 'allegrato' );
		echo "<a href='http://allegro.pl/contact/contact.php?topic=288'>" . __( "that form.", 'allegrato' ) . "</a>.";

		echo "<form id='allegrato_options_form1' action='options.php' method='post' name='form1'>";
		echo "<input type='hidden' value='allegrato' name='option_page' />";
		echo "<input type='hidden' value='update' name='action' />";
		echo "<input type='hidden' value='" . get_admin_url() . "options-general.php?page=allegrato.php' name='_wp_http_referer' />";
		$allegrato_options_wpnonce = wp_nonce_field( 'allegrato-options' );
		
		echo "<table cellpadding='5' width='100%' class='form-table'>";
		echo "<tbody>";
		
		echo "<tr valign='top'><th scope='row'><label for='allegrato_webapi_key_allegro'>" . __( 'Key', 'allegrato' ) . ":</label></th><td>";
		echo "<input id='allegrato_webapi_key_allegro' value='{$allegratoWebapiKeyAllegro}' type='text' name='allegrato_webapi_key_allegro' />";
		echo "<span class='description'>" . __( 'WebApi key for allegro.', 'allegrato' ) . "</span></td></tr>";

		echo "<tr valign='top'><th scope='row'><label for='allegrato_webapi_login_allegro'>" . __( 'Login', 'allegrato' ) . ":</label></th><td>";
		
		echo "<input {$disabled} id='allegrato_webapi_login_allegro' value='{$allegratoWebapiLoginAllegro}' type='text' name='allegrato_webapi_login_allegro' />";
		echo "<span class='description'>" . __( 'Login for allegro.', 'allegrato' ) . "</span></td></tr>";
		
		echo "<tr valign='top'><th scope='row'><label for='allegrato_webapi_password_allegro'>" .  __( 'Password', 'allegrato' ) . ":</label></th><td>";
		echo "<input {$disabled} id='allegrato_webapi_password_allegro' value='{$allegratoWebapiPasswordAllegro}' type='password' name='allegrato_webapi_password_allegro' />";
		echo "<span class='description'>" . __( 'Password for allegro.', 'allegrato' ) . "</span></td></tr>";
		

		echo "<tr valign='top'><th scope='row'><label for='allegrato_webapi_site_allegro'>" . __( 'Site', 'allegrato' ) . ":</label></th><td>";
		if( isset( $resultSitesInfo['sites-info-list'] ) && count( $resultSitesInfo['sites-info-list'] ) > 0 ):
		echo "<select id='allegrato_webapi_site_allegro' name='allegrato_webapi_site_allegro'>";
			if( $allegratoWebapiSiteAllegro == '' ):
			    echo "<option value='' selected='selected'>" . __( 'Choose site', 'allegrato' ) . "</option>";
			else:
			    echo "<option value=''>" . __( 'Choose site', 'allegrato' ) . "</option>";
			endif;
		
		foreach( $resultSitesInfo['sites-info-list'] as $site )
		{
 
			$variableName = 'site-name';
			$siteName = $site->$variableName;
 
			$selected = "";
			if( $siteName == $allegratoWebapiSiteAllegro )
			{
				$selected = " selected='selected'";	
			}
			
			echo "<option value='{$siteName}' {$selected}>{$siteName}</option>";	
		}
		else:
		    echo "<select disabled='disabled' id='allegrato_webapi_site_allegro' name='allegrato_webapi_site_allegro'>";
		    echo "<option value='' selected='selected'>" . __( 'Choose site', 'allegrato' ) . "</option>";
		endif;
		
		echo "</select>";
		echo "<span class='description'>" . __( 'Site for allegro.', 'allegrato' ) . "</span></td></tr>";
		
		echo "<tr valign='top'><th scope='row'><label for='allegrato_webapi_country_allegro'>" . __( 'Country', 'allegrato' ) . ":</label></th><td>";
		
        if( count( $resultCountries ) > 0 ):
        echo "<select id='allegrato_webapi_country_allegro' name='allegrato_webapi_country_allegro'>";
	        if( $allegratoWebapiCountryAllegro == '' ):
	            echo "<option value='' selected='selected'>" . __( 'Choose country', 'allegrato' ) . "</option>";
	        else:
	            echo "<option value=''>" . __( 'Choose country', 'allegrato' ) . "</option>";
	        endif;
        
		foreach( $resultCountries as $country ):
			$countryIdName = 'country-id';
			$variableNameName = 'country-name';
			$countryId = $country->$countryIdName;
			$countryName = $country->$variableNameName;

			$selected = "";
			if( $countryId == $allegratoWebapiCountryAllegro )
			{
				$selected = " selected='selected'";	
			}
			
			echo "<option value='{$countryId}'{$selected}>{$countryName}</option>";	
		endforeach;
		else:
		    echo "<select disabled='disabled' id='allegrato_webapi_site_allegro' name='allegrato_webapi_country_allegro'>";
		    echo "<option value='' selected='selected'>" . __( 'Choose country', 'allegrato' ) . "</option>";
		endif;
		echo "</select>";
		echo "<span class='description'>" . __( 'Country for allegro.', 'allegrato' ) ."</span></td></tr>";		
		
		
		if ( $disabled == '' ):
			echo "<tr valign='top'><th scope='row'><label for='allegrato_test_remote_system_allegro'></label></th><td>";
			echo "<input id='allegrato_test_remote_system_allegro' value='" . __( 'test', 'allegrato' ) . "' type='button' name='allegrato_test_remote_system_allegro' />";
			echo "<span class='description'>" . __( 'Enable to test your access data before saving it.', 'allegrato')."</span></td></tr>";
		endif;		
		
		
		
		
		echo "</tbody></table>"; 
		echo "<input type='submit' value='" . __( 'save', 'allegrato' ) . "' name='Submit' />";
		echo "</form>";
		echo "<script type='text/javascript' src='http://localhost/workspaces/home/wordpress/wp-content/plugins/allegrato/js/allegrato.js'></script>";
		$adminUrl = get_admin_url(); 
		echo "<span class='hidden' id='allegrato_admin_url'>{$adminUrl}admin-ajax.php</span>";
		$ajaxNonce = wp_create_nonce( 'allegrato_test_remote_system_allegro', 'allegrato_test_remote_system_allegro_wpnonce' );
		echo "<span class='hidden' id='allegrato_admin_ajax_nonce'>{$ajaxNonce}</span>";
		echo "</div>";
		
	}
}

add_action( 'wp_ajax_allegrato_test_remote_system_allegro', 'allegratoTestRemoteSystemAllegro' );

if ( !class_exists( 'AllegratoSoapClientAllegro' ) ) 
{
	  $path = dirname( __FILE__ );
      if ( file_exists( "$path/classes/allegro/allegratosoapclientallegro.php" ) ) 
      {
			require_once( "$path/classes/allegro/allegratosoapclientallegro.php" );
      }
}

if ( !class_exists( 'AllegratoCachedClientAllegro' ) ) 
{
    $path = dirname( __FILE__ );
    if ( file_exists( "$path/classes/allegro/allegratocachedclientallegro.php" ) ) 
    {
        require_once( "$path/classes/allegro/allegratocachedclientallegro.php" );
    }
}

function allegratoTestRemoteSystemAllegro()
{
	if ( check_ajax_referer( 'allegrato_test_remote_system_allegro' ) ) 
	{
		$accessData = array(
			'login' => $_POST['allegrato_webapi_login_allegro'],
			'password' => $_POST['allegrato_webapi_password_allegro'],
			'webapi_key' => $_POST['allegrato_webapi_key_allegro'],
			'country' => $_POST['allegrato_webapi_country_allegro'],
		); 

		try
		{
			$client = new AllegratoAllegroSoapClient( $accessData );
			$resultCountries = $client->login();
			echo json_encode( array( 'type' => 'feedback', 'text' => __( 'Successfull log-in. Your access data is correct. Please save it now.', 'allegrato' ) ) );
			die();
		}
		catch( Exception $e )
		{
			echo json_encode( array( 'type' => 'error', 'text' => $e->getMessage() ) );
			die();
		}
	}	
	echo json_encode( array( 'type' => 'error', 'text' => 'Podczas przetwarzania wystąpiły błędy z nonce.' ) );
	die();
}

function allegratoFetchMyAuctionsAllegro()
{
	$accessData = array(
		'login' => get_option( 'allegrato_webapi_login_allegro' ),
		'password' => get_option( 'allegrato_webapi_password_allegro' ),
		'webapi_key' => get_option( 'allegrato_webapi_key_allegro' ),
		'country' => get_option( 'allegrato_webapi_country_allegro' ),
	); 

	$client = new AllegratoCachedClientAllegro( new AllegratoAllegroSoapClient( $accessData ) );
	$auctions = $client->doGetUserItems();
	return $auctions; 
}

function allegratoFetchSitesAllegro()
{	
	$accessData = array(
		'login' => get_option( 'allegrato_webapi_login_allegro' ),
		'password' => get_option( 'allegrato_webapi_password_allegro' ),
		'webapi_key' => get_option( 'allegrato_webapi_key_allegro' ),
		'country' => get_option( 'allegrato_webapi_country_allegro' ),
	); 
	$client = new AllegratoCachedClientAllegro( new AllegratoAllegroSoapClient( $accessData ) );
	$sites = $client->doGetSitesInfo();
	return $sites; 
}

add_action( 'admin_menu', 'addPluginOptionsPage' );
add_action( 'admin_init', 'registerAllegratoSettings' );
add_action( 'init', 'allegratoInit' );


function allegratoInit()
{
    $pluginDir = basename(dirname(__FILE__));
    load_plugin_textdomain('allegrato', false, "$pluginDir/translations");
}


if ( !function_exists( 'registerAllegratoSettings' ) )
{
	function registerAllegratoSettings()
	{
		$activator = new AllegratoActivator();
		foreach( $activator->getOptions() as $option )
		{
			register_setting( 'allegrato', $option );			
		}
	}
}

/*** Activation/Deactivation process ***/
if ( !class_exists( 'AllegratoMyAuctionsAllegro' ) ) 
{
	$path = dirname( __FILE__ );
      if ( file_exists( "$path/widgets/allegrato_my_auctions_allegro.php" ) ) 
      {
			require_once("$path/widgets/allegrato_my_auctions_allegro.php");      	
      }
}
