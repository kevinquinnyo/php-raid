<?php
namespace kevinquinnyo\Raid;

use kevinquinnyo\Raid\Raid\RaidZero;
use kevinquinnyo\Raid\Raid\RaidOne;
use kevinquinnyo\Raid\Raid\RaidFive;
use kevinquinnyo\Raid\Raid\RaidSix;
use kevinquinnyo\Raid\Raid\RaidTen;
use InvalidArgumentException;

class RaidFactory
{
    public function create(int $level, $drives)
    {
        $drives = (array)$drives;

        switch ($level) {
            case 0:
                return new RaidZero($drives);
                break;
            case 1:
                return new RaidOne($drives);
                break;
            case 5:
                return new RaidFive($drives);
                break;
            case 6:
                return new RaidSix($drives);
                break;
            case 10:
                return new RaidTen($drives);
                break;
        }

        throw new InvalidArgumentException('Unsupported RAID level supported. (Supported levels: 0, 1, 5, 6, 10)');
    }
}
