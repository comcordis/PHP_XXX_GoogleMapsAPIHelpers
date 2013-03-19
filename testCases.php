<?php

echo '<pre>';

$test = XXX_CurrencyImporter::import();

print_r($test);

/*
$test = XXX_GoogleMapsAPIHelpers::lookupAddress('Meusertstraat 219, Kerkrade, Holland');

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::lookupAddress('Flughafen dusseldorf, dusseldorf, deutschland');

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::reverseLookupCoordinate(50.8842594, 6.0751393);

print_r($test);
																					// 1364626800
																					// 1364713200
																					// 1364695199
$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, 1382835599);

print_r($test);


																			// 1364695199
$test = XXX_GoogleMapsAPIHelpers::getDistanceAndDurationInformationForAddressStrings('Meuserstraat 219, Kerkrade, Nederland', 'Loorderstraat 1, Grathem, Holland');

print_r($test);


// 1364695199
																
																
$test = XXX_GoogleMapsAPIHelpers::getDistanceAndDurationInformationForCoordinates(50.8842594, 6.0751393, 51.2846292, 6.778646);

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, 1382835599);

print_r($test);
*/

/*

- normalTimezoneOffset (Altijd opvraagbaar op basis van locatie en fake timestamp)

- daylightSavingTimeTimezoneOffset ()


*/

// Meuserstraat 219, Kerkrade, Nederland: 50.8842594, 6.0751393
// UTC+1 with DST in Summer
	
	// DST is like moving a timezone! Not skipping/doubling time.
	
	// Sun, 31 Mar 2013, 02:00:00 clocks are turned forward 1 hour to
	// Sun, 31 Mar 2013, 03:00:00 local daylight time instead
	// One hour doesn't exist in local time (but in utc it does)

	// Last second without DST
	$a = 1364691599;
		// UTC: Sun, 31 Mar 2013 00:59:59
		// UTC+1: Sun, 31 Mar 2013 01:59:59
		// Google response: UTC+3600
	
	// First second with DST 
	$b = 1364691600;
		// UTC: Sun, 31 Mar 2013 01:00:00
		// UTC+1 + 1h DST (UTC+2): Sun, 31 Mar, 2013 03:00:00
		// Google response: UTC+7200

	// Sun, 27 Oct 2013, 03:00:00 clocks are turned backward 1 hour to
	// Sun, 27 Oct 2013, 02:00:00 local standard time instead
	// One hour exists double in local time (but in UTC it doesn't, just once)

	// Last second with DST
	$c = 1382835599;
		// UTC: Sun, 27 Oct 2013 00:59:59
		// UTC+1 + 1h DST (UTC+2): Sun, 27 Oct, 2013 02:59:59
		// Google response: UTC+7200
		
	// First second without DST
	$d = 1382835600;
		// UTC: Sun, 27 Oct 2013 01:00:00
		// UTC+1: Sun, 27 Oct, 2013 02:00:00
		// Google response: UTC+3600

/*

Google timezone API:

Input:
	- location coordinates
	- timestamp UTC
	
Output:
	- timezone offset for the timestamp
	- dst offset for the timestamp
	- timezone city
	- timezone name
	
Problems:
	- Client enters time for airport? Airport timezone is known? DST depends on date?

*/
$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, $a);

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, $b);

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, $c);

print_r($test);

$test = XXX_GoogleMapsAPIHelpers::getTimezoneInformationForLocation(50.8842594, 6.0751393, $d);

print_r($test);

echo '</pre>';

?>