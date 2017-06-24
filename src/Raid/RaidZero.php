<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidZero extends AbstractRaid
{
    protected $drives = [];
    protected $minimumDrives = 2;

    public function __construct($drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->drives = $drives;
    }

    public function getCapacity()
    {
        $total = $this->getTotalCapacity();
        $count = $this->getDriveCount(false);

        return $total;
    }
}
