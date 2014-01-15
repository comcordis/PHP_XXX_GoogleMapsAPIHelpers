<?php

/*

SOLVED. In Google API Console, I had declared allowed server IPs. I was assuming that if I declared an IP, I was just ensuring that requests would be allowed only from that IP. 
In order for this to work, you must also declare the allowed per-user limits for each IP, or else it just doesn't allow any requests. I removed all the allowed IPs, waited for 3-4 minutes, and the request was allowed.

*/

class XXX_GoogleMapsAPI_PlacesService
{
	public static function lookupPlace ($rawPlaceString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// Maps API for Business customers should not include a client or signature parameter with their requests.
		
		// https://maps.googleapis.com/maps/api/place/autocomplete/json?input=Vict&types=geocode&language=fr&sensor=true&key=AddYourOwnKeyHere
		
		$protocol = 'http://';
		$protocol = 'https://'; // Should be https
		
		if (XXX_GoogleMapsAPIHelpers::$encryptedConnection)
		{
			$protocol = 'https://';
		}
		
		$domain = 'maps.googleapis.com';
		$path = '/maps/api/place/textsearch/json';
		$path .= '?';
		$path .= 'query=' . urlencode($rawPlaceString);
		$path .= '&sensor=false';
		
		//$path .= '&types=geocode';
		if ($languageCode != '')
		{
			$path .= '&language=' . $languageCode;
		}
		if ($locationBias != '')
		{
			$path .= '&region=' . urlencode($locationBias);
		}
		
		$path = XXX_GoogleMapsAPIHelpers::addAuthenticationToPath($path, true);
				
		$uri = $protocol . $domain . $path;
		
		$response = XXX_GoogleMapsAPIHelpers::doGETRequest($uri);
				
		if ($response != false && $response['status'] == 'OK')
		{
			$extraInformation = array
			(
				'rawPlaceString' => $rawPlaceString,
				'languageCode' => $languageCode,
				'locationBias' => $locationBias
			);
			
			$result = self::parsePlacesResponse($response, $extraInformation);
		}
		
		return $result;
	}
	
	public static function parsePlacesResponse ($placesResponse = array(), $extraInformation = array())
	{
		$results = false;
		
		if ($placesResponse['results'])
		{
			$results = array();
			
			foreach ($placesResponse['results'] as $placeResult)
			{
				$results[] = self::parsePlaceResult($placeResult, $extraInformation);
			}
		}
		
		return $results;
	}
	
	public static function parsePlaceResult ($placeResult = array(), $extraInformation = array())
	{
		$result = false;
		
		$result = false;
		
		if ($placeResult['name'] != '')
		{
			$result = array();
			
			if (XXX_Type::isArray($extraInformation))
			{
				$result = XXX_Array::merge($result, $extraInformation);
			}
		
			$result['formattedAddressString'] = $placeResult['formatted_address'];
			$result['latitude'] = $placeResult['geometry']['location']['lat'];
			$result['longitude'] = $placeResult['geometry']['location']['lng'];
			$result['name'] = $placeResult['name'];
			$result['places_ID'] = $placeResult['id'];
			$result['places_reference'] = $placeResult['reference'];
			$result['types'] = $placeResult['types'];
			$result['attributions'] = $placeResult['attributions'];
		}
		
		return $result;
	}
}

?>