<?php

class XXX_GoogleMapsAPI_DirectionsService
{
	// Free
	public static $serverKey = '';
	
	// Business
	public static $client_ID = '';
	public static $cryptoKey = '';
	
	public static $httpsOnly = false;
	
	public static $authenticationType = 'free';
	
	public static $error = false;
	
	public static function getRideInformationForAddressStrings ($fromRawAddressString = '', $toRawAddressString = '', $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		self::$error = false;
		
		// http://maps.googleapis.com/maps/api/directions/json?origin=Boston,MA&destination=Concord,MA&waypoints=A|B&sensor=false
		
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
		
		$path = '/maps/api/directions/json';
		$path .= '?';
		$path .= 'origin=' . urlencode($fromRawAddressString);
		$path .= '&destination=' . urlencode($toRawAddressString);
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
			
			$result = self::parseDirectionsResponse($response, $extraInformation);
		}
		else
		{
			self::$error = self::determineError($response['status']);
		}
		
		return $result;
	}
		
	public static function getRideInformationForGeoPositions ($fromLatitude = 0, $fromLongitude = 0, $toLatitude = 0, $toLongitude = 0, $languageCode = 'en', $locationBias = '')
	{
		$result = false;
		
		self::$error = false;
		
		// http://maps.googleapis.com/maps/api/directions/json?origin=41.43206,-81.38992&destination=41.43206,-81.38992&waypoints=A|B&sensor=false
		
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
		
		$path = '/maps/api/directions/json';
		$path .= '?';
		$path .= 'origin=' . urlencode($fromLatitude . ',' . $fromLongitude);
		$path .= '&destination=' . urlencode($toLatitude . ',' . $toLongitude);
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
				'fromLatitde' => $fromLatitude,
				'fromLongitude' => $fromLongitude,
				'toLatitude' => $toLatitude,
				'toLongitude' => $toLongitude,
				'languageCode' => $languageCode,
				'locationBias' => $locationBias
			);
			
			$result = self::parseDirectionsResponse($response, $extraInformation);
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
				// indicates that the provided request was invalid. Common causes of this status include an invalid parameter or parameter value.
				$result = 'invalidRequest';
				break;
			case 'NOT_FOUND':
				// indicates at least one of the locations specified in the requests's origin, destination, or waypoints could not be geocoded.
				$result = 'notFound';
				break;
			case 'MAX_WAYPOINTS_EXCEEDED':
				// indicates that too many waypointss were provided in the request The maximum allowed waypoints is 8, plus the origin, and destination. ( Google Maps API for Business customers may contain requests with up to 23 waypoints.)
				$result = 'maximumWaypointsExceeded';
				break;
			case 'OVER_QUERY_LIMIT':
				// indicates the service has received too many requests from your application within the allowed time period.
				$result = 'overQueryLimit';
				break;
			case 'REQUEST_DENIED':
				// indicates that the service denied use of the directions service by your application.
				$result = 'requestDenied';
				break;
			case 'UNKNOWN_ERROR':
				// indicates a directions request could not be processed due to a server error. The request may succeed if you try again.
				$result = 'unknownError';
				break;
			case 'ZERO_RESULTS':
				// indicates no route could be found between the origin and destination.
				$result = 'noResults';
				break;
		}
		
		return $result;
	}
	
	public static function parseDirectionsResponse ($response, $extraInformation = array())
	{
		$result = false;
		
		if (XXX_Array::getFirstLevelItemTotal($response['routes']))
		{
   			$result = array();
   			
   			if (XXX_Type::isArray($extraInformation))
   			{
   				$result = XXX_Array::merge($result, $extraInformation);
   			}
   			
   			$rideParts = array();
   			
   			for ($i = 0, $iEnd = XXX_Array::getFirstLevelItemTotal($response['routes'][0]['legs']); $i < $iEnd; ++$i)
   			{
   				for ($j = 0, $jEnd = XXX_Array::getFirstLevelItemTotal($response['routes'][0]['legs'][$i]['steps']); $j < $jEnd; ++$j)
   				{
   					$distance = $response['routes'][0]['legs'][$i]['steps'][$j]['distance']['value'];
   					$duration = $response['routes'][0]['legs'][$i]['steps'][$j]['duration']['value'];
   					
   					$averageSpeed = (($distance / $duration) * 3600) / 1000;
   					
   					$description = $response['routes'][0]['legs'][$i]['steps'][$j]['html_instructions'];
   					
   					$fromLatitude = $response['routes'][0]['legs'][$i]['steps'][$j]['start_location']['lat'];
   					$fromLongitude = $response['routes'][0]['legs'][$i]['steps'][$j]['start_location']['lng'];
   					   					
   					$toLatitude = $response['routes'][0]['legs'][$i]['steps'][$j]['end_location']['lat'];
   					$toLongitude = $response['routes'][0]['legs'][$i]['steps'][$j]['end_location']['lng'];
   					
   					$polyline = $response['routes'][0]['legs'][$i]['steps'][$j]['polyline']['points'];
					
					$rideParts[] = array
					(
						'distance' => $distance,
						'duration' => $duration,
						'averageSpeed' => $averageSpeed,
						'description' => $description,
						'fromLatitude' => $fromLatitude,
						'fromLongitude' => $fromLongitude,
						'toLatitude' => $toLatitude,
						'toLongitude' => $toLongitude,
						'polyline' => $polyline
					);
   				}
   			}
   			
   			$result['rideParts'] = $rideParts;
   			
   			$result['copyrights'] = $response['routes'][0]['copyrights'];
   			
   			$result['bounds'] = array
   			(
   				'topLeft' => array
   				(
   					'latitude' => $response['routes'][0]['bounds']['northeast']['lat'],
   					'longitude' => $response['routes'][0]['bounds']['northeast']['lng']
   				),
   				'bottomRight' => array
   				(
   					'latitude' => $response['routes'][0]['bounds']['southwest']['lat'],
   					'longitude' => $response['routes'][0]['bounds']['southwest']['lng']
   				)
   			);
   			   			
   			$result['distance'] = $response['routes'][0]['legs'][0]['distance']['value'];
   			$result['duration'] = $response['routes'][0]['legs'][0]['duration']['value'];
   			$result['durationInTraffic'] = $response['routes'][0]['legs'][0]['duration_in_traffic']['value'];
   			$result['averageSpeed'] = (($result['distance'] / $result['duration']) * 3600) / 1000;
   			$result['fromFormattedAddressString'] = $response['routes'][0]['legs'][0]['start_address'];
   			$result['toFormattedAddressString'] = $response['routes'][0]['legs'][0]['end_address'];
   			
   			
   			$result['fromLatitude'] = $response['routes'][0]['legs'][0]['start_location']['lat'];
   			$result['fromLongitude'] = $response['routes'][0]['legs'][0]['start_location']['lng'];
   			
   			$result['toLatitude'] = $response['routes'][0]['legs'][0]['end_location']['lat'];
   			$result['toLongitude'] = $response['routes'][0]['legs'][0]['end_location']['lng'];
   			
   			$result['polyline'] = $response['routes'][0]['overview_polyline']['points'];
		}
		
		return $result;
	}
}

?>