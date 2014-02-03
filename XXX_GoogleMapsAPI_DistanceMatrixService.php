<?php

/*

elements = origins * destinations

625 elements per query.
1 000 elements per 10 seconds.
100 000 elements per 24 hour period.

Distance Matrix API URLs are restricted to approximately 2000 characters, after URL encoding.

You now have a distance matrix quota of 1,000 elements every 10 seconds and 864,000 elements per day.

It expires in 13 days (2014-01-22)

*/

class XXX_GoogleMapsAPI_DistanceMatrixService
{
	// Free
	public static $serverKey = '';
	
	// Business
	public static $client_ID = '';
	public static $cryptoKey = '';
	
	public static $httpsOnly = false;
	
	public static $authenticationType = 'free';
	
	public static function getRideInformationForAddressStrings ($fromRawAddressString = '', $toRawAddressString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC&mode=bicycling&language=fr-FR&sensor=false
		
		if (self::$httpsOnly)
		{
			$protocol = 'https://';
		}
		else
		{		
			$protocol = 'http://';
		
			if (class_exists('XXX_HTTPServer') && XXX_HTTPServer::$encryptedConnection)
			{
				$protocol = 'https://';
			}
		}
		
		$domain = 'maps.googleapis.com';
		
		$path = '/maps/api/distancematrix/json';
		$path .= '?';
		$path .= 'origins=' . urlencode($fromRawAddressString);
		$path .= '&destinations=' . urlencode($toRawAddressString);
		$path .= '&units=metric';
		$path .= '&mode=driving';
		$path .= '&sensor=false';
		
		if ($languageCode != '')
		{
			$path .= '&language=' . $languageCode;
		}
		if ($locationBias != '')
		{
			$path .= '&region=' . $locationBias;
		}
		
		// Free
		$authenticationType = 'none';
		
		if (self::$authenticationType == 'business')
		{
			$authenticationType = 'client_IDAndSignature';
		}
		
		$path = XXX_GoogleMapsAPIHelpers::addAuthenticationToPath($path, $authenticationType, self::$serverKey, self::$client_ID, self::$cryptoKey);
				
		$uri = $protocol . $domain . $path;
		
		$response = XXX_GoogleMapsAPIHelpers::doGETRequest($uri);
		
		if ($response != false && $response['status'] == 'OK')
		{
			$extraInformation = array
			(
				'fromRawAddressString' => $fromRawAddressString,
				'toRawAddressString' => $toRawAddressString,
				'languageCode' => $languageCode,
				'locationBias' => $locationBias
			);
			
			$result = self::parseDistanceMatrixResponse($response, $extraInformation);
		}
		
		return $result;
	}
	
