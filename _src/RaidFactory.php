<?php
namespace kevinquinnyo\Raid;

use InvalidArgumentException;
use kevinquinnyo\Raid\Levels\RaidZero;
use kevinquinnyo\Raid\Levels\RaidOne;
use kevinquinnyo\Raid\Levels\RaidFive;
use kevinquinnyo\Raid\Levels\RaidSix;
use kevinquinnyo\Raid\Levels\RaidTen;

class RaidFactory
{
    protected $levels = [
        0 => [
            'minimumDrives' => 2,
            'className' => 'RaidZero',
        ],
        1=> [
            'minimumDrives' => 2,
            'className' => 'RaidOne',
        ],
        5 => [
            'minimumDrives' => 3,
            'className' => 'RaidFive',
        ],
        6 => [
            'minimumDrives' => 4,
            'className' => 'RaidSix',
        ],
        10 => [
            'minimumDrives' => 4,
            'className' => 'RaidTen',
        ],
    ];

    public static function create(int $level, int $drives)
    {
        $this->validate($level, $drives);
        $className = __NAMESPACE__ . '\\Levels\\' . $this->levels[$level]['className'];

        return new $className($level, $drives);
    }

    protected function validate($level, $drives)
    {
        if (isset($levels[$level]) === false) {
            throw new InvalidArgumentException('The provided RAID level is not supported or invalid.');
        }

        $minimumDrives = $this->levels[$level];
        if($minimumDrives > $drives) {
            throw new InvalidArgumentException(sprintf('The minimum required drives for a RAID {0} is {1}', $level, $minimumDrives));
        }
    }
}
