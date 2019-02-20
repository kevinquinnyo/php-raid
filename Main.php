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

$RAIDTypes = array(0, 1, 5, 6, 10, 'SHR');
$drives = [
	new Drive('1TB', 'hdd', 1),
	new Drive('1TB', 'hdd', 2),
	new Drive('250GB', 'hdd', 3),
	new Drive('250GB', 'hdd', 4),
	new Drive('160GB', 'hdd', 5),
	new Drive('160GB', 'hdd', 6),
];
$factory = new RaidFactory();

$capacity = '1TB';
foreach ($RAIDTypes as $RAIDType) {
	$raid[$RAIDType] = $factory->create($RAIDType, $drives);
	echo "Number of drives of " . $capacity . " : " . $raid[$RAIDType]->getNumberOfDrivesOfThisCapacity($capacity) . "\n";
	echo "RAID level : " . $raid[$RAIDType]->getLevel() . "\n";
	echo $raid[$RAIDType]->getLevel() . " RAID total capacity : " . toHumanReadableSize($raid[$RAIDType]->getTotalCapacity(), 2) . "\n";
	echo $raid[$RAIDType]->getLevel() . " RAID usable space : " . toHumanReadableSize($raid[$RAIDType]->getCapacity(), 2) . "\n";
	echo $raid[$RAIDType]->getLevel() . " RAID lossed space : " . toHumanReadableSize($raid[$RAIDType]->getLossedSpace(), 2) . "\n";
	echo $raid[$RAIDType]->getLevel() . " RAID parity size : " . toHumanReadableSize($raid[$RAIDType]->getParitySize(), 2) . "\n";
}

//var_dump($raidSHR);
