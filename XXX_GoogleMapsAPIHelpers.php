<?php

class XXX_GoogleMapsAPIHelpers
{
	public static function doGETRequest ($uri = '', $parseJSONToArray = true)
	{
		$result = false;
		
		$response = XXX_HTTP_Request::execute($uri);
		
		if ($response != false)
		{
			if ($parseJSONToArray)
			{
				$response = XXX_String_JSON::decode($response);
				
				if ($response != false)
				{
					$response = XXX_Object::convertToArray($response);
					
					if ($response != false)
					{
						$result = $response;
					}
				}
			}
			else
			{
				$result = $response;
			}
		}
		
		return $result;
	}
}

?>