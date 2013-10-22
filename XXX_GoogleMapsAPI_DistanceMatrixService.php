<?php

class XXX_GoogleMapsAPI_DistanceMatrixService
{
	public static function getRideInformationForAddressStrings ($fromRawAddressString = '', $toRawAddressString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC&mode=bicycling&language=fr-FR&sensor=false
		
		$protocol = 'http://';
		
		if (XXX_GoogleMapsAPIHelpers::$encryptedConnection)
		{
			$protocol = 'https://';
		}
		
		$domain = 'maps.googleapis.com';
		
		$path = '/maps/api/distancematrix/json';
		$path .= '?';
		$path .= 'origins=' . urlencode($fromRawAddressString);
		$path .= '&destinations=' . urlencode($to);
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
		
		$path = XXX_GoogleMapsAPIHelpers::addAuthenticationToPath($path);
				
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
		
	public static function getRideInformationForGeoPositions ($fromLatitude = 0, $fromLongitude = 0, $toLatitude = 0, $toLongitude = 0, $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=41.43206,-81.38992&destinations=41.43206,-81.38992&mode=bicycling&language=fr-FR&sensor=false
		
		$protocol = 'http://';
		
		if (XXX_GoogleMapsAPIHelpers::$encryptedConnection)
		{
			$protocol = 'https://';
		}
				
		$domain = 'maps.googleapis.com';
		
		$path = '/maps/api/distancematrix/json';
		$path .= '?';
		$path .= 'origins=' . urlencode($fromLatitude . ',' . $fromLongitude);
		$path .= '&destinations=' . urlencode($toLatitude . ',' . $toLongitude);
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
		
		$path = XXX_GoogleMapsAPIHelpers::addAuthenticationToPath($path);
				
		$uri = $protocol . $domain . $path;
				
		$response = XXX_GoogleMapsAPIHelpers::doGETRequest($uri);
		
		if ($response != false && $response['status'] == 'OK')
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
			
			$result = self::parseDistanceMatrixResponse($response, $extraInformation);
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
   			
   			$result['distance'] = $response['rows'][0]['elements'][0]['distance']['value'];
   			$result['duration'] = $response['rows'][0]['elements'][0]['duration']['value'];
   			$result['fromFormattedAddressString'] = $response['origin_addresses'][0];
   			$result['toFormattedAddressString'] = $response['destination_addresses'][0];
		}
		
		return $result;
	}
}

?>