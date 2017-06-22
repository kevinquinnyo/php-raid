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
            'minimumDisks' => 2,
            'className' => 'RaidZero',
        ],
        1=> [
            'minimumDisks' => 2,
            'className' => 'RaidOne',
        ],
        5 => [
            'minimumDisks' => 3,
            'className' => 'RaidFive',
        ],
        6 => [
            'minimumDisks' => 4,
            'className' => 'RaidFour',
        ],
        10 => [
            'minimumDisks' => 4,
            'className' => 'RaidTen',
        ],
    ];

    public static function create(int $level, int $disks)
    {
        $this->validate($level, $disks);

        $className = __NAMESPACE__ . '\\Levels\\' . $this->levels[$level]['className'];
    }

    protected function validate($level, $disks)
    {
        if (isset($levels[$level]) === false) {
            throw new InvalidArgumentException(sprintf('The provided RAID level is not supported or invalid.'));
        }

        $minimumDisks = $this->levels[$level];
        if($minimumDisks > $disks) {
            throw new InvalidArgumentException(sprintf('The minimum required disks for a RAID {0} is {1}', $level, $minimumDisks));
        }
    }
}