	// ~ approximately 40 geo positions
	public static function getRideInformationForGeoPositions ($fromLatitude = 0, $fromLongitude = 0, $toLatitude = 0, $toLongitude = 0, $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=41.43206,-81.38992&destinations=41.43206,-81.38992&mode=bicycling&language=fr-FR&sensor=false
		
		if (self::$httpsOnly)
		{
			$protocol = 'https://';
		}
		else
		{		
			$protocol = 'http://';
		
			if (class_exists('XXX_HTTPServer') && XXX_HTTPServer::$encryptedConnection)
			{
				$protocol = 'https://';
			}
		}
				
		$domain = 'maps.googleapis.com';
		
		$path = '/maps/api/distancematrix/json';
		$path .= '?';
		
		if (XXX_Type::isArray($fromLatitude))
		{
			$origins = '';
			$destinations = '';
			
			$geoPositionTotal = 0;
			$i = 0;
			
			foreach ($fromLatitude as $tempGeoPosition)
			{
				$originsAddition = '';
				$destinationsAddition = '';
				
				if ($i > 0)
				{
					$originsAddition .= '|';
					$destinationsAddition .= '|';
				}
				
				$originsAddition .= urlencode($tempGeoPosition['latitude'] . ',' . $tempGeoPosition['longitude']);
				$destinationsAddition .= urlencode($tempGeoPosition['latitude'] . ',' . $tempGeoPosition['longitude']);
				
				$uriLength = 180;
				$uriLength += XXX_String::getCharacterLength($origins);
				$uriLength += XXX_String::getCharacterLength($destinations);
				$uriLength += XXX_String::getCharacterLength($originsAddition);
				$uriLength += XXX_String::getCharacterLength($destinationsAddition);
				
				$geoPositionTotal = $i + 1;
				
				if ($uriLength < 1800)
				{
					$origins .= $originsAddition;
					$destinations .= $destinationsAddition;
				}
				else
				{
					--$geoPositionTotal;
					break;
				}
				
				++$i;
			}
			
			$path .= 'origins=' . $origins;
			$path .= '&destinations=' . $destinations;
		}
		else
		{		
			$path .= 'origins=' . urlencode($fromLatitude . ',' . $fromLongitude);
			$path .= '&destinations=' . urlencode($toLatitude . ',' . $toLongitude);
		}
		$path .= '&units=metric';
		$path .= '&mode=driving';
		$path .= '&sensor=false';
		
		if ($languageCode != '')
		{
			$path .= '&language=' . $languageCode;
		}
		if ($locationBias != '')
		{
			$path .= '&region=' . $locationBias;
		}
		
		// Free
		$authenticationType = 'none';
		
		if (self::$authenticationType == 'business')
		{
			$authenticationType = 'client_IDAndSignature';
		}
		
		$path = XXX_GoogleMapsAPIHelpers::addAuthenticationToPath($path, $authenticationType, self::$serverKey, self::$client_ID, self::$cryptoKey);
				
		$uri = $protocol . $domain . $path;
		
		
		//XXX_Type::peakAtVariable($uri);
				
		$response = XXX_GoogleMapsAPIHelpers::doGETRequest($uri);
		
		
		//XXX_Type::peakAtVariable($response);
		
		if ($response != false && $response['status'] == 'OK')
		{
			if (XXX_Type::isArray($fromLatitude))
			{
				$extraInformation = array
				(
					'geoPositionTotal' => $geoPositionTotal,
					'geoPositions' => $fromLatitude,
					'languageCode' => $languageCode,
					'locationBias' => $locationBias
				);
			}
			else
			{			
				$extraInformation = array
				(
					'fromLatitde' => $fromLatitude,
					'fromLongitude' => $fromLongitude,
					'toLatitude' => $toLatitude,
					'toLongitude' => $toLongitude,
					'languageCode' => $languageCode,
					'locationBias' => $locationBias
				);
			}
			
			$result = self::parseDistanceMatrixResponse($response, $extraInformation);
		}
		else
		{
			trigger_error($response['status']);
		}
		
		return $result;
	}
	
	public static function parseDistanceMatrixResponse ($response, $extraInformation = array())
	{
		$result = false;
				
		if (XXX_Array::getFirstLevelItemTotal($response['origin_addresses']))
		{
   			$result = array();
   			
   			if (XXX_Type::isArray($extraInformation))
   			{
   				$result = XXX_Array::merge($result, $extraInformation);
   			}
   			
   			if ($result['geoPositionTotal'])
   			{
   				for ($i = 0, $iEnd = $result['geoPositionTotal']; $i < $iEnd; ++$i)
   				{
   					$result['geoPositions'][$i]['formattedAddressString'] = $response['origin_addresses'][$i];
   					$result['geoPositions'][$i]['result'] = array();
   					
   					for ($j = 0, $jEnd = $result['geoPositionTotal']; $j < $jEnd; ++$j)
   					{
   						$result['geoPositions'][$i]['result'][$j] = array();
   						$result['geoPositions'][$i]['result'][$j]['distance'] = $response['rows'][$i]['elements'][$j]['distance']['value'];
			   			$result['geoPositions'][$i]['result'][$j]['duration'] = $response['rows'][$i]['elements'][$j]['duration']['value'];
			   			$result['geoPositions'][$i]['result'][$j]['fromFormattedAddressString'] = $response['origin_addresses'][$i];
			   			$result['geoPositions'][$i]['result'][$j]['toFormattedAddressString'] = $response['destination_addresses'][$j];
   					}
   				}
   			}
   			else
   			{   			
	   			$result['distance'] = $response['rows'][0]['elements'][0]['distance']['value'];
	   			$result['duration'] = $response['rows'][0]['elements'][0]['duration']['value'];
	   			$result['fromFormattedAddressString'] = $response['origin_addresses'][0];
	   			$result['toFormattedAddressString'] = $response['destination_addresses'][0];
   			}
		}
		
		return $result;
	}
}

?>