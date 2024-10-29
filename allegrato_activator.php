<?php
error_reporting ( 0 );
class AllegratoActivator
{
	private $options = array(
		'allegrato_webapi_key_allegro' => null,
		'allegrato_webapi_login_allegro' => null,
		'allegrato_webapi_password_allegro' => null,
		'allegrato_webapi_site_allegro' => null,
		'allegrato_webapi_country_allegro' => null,
	);
	
	public function activate()
	{
		foreach( $this->options as $optionKey => $optionValue )
		{
			if( isset( $optionValue ) )
			{
				add_option( $optionKey, $optionValue );	
			}
		}
	}
	
	public function deactivate()
	{
		foreach( array_keys( $this->options ) as $optionKey )
		{
			delete_option( $optionKey );
		}
	}
	
	public function getOptions()
	{
		return array_keys( $this->options );
	}
}