<?php

class XXX_GoogleMapsAPI_TimezoneService
{
	// Free
	public static $serverKey = '';
	
	// Business
	public static $client_ID = '';
	public static $cryptoKey = '';
	
	// Important: You must submit requests via https, not http.
	public static $httpsOnly = true;
	
	public static $authenticationType = 'free';
	
	public static $error = false;
	
	public static function getTimezoneInformationForGeoPositionAndLocalTimestamp ($latitude = 0, $longitude = 0, $localTimestamp = false, $languageCode = 'en')
	{
		$result = array
		(
			'latitude' => $latitude,
			'longitude' => $longitude,
			'localTimestamp' => $localTimestamp,
			'timestamp' => false,
			'timezoneOffset' => false,
			'type' => 'normal',
			'isWithinRepeatedHour' => false,
			'isWithinSkippedHour' => false,
			'languageCode' => $languageCode
		);
		
			$tempResult = XXX_GoogleMapsAPI_TimezoneService::getExtendedTimezoneInformationForGeoPosition($latitude, $longitude, $localTimestamp);
						
			$normalUTC = $localTimestamp - $tempResult['normalTimezoneOffset'];
			$daylightSavingTimeUTC = $localTimestamp - $tempResult['daylightSavingTimeTimezoneOffset'];
			
			$tempResult1 = XXX_GoogleMapsAPI_TimezoneService::getTimezoneInformationForGeoPosition($latitude, $longitude, $normalUTC);
			$tempResult2 = XXX_GoogleMapsAPI_TimezoneService::getTimezoneInformationForGeoPosition($latitude, $longitude, $daylightSavingTimeUTC);
						
			$localByNormalUTC = $normalUTC + $tempResult1['timezoneOffset'];
			$localByDaylightSavingTimeUTC = $daylightSavingTimeUTC + $tempResult2['timezoneOffset'];
			
			if ($localTimestamp == $localByNormalUTC && $localTimestamp == $localByDaylightSavingTimeUTC)
			{
				$result['isWithinRepeatedHour'] = true;
				
				$result['type'] = 'both';
			}
			else if ($localTimestamp == $localByNormalUTC)
			{
				$result['timezoneOffset'] = $tempResult['normalTimezoneOffset'];
				
				$result['type'] = 'normal';
				
				$result['timestamp'] = $result['localTimestamp'] - $result['timezoneOffset'];
				
			}
			else if ($localTimestamp == $localByDaylightSavingTimeUTC)
			{
				$result['timezoneOffset'] = $tempResult['daylightSavingTimeTimezoneOffset'];
				
				$result['type'] = 'daylightSavingTime';
				
				$result['timestamp'] = $result['localTimestamp'] - $result['timezoneOffset'];
			}
			else
			{
				$result['isWithinSkippedHour'] = true;
				
				$result['type'] = 'neither';
			}
			
			
		return $result;
	}
	
