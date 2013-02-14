<?php

class XXX_GoogleMapsAPIHelpers
{
	public static function lookupAddress ($addressString = '', $language = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false
		$uri = 'http://maps.googleapis.com/maps/api/geocode/json';
		$uri .= '?';
		$uri .= 'address=' . urlencode($addressString);
		$uri .= '&sensor=false';
		if ($language != '')
		{
			$uri .= '&language=' . $language;
		}
		if ($region != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
		$result = self::doGETRequest($uri, 'geocoder');
		
		if ($result != false)
		{
			$result['originalAddressString'] = $addressString;
			$result['language'] = $language;
			$result['locationBias'] = $locationBias;
		}
		
		return $result;
	}
	
	public static function reverseLookupCoordinate ($latitude = 0, $longitude = 0, $language = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&sensor=true_or_false
		$uri = 'http://maps.googleapis.com/maps/api/geocode/json';
		$uri .= '?';
		$uri .= 'latlng=' . urlencode($latitude . ',' . $longitude);
		$uri .= '&sensor=false';
		if ($language != '')
		{
			$uri .= '&language=' . $language;
		}
		if ($region != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
		$result = self::doGETRequest($uri, 'geocoder');
		
		if ($result != false)
		{
			$result['originalLatitude'] = $latitude;
			$result['originalLongitude'] = $longitude;
			$result['language'] = $language;
			$result['locationBias'] = $locationBias;
		}
		
		return $result;
	}
	
	public static function getTimezoneInformationForLocation ($latitude = 0, $longitude = 0, $timestamp = 0, $language = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/timezone/json?location=39.6034810,-119.6822510&timestamp=1331161200&sensor=true_or_false
		$uri = 'https://maps.googleapis.com/maps/api/timezone/json';
		$uri .= '?';
		$uri .= 'location=' . urlencode($latitude . ',' . $longitude);
		$uri .= '&timestamp=' . urlencode($timestamp);
		$uri .= '&sensor=false';
		if ($language != '')
		{
			$uri .= '&language=' . $language;
		}
		if ($region != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
		$result = self::doGETRequest($uri, 'timezone');
		
		if ($result != false)
		{
			$result['originalLatitude'] = $latitude;
			$result['originalLongitude'] = $longitude;
			$result['timestamp'] = $timestamp;
			$result['language'] = $language;
			$result['locationBias'] = $locationBias;
		}
		
		return $result;
	}
	
	public static function getDistanceAndDurationInformationForAddressStrings ($from = '', $to = '', $language = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC&mode=bicycling&language=fr-FR&sensor=false
		$uri = 'http://maps.googleapis.com/maps/api/distancematrix/json';
		$uri .= '?';
		$uri .= 'origins=' . urlencode($from);
		$uri .= '&destinations=' . urlencode($to);
		$uri .= '&units=metric';
		$uri .= '&mode=driving';
		$uri .= '&sensor=false';
		if ($language != '')
		{
			$uri .= '&language=' . $language;
		}
		if ($region != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
		$result = self::doGETRequest($uri, 'distanceAndDurationForAddressStrings');
		
		if ($result != false)
		{
			$result['originalFromAddressString'] = $from;
			$result['originalToAddressString'] = $to;
			$result['language'] = $language;
			$result['locationBias'] = $locationBias;
		}
		
		return $result;
	}
	
	
	public static function getDistanceAndDurationInformationForCoordinates ($fromLatitude = 0, $fromLongitude = 0, $toLatitude = 0, $toLongitude = 0, $language = 'en', $locationBias = '')
	{
		$result = false;
		
		// http://maps.googleapis.com/maps/api/distancematrix/json?origins=41.43206,-81.38992&destinations=41.43206,-81.38992&mode=bicycling&language=fr-FR&sensor=false
		$uri = 'http://maps.googleapis.com/maps/api/distancematrix/json';
		$uri .= '?';
		$uri .= 'origins=' . urlencode($fromLatitude . ',' . $fromLongitude);
		$uri .= '&destinations=' . urlencode($toLatitude . ',' . $toLongitude);
		$uri .= '&units=metric';
		$uri .= '&mode=driving';
		$uri .= '&sensor=false';
		if ($language != '')
		{
			$uri .= '&language=' . $language;
		}
		if ($region != '')
		{
			$uri .= '&region=' . $locationBias;
		}
		
		$result = self::doGETRequest($uri, 'distanceAndDurationForCoordinates');
		
		if ($result != false)
		{
			$result['originalFromLatitde'] = $fromLatitude;
			$result['originalFromLongitude'] = $fromLongitude;
			$result['originalToLatitude'] = $toLatitude;
			$result['originalToLongitude'] = $toLongitude;
			$result['language'] = $language;
			$result['locationBias'] = $locationBias;
		}
		
		return $result;
	}
	
	public static function doGETRequest ($uri = '', $type = 'geocoder')
	{
		$result = false;
		
		$request = curl_init($uri);
		
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($request);
		curl_close($request);
		
		if ($response != false)
		{
			if ($type == 'geocoder')
			{
				$result = self::parseGeocoderResponse($response);
			}
			else if ($type == 'timezone')
			{
				$result = self::parseTimezoneResponse($response);
			}
			else if ($type == 'distanceAndDurationForAddressStrings')
			{
				$result = self::parseDistanceAndDurationForAddressStringsResponse($response);
			}
			else if ($type == 'distanceAndDurationForCoordinates')
			{
				$result = self::parseDistanceAndDurationForCoordinatesResponse($response);
			}
		}
		
		return $result;
	}
	
	
	public static function parseDistanceAndDurationForAddressStringsResponse ($response)
	{
		$result = false;
		
		$response = json_decode($response);
		$response = self::objectToArray($response);
		
		//print_r($response);
		
		if ($response['status'] == 'OK')
		{
   			$result = array();
   			$result['distance'] = $response['rows'][0]['elements'][0]['distance']['value'];
   			$result['duration'] = $response['rows'][0]['elements'][0]['duration']['value'];
   			$result['fromAddressString'] = $response['origin_addresses'][0];
   			$result['toAddressString'] = $response['destination_addresses'][0];
		}
		
		return $result;
	}
	
	public static function parseDistanceAndDurationForCoordinatesResponse ($response)
	{
		$result = false;
		
		$response = json_decode($response);
		$response = self::objectToArray($response);
		
		//print_r($response);
		
		if ($response['status'] == 'OK')
		{
   			$result = array();
   			$result['distance'] = $response['rows'][0]['elements'][0]['distance']['value'];
   			$result['duration'] = $response['rows'][0]['elements'][0]['duration']['value'];
   			$result['fromAddressString'] = $response['origin_addresses'][0];
   			$result['toAddressString'] = $response['destination_addresses'][0];
		}
		
		return $result;
	}
	
	public static function parseTimezoneResponse ($response)
	{
		$result = false;
		
		$response = json_decode($response);
		$response = self::objectToArray($response);
		
		//print_r($response);
		
		if ($response['status'] == 'OK')
		{
   			$result = array();
   			$result['offset'] = $response['rawOffset'];
   			$result['daylightSavingTimeOffset'] = $response['dstOffset'];
   			$result['timezoneCity'] = $response['timeZoneId'];
   			$result['timezoneName'] = $response['timeZoneName'];
		}
		
		return $result;
	}
	
	public static function parseGeocoderResponse ($response)
	{
		$result = false;
		
		$response = json_decode($response);
		$response = self::objectToArray($response);
		
		//print_r($response);
		
		if ($response['status'] == 'OK')
		{
			$result = array();
			
			$firstResult = $response['results'][0];
			
			$result['latitude'] = $firstResult['geometry']['location']['lat'];
			$result['longitude'] = $firstResult['geometry']['location']['lng'];
			$result['addressString'] = $firstResult['formatted_address'];
			
			for ($i = 0, $iEnd = count($firstResult['address_components']); $i < $iEnd; ++$i)
			{
				$addressComponent = $firstResult['address_components'][$i];
				
				for ($j = 0, $jEnd = count($addressComponent['types']); $j < $jEnd; ++$j)
				{
					$type = $addressComponent['types'][$j];
					
					if ($type == 'street_number')
					{
						$result['houseNumber'] = $addressComponent['long_name'];
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
						$result['state'] = $addressComponent['long_name'];
						$result['stateCode'] = $addressComponent['short_name'];
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
		}
		
		return $result;
	}	
	
	public static function objectToArray ($d)
	{
		if (is_object($d))
		{
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d))
		{
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map('XXX_GoogleMapsAPIHelpers::objectToArray', $d);
		}
		else
		{
			// Return array
			return $d;
		}
	}
}

class XXX_CurrencyImporter
{
	public static function doGETRequest ($uri = '')
	{
		$result = false;
		
		$request = curl_init($uri);
		
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
		
		$response = curl_exec($request);
		curl_close($request);
		
		if ($response != false)
		{
			$result = $response;			
		}
		
		return $result;
	}
	
	public static function import ($method = 'ecb')
	{
		$result = false;
		
		switch ($method)
		{
			case 'ecb':
				$uri = 'http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml';
		
				$result = self::doGetRequest($uri);
				
				if ($result)
				{				
					preg_match_all('`currency=\'([0-9a-z]{1,})\'\\srate=\'([0-9.,]{1,})\'`i', $result, $matches);
					
					$tempResult = array();
					
					for ($i = 0, $iEnd = count($matches[0]); $i < $iEnd; ++$i)
					{
						$tempResult[strtolower($matches[1][$i])] = floatval($matches[2][$i]);
					}
					
					$tempResult['eur'] = 1;
					
					$result = $tempResult;
				}
				break;
		}
		
		return $result;
	}
}

?>