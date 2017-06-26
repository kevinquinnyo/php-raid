<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidFive extends AbstractRaid
{
    const LEVEL = 5;
    protected $drives = [];
    protected $hotSpares = [];
    protected $minimumDrives = 3;
    protected $mirrored = false;
    protected $parity = true;
    protected $striped = true;

    public function __construct($drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->drives = $drives;
    }

    public function getCapacity($options = [])
    {
        $options = [
            'human' => false,
        ];
        $total = $this->getTotalCapacity();
        $min = $this->getMinimumDriveSize();
        $result = $total === 0 ? $total : ($total - $min);
        
        if ($options['human'] === true) {
            return Number::toReadableSize($result);
        }   

        return $result;
    }
}
