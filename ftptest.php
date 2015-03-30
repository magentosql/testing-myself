<?php

ini_set("max_execution_time", "100000");
set_time_limit(100000);
ini_set('memory_limit', '16138M');
require_once('app/Mage.php');
umask(0);
Mage::app();
$startTime = time();
echo "Started. Please don't close this windows because the import will get interrupted.";
echo "<br /><br />";
$successes = 0;
$errors = 0;

//$filename = "/home/thelaund/var/log/sc.txt";
//$handle = fopen($filename, "r");
//$contents = fread($handle, filesize($filename));
//fclose($handle);

//echo $contents;

$shipment = Mage::getModel('capacity/shipment');
////echo get_class($shipment);
////echo "<br />"; 
//$shipment->getShipmentFile();
$shipment->handleShipmentResponse();
echo "<br />"; 

$finishTime = time();
$timeElapsed = timeElapsed($finishTime - $startTime);

echo "Done. <br />Time elapsed: " . $timeElapsed . " Products saved: " . $successes .  " Problems: " . $errors;
echo "<br />";
function timeElapsed($secs) {
    $bit = array(
        'y' => $secs / 31556926 % 12,
        'w' => $secs / 604800 % 52,
        'd' => $secs / 86400 % 7,
        'h' => $secs / 3600 % 24,
        'm' => $secs / 60 % 60,
        's' => $secs % 60
    );

    foreach ($bit as $k => $v)
        if ($v > 0)
            $ret[] = $v . $k;

    return join(' ', $ret);
}
