<?php
error_reporting ( 0 );
class AllegratoAllegroSoapClient extends SoapClient
{
	const COUNTRY_PL = 1;
	const QUERY_ALLEGROWEBAPI = 228;

	private $accessData = array(
		'login' => null,
		'password' => null,
		'webapi_key' => null,
		'country' => self::QUERY_ALLEGROWEBAPI,
	);
	
	private $userId = null;
	private $sessionHandlePart = null;
	private $serverTime = null;
	
	public function __construct( $accessData=array() )
	{
		if( !empty( $accessData ) )
		{
			$this->accessData = $accessData;
		}
		
		parent::__construct( 'http://webapi.allegro.pl/uploader.php?wsdl' );
	}
	
	private function doQuerySysStatus()
	{
		$msg = array(
			"sysvar" => 1, 
			"country-id" => $this->accessData['country'], 
			"webapi-key" => $this->accessData['webapi_key'],		
		);
		
		try
		{
			return $this->__call( 'doQuerySysStatus', $msg );
		}
		catch( Exception $e )
		{
			//TODO: debug			
            //$e->faultcode
		 	throw $e;
		}		
	}
	
	public function login()
	{
		if( !isset( $this->userId ) )
		{
			$response = $this->doLogin();	
			
			$this->userId = $response['user-id'];
			$this->sessionHandlePart = $response['session-handle-part'];
			$this->serverTime = $response['server-time'];
			
			return $response;		
		}
		
	}
	
	private function doLogin()
	{
		 try
		 {
			$version = $this->doQuerySysStatus();
			$msg = array(
				"user-login" => $this->accessData['login'],
			 	"user-password" => $this->accessData['password'],
			 	"country-code" => $this->accessData['country'],
			 	"webapi-key" => $this->accessData['webapi_key'],
			 	"local-version" => $version['ver-key'],
			 );
			 
			 return $this->__call( 'doLogin', $msg );
		 }
		 catch( Exception $e )
		 {
			//TODO: debug		 	
		 	throw $e;
		 }
	}
	
//	public function doGetUserId()
//	{
//		try
//		{
//			$msg = array(
//				'country-id' => self::COUNTRY_PL,
//		      	'user-login' => 'ssmentek',
//		   		'webapi-key' => $this->accessData['webapi_key'],
//			);
//			
//			return $this->__call( "doGetUserId", $msg );
//		}
//		catch( Exception $e )
//		{
//		 	echo $e->getMessage();
//		}
//				
//	}
	
	public function doGetUserItems()
	{
		$results = $this->login();
		try
		{
			$msg = array(
				'user-id' => $this->userId,
				'webapi-key' => $this->accessData['webapi_key'],
				'country-id'=> $this->accessData['country'],
				'offset' => 0,
				'limit' => 0,
			);
			
			return $this->__call( 'doGetUserItems', $msg );
			
		}
		catch( Exception $e )
		{
			//TODO: debug			
		 	throw $e;
		}
	}
	
	
	public function doGetSitesInfo()
	{
		try
		{
			$msg = array(
	      		'country-code' => $this->accessData['country'],
	      		'webapi-key' => $this->accessData['webapi_key'],
			);
			return $this->__call( 'doGetSitesInfo', $msg );
		}
		catch( Exception $e )
		{
			//TODO: debug
		 	throw $e;
		}			
	}
	
	public function doGetCountries()
	{
		try
		{
			$msg = array(
	      		'country-code' => $this->accessData['country'],
	      		'webapi-key' => $this->accessData['webapi_key'],
			);
			return $this->__call( 'doGetCountries', $msg );
		}
		catch( Exception $e )
		{
			//TODO: debug
		 	throw $e;
		}			
	}
	
}