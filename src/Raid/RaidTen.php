<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidTen extends AbstractRaid
{
    const LEVEL = 10;
    protected $drives = [];
    protected $minimumDrives = 4;
    protected $mirrored = true;

    public function __construct($drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->drives = $drives;
    }

    public function getCapacity()
    {
        return $this->getTotalCapacity() / 2;
    }
}
