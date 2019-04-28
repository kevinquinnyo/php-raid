<?php
require __DIR__ . '/vendor/autoload.php';

use kevinquinnyo\Raid\RaidFactory;
use kevinquinnyo\Raid\Drive;

function toHumanReadableSize($size, $precision = 1){
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $step = 1024;
    $i = 0;
    while (($size / $step) > 0.9) {
        $size /= $step;
        $i++;
    }
    return number_format(round($size, $precision), $precision, ',', '.') . " " . $units[$i];
}

$RAIDTypes = array(0, 1, 5, 6, 10, 'SHR', 'SHR2');
$drives = [
    new Drive('1TB', 'hdd', 1),
    new Drive('1TB', 'hdd', 2),
    new Drive('250GB', 'hdd', 3),
    new Drive('250GB', 'hdd', 4),
    new Drive('160GB', 'hdd', 5),
    new Drive('160GB', 'hdd', 6),
];
$factory = new RaidFactory();
$numberOfDecimalsToPrint = 2;

foreach ($RAIDTypes as $RAIDType) {
    $raid[$RAIDType] = $factory->create($RAIDType, $drives);
    echo "RAID level : " . $raid[$RAIDType]->getLevel() . "\n\n";
    echo "RAID " . $raid[$RAIDType]->getLevel() . " total capacity : " . toHumanReadableSize($raid[$RAIDType]->getTotalCapacity(), $numberOfDecimalsToPrint) . "\n";
    echo "RAID " . $raid[$RAIDType]->getLevel() . " usable space : " . toHumanReadableSize($raid[$RAIDType]->getCapacity(), $numberOfDecimalsToPrint) . "\n";
    echo "RAID " . $raid[$RAIDType]->getLevel() . " parity size : " . toHumanReadableSize($raid[$RAIDType]->getParitySize(), $numberOfDecimalsToPrint) . "\n";
    echo "RAID " . $raid[$RAIDType]->getLevel() . " lossed space : " . toHumanReadableSize($raid[$RAIDType]->getLossedSpace(), $numberOfDecimalsToPrint) . "\n";
    echo "RAID " . $raid[$RAIDType]->getLevel() . " data protected : ";
    if ($raid[$RAIDType]->getDrivesFailureSupported() === 0) {
        echo "no";
    } else if ($raid[$RAIDType]->getDrivesFailureSupported() === 1) {
        echo "yes, once";
    } else if ($raid[$RAIDType]->getDrivesFailureSupported() === 2) {
        echo "yes, twice";
    } else {
        echo "yes, " . $raid[$RAIDType]->getDrivesFailureSupported() . " times";
    }
    echo "\n\n";
}
