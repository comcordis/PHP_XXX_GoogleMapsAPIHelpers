<?php

class XXX_GoogleMapsAPI_PlacesService
{
	public static $key = '';
	
	public static function lookupPlace ($rawPlaceString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// https://maps.googleapis.com/maps/api/place/autocomplete/json?input=Vict&types=geocode&language=fr&sensor=true&key=AddYourOwnKeyHere
		$uri = 'https://maps.googleapis.com/maps/api/place/textsearch/json';
		$uri .= '?';
		$uri .= 'input=' . urlencode($rawPlaceString);
		$uri .= '&sensor=false';
		if (self::$key != '')
		{
			$uri .= '&key=' . self::$key;
		}
		//$uri .= '&types=geocode';
		if ($languageCode != '')
		{
			$uri .= '&language=' . $languageCode;
		}
		if ($locationBias != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
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