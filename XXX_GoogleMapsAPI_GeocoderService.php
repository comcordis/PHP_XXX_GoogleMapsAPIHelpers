<?php

class XXX_GoogleMapsAPI_GeocoderService
{
	public static function lookupAddress ($rawAddressString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false
		
		$protocol = 'http://';
		
		if (XXX_GoogleMapsAPIHelpers::$encryptedConnection)
		{
			$protocol = 'https://';
		}
		
		$domain = 'maps.googleapis.com';
		$path = '/maps/api/geocode/json';
		$path .= '?';
		$path .= 'address=' . urlencode($rawAddressString);
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
				'lookupType' => 'rawAddressString',
				'rawAddressString' => $rawAddressString,
				'languageCode' => $languageCode,
				'locationBias' => $locationBias
			);
			
			$result = self::parseGeocoderResponse($response, $extraInformation);
		}
		
		return $result;
	}
	
	public static function lookupGeoPosition ($latitude = 0, $longitude = 0, $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false
		
		$protocol = 'http://';
		
		if (XXX_GoogleMapsAPIHelpers::$encryptedConnection)
		{
			$protocol = 'https://';
		}
				
		$domain = 'maps.googleapis.com';
		$path = '/maps/api/geocode/json';
		$path .= '?';
		$path .= 'latlng=' . urlencode($latitude . ',' . $longitude);
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
				'lookupType' => 'geoPosition',
				'latitude' => $latitude,
				'longitude' => $longitude,
				'languageCode' => $languageCode,
				'locationBias' => $locationBias
			);
			
			$result = self::parseGeocoderResponse($response, $extraInformation);
		}
		
		return $result;
	}
	
	public static function parseGeocoderResponse ($response = array(), $extraInformation = array())
	{
		$results = false;
		
		if (XXX_Array::getFirstLevelItemTotal($response['results']))
		{
			$results = array();
			
			foreach ($response['results'] as $result)
			{
				$results[] = self::parseGeocoderResult($result, $extraInformation);
			}
		}
		
		return $results;
	}	
	
	public static function parseGeocoderResult ($geocoderResult = array(), $extraInformation = array())
	{
		$result = false;
		
		if ($geocoderResult['formatted_address'] != '')
		{
			$result = array();
			
			if (XXX_Type::isArray($extraInformation))
			{
				$result = XXX_Array::merge($result, $extraInformation);
			}
			
			$result['latitude'] = $geocoderResult['geometry']['location']['lat'];
			$result['longitude'] = $geocoderResult['geometry']['location']['lng'];
			$result['formattedAddressString'] = $geocoderResult['formatted_address'];
			$result['types'] = $geocoderResult['types'];
			
			$result['precisionType'] = $geocoderResult['geometry']['location_type'];
			
			$result['isPartialMatch'] = XXX_Type::makeBoolean($geocoderResult['partial_match']);
			
			for ($i = 0, $iEnd = count($geocoderResult['address_components']); $i < $iEnd; ++$i)
			{
				$addressComponent = $geocoderResult['address_components'][$i];
				
				for ($j = 0, $jEnd = count($addressComponent['types']); $j < $jEnd; ++$j)
				{
					$type = $addressComponent['types'][$j];
					
					if ($type == 'street_number')
					{
						$result['streetNumber'] = $addressComponent['long_name'];
					}
					else if ($type == 'route')
					{
						$result['street'] = $addressComponent['long_name'];
					}
					else if ($type == 'locality')
					{
						$result['city'] = $addressComponent['long_name'];
					}
					else if ($type == 'administrative_area_level_1')
					{
						$result['stateOrProvince'] = $addressComponent['long_name'];
						$result['stateOrProvinceCode'] = $addressComponent['short_name'];
					}
					else if ($type == 'country')
					{
						$result['country'] = $addressComponent['long_name'];
						$result['countryCode'] = $addressComponent['short_name'];
					}
					else if ($type == 'postal_code')
					{
						$result['postalCode'] = $addressComponent['short_name'];
					}
				}
			}
			
			$result = $result;
		}
		
		return $result;
	}
}

?>