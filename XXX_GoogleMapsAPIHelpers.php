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
	
	// http://gmaps-samples.googlecode.com/svn/trunk/urlsigning/UrlSigner.php-source
	
	// Encode a string to URL-safe base64
	public static function encodeBase64URISafe ($value)
	{
	  return str_replace(array('+', '/'), array('-', '_'), base64_encode($value));
	}
	
	// Decode a string from URL-safe base64
	public static function decodeBase64URISafe ($value)
	{
	  return base64_decode(str_replace(array('-', '_'), array('+', '/'), $value));
	}
	
	// Sign a URL with a given crypto key
	// Note that this URL must be properly URL-encoded
	public static function addSignatureToURI ($uriToSign, $privateKey)
	{
	  // parse the url
	  $url = parse_url($uriToSign);
	
	  $urlPartToSign = $url['path'] . '?' . $url['query'];
	
	  // Decode the private key into its binary format
	  $decodedKey = self::decodeBase64URISafe($privateKey);
	
	  // Create a signature using the private key and the URL-encoded string using HMAC SHA1. This signature will be binary.
	  $signature = hash_hmac('sha1', $urlPartToSign, $decodedKey, true);
	
	  $encodedSignature = self::encodeBase64URISafe($signature);
	
	  return $uriToSign . '&signature=' . $encodedSignature;
	}
	
	// XXX_GoogleMapsAPIHelpers::addSignatureToURI("http://maps.google.com/maps/api/geocode/json?address=New+York&sensor=false&client=clientID", 'vNIXE0xscrmjlyV-12Nj_BvUPaw=');

}

?>