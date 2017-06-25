<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidZero extends AbstractRaid
{
    const LEVEL = 0;
    protected $drives = [];
    protected $minimumDrives = 2;
    protected $mirrored = false;
    protected $striped = true;

    public function __construct($drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->drives = $drives;
    }

    public function getCapacity()
    {
        return $this->getTotalCapacity();
    }
}
