<?php
error_reporting ( 0 );
class AllegratoCachedClientAllegro
{
    private $client;
    private $ttl;
    
    public function __construct( $client, $ttl=3600 )
    {
        $this->client = $client;
        $this->ttl = $ttl;
    }
    
    public function doGetUserItems()
    {
        $key = $this->calculateCacheKey( __METHOD__ );
        $flag = 'allegrato_my_auctions_allegro_widget';
		
        $cachedResults = wp_cache_get( $key, $flag );
        if( $cachedResults === false )
        {
            $functionName = __FUNCTION__;
            $freschResults = $this->client->$functionName();
               
            wp_cache_add( $key, $freschResults, $flag, $this->ttl );
            return $freschResults;    
        }
		return $cachedResults;
    }

    public function doGetSitesInfo()
    {
        $key = $this->calculateCacheKey( __METHOD__ );
        $flag = 'allegrato_sites_allegro_widget';
		
        $cachedResults = wp_cache_get( $key, $flag );
        if( $cachedResults === false )
        {
            $functionName = __FUNCTION__;
            $freschResults = $this->client->$functionName();
               
            wp_cache_add( $key, $freschResults, $flag, $this->ttl );
            return $freschResults;    
        }
		return $cachedResults;        
    }
    
    private function calculateCacheKey( $method )
	{
        return md5( $method );
	}
    
}