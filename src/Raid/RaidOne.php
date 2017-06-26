<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidOne extends AbstractRaid
{
    const LEVEL = 1;
    protected $drives = [];
    protected $hotSpares = [];
    protected $minimumDrives = 2;
    protected $mirrored = true;
    protected $striped = false;

    public function __construct($drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->drives = $drives;
    }

    public function getCapacity($human = false)
    {
        if ($human === true) {
            return Number::toReadableSize($this->getMinimumDriveSize());
        }

        return $this->getMinimumDriveSize();
    }
}
