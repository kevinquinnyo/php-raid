<?php
namespace kevinquinnyo\Raid;

use InvalidArgumentException;
use kevinquinnyo\Raid\Raid\RaidFive;
use kevinquinnyo\Raid\Raid\RaidOne;
use kevinquinnyo\Raid\Raid\RaidSix;
use kevinquinnyo\Raid\Raid\RaidTen;
use kevinquinnyo\Raid\Raid\RaidZero;

class RaidFactory
{
    /**
     * Create
     *
     * @param int $level The RAID level to create.
     * @param mixed $drives A single \kevinquinnyo\Raid\Drive or array of Drive objects.
     * @throws \InvalidArgumentException If the level provided is not supported by this library.
     * @return \kevinquinnyo\Raid\AbstractRaid Initialized Raid object.
     */
    public function create(int $level, $drives)
    {
        $drives = (array)$drives;
        $raid = null;

        switch ($level) {
            case 0:
                $raid = new RaidZero($drives);
                break;
            case 1:
                $raid = new RaidOne($drives);
                break;
            case 5:
                $raid = new RaidFive($drives);
                break;
            case 6:
                $raid = new RaidSix($drives);
                break;
            case 10:
                $raid = new RaidTen($drives);
                break;
        }

        if ($raid === null) {
            throw new InvalidArgumentException('Unsupported RAID level provided. (Supported levels: 0, 1, 5, 6, 10)');
        }

        return $raid;
    }
}