	public static function getExtendedTimezoneInformationForGeoPosition ($latitude = 0, $longitude = 0, $timestamp = false, $languageCode = 'en')
	{
		if ($timestamp === false)
		{
			$timestamp = XXX_TimestampHelpers::getCurrentTimestamp();
		}
		
		$result = false;
		
		$timezoneInformation = self::getTimezoneInformationForGeoPosition($latitude, $longitude, $timestamp);
		
		// 120 days before	
		$timezoneInformation2 = self::getTimezoneInformationForGeoPosition($latitude, $longitude, $timestamp + 10368000);
		
		// 120 days after
		$timezoneInformation3 = self::getTimezoneInformationForGeoPosition($latitude, $longitude, $timestamp - 10368000);
		
		if ($timezoneInformation !== false)
		{
			$a = false; 
			$b = false;
			
			if ($timezoneInformation['daylightSavingTimeTimezoneOffset'] > $timezoneInformation2['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation2;
				$b = $timezoneInformation;
			}
			else if ($timezoneInformation['daylightSavingTimeTimezoneOffset'] > $timezoneInformation3['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation3;
				$b = $timezoneInformation;
			}
			else if ($timezoneInformation2['daylightSavingTimeTimezoneOffset'] > $timezoneInformation['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation;
				$b = $timezoneInformation2;
			}
			else if ($timezoneInformation2['daylightSavingTimeTimezoneOffset'] > $timezoneInformation3['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation3;
				$b = $timezoneInformation2;
			}
			else if ($timezoneInformation3['daylightSavingTimeTimezoneOffset'] > $timezoneInformation['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation;
				$b = $timezoneInformation3;
			}
			else if ($timezoneInformation3['daylightSavingTimeTimezoneOffset'] > $timezoneInformation2['daylightSavingTimeTimezoneOffset'])
			{
				$a = $timezoneInformation2;
				$b = $timezoneInformation3;
			}
			// No difference
			else
			{
				$a = $timezoneInformation;
				$b = $timezoneInformation;
			}
			
			$result = array
			(
				'normalTimezoneOffset' => $a['normalTimezoneOffset'],
				'daylightSavingTimeTimezoneOffset' => $b['daylightSavingTimeTimezoneOffset'],
				'timezoneOffset' => $timezoneInformation['normalTimezoneOffset'],
				
				
				'normalTimezoneCity' => $a['timezoneCity'],
				'normalTimezoneName' => $a['timezoneName'],
				
				'daylightSavingTimeTimezoneCity' => $b['timezoneCity'],
				'daylightSavingTimeTimezoneName' => $b['timezoneName'],
				
				'timezoneCity' => $timezoneInformation['timezoneCity'],
				'timezoneName' => $timezoneInformation['timezoneName'],
				
				'latitude' => $timezoneInformation['latitude'],
				'longitude' => $timezoneInformation['longitude'],
				'timestamp' => $timezoneInformation['timestamp'],
				'languageCode' => $timezoneInformation['languageCode']
			);			
		}
		
		return $result;
	}
	
	public static function getTimezoneInformationForGeoPosition ($latitude = 0, $longitude = 0, $timestamp = false, $languageCode = 'en')
	{
		$result = false;
		
		self::$error = false;
		
		if ($timestamp === false)
		{
			$timestamp = XXX_TimestampHelpers::getCurrentTimestamp();
		}
		
		// https://maps.googleapis.com/maps/api/timezone/json?location=39.6034810,-119.6822510&timestamp=1331161200&sensor=true_or_false
		
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
		
		$path = '/maps/api/timezone/json';
		$path .= '?';
		$path .= 'location=' . urlencode($latitude . ',' . $longitude);
		$path .= '&timestamp=' . urlencode($timestamp);
		$path .= '&sensor=false';
		
		if ($languageCode != '')
		{
			$path .= '&language=' . $languageCode;
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
				'latitude' => $latitude,
				'longitude' => $longitude,
				'timestamp' => $timestamp,
				'languageCode' => $languageCode
			);
			
			$result = self::parseTimezoneResponse($response, $extraInformation);
		}
		else
		{
			self::$error = self::determineError($response['status']);
		}
		
		return $result;
	}
	
	public static function determineError ($status = '')
	{
		$result = false;
		
		switch ($status)
		{
			case 'INVALID_REQUEST':
				// indicates that the request was malformed.
				$result = 'invalidRequest';
				break;
			case 'OVER_QUERY_LIMIT':
				// indicates the requestor has exceeded quota.
				$result = 'overQueryLimit';
				break;
			case 'REQUEST_DENIED':
				// indicates that the the API did not complete the request. Confirm that the request was sent over http instead of https.
				$result = 'requestDenied';
				break;
			case 'UNKNOWN_ERROR':
				// indicates an unknown error.
				$result = 'unknownError';
				break;
			case 'ZERO_RESULTS':
				// indicates that no time zone data could be found for the specified position or time. Confirm that the request is for a location on land, and not over water.
				$result = 'noResults';
				break;
		}
		
		return $result;
	}
	
	public static function parseTimezoneResponse ($response, $extraInformation = array())
	{
		$result = false;
		
		if ($response['timeZoneName'] != '')
		{
   			$result = array();
   			
   			if (XXX_Type::isArray($extraInformation))
   			{
   				$result = XXX_Array::merge($result, $extraInformation);
   			}
   			
   			$result['normalTimezoneOffset'] = XXX_Type::makeInteger($response['rawOffset']);
   			$result['daylightSavingTimeTimezoneOffset'] = $result['normalTimezoneOffset'] + XXX_Type::makeInteger($response['dstOffset']);
   			$result['timezoneOffset'] = $result['daylightSavingTimeTimezoneOffset'];
   			$result['timezoneCity'] = $response['timeZoneId'];
   			$result['timezoneName'] = $response['timeZoneName'];
   			$result['data'] = $response;
		}
		
		return $result;
	}
	
}

?>