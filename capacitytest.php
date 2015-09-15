<?php

ini_set("max_execution_time", "100000");
set_time_limit(100000);
ini_set('memory_limit', '16138M');
require_once('app/Mage.php');
umask(0);
Mage::app();
$startTime = time();
echo "Started. Please don't close the browser window until a 'Done. Time elapsed:...' text appears.";
echo "<br /><br />";
$successes = 0;
$errors = 0;

$shipment = Mage::getModel('capacity/shipment');
//$shipment->setLocalDebug(true);
//$shipment->setPathToFile('var/log/sc.txt');
$shipment->handleShipmentResponse();
echo "<br />"; 

$finishTime = time();
$timeElapsed = timeElapsed($finishTime - $startTime);

echo "Done. <br />Time elapsed: " . $timeElapsed;
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
