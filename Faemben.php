<?php
error_reporting(0);


function die_usage($str = null){
    if (isset($str)){
        echo $str . PHP_EOL;
    }
    
    echo "Usage : php -f Faemben.php filename.csv > results.csv OR php -f Faemben.php filename.csv --verbose" . PHP_EOL;
    exit(1);
}

function verbose($str){
    if ($GLOBALS["isVerbose"]){
        echo $str . PHP_EOL;
    }
}


function csv_line($str){
    if (!$GLOBALS["isVerbose"]){
        echo $str . PHP_EOL;
    }
}

if ($argc < 2 && $argc > 4){
    die_usage();
}

$filePtr = file($argv[1]);
$GLOBALS["isVerbose"] = $argv[2] == "--verbose" ? true : false;

if (!$filePtr){
    die_usage("Error : file not found.");
}

$csv = array_map('str_getcsv', $filePtr);
$total = 0;
$success = 0;

foreach($csv as $key => $row){    
    
    $address = trim($row[0]);
    $address = str_replace(';', ' ', $address);
    $address = preg_replace('!\s+!', ' ', $address);
    
    verbose("Looking for : $address");
    
    $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');
    $geo = json_decode($geo, true);
    
    if ($geo['status'] = 'OK') {
        
        $success++;
        verbose("Found !");   
  
        $latitude = $geo['results'][0]['geometry']['location']['lat'];
        $longitude = $geo['results'][0]['geometry']['location']['lng'];
        
        verbose("Longitude : $longitude");
        verbose("Latitude : $latitude");
        
        csv_line("$latitude;$longitude");
        
      } else {
        verbose("Not found !");
        csv_line(";");
      }
      
      $total++;
      sleep(1);
}

verbose("Result : $success / $total founded.");