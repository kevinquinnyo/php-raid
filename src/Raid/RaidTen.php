<?php
namespace kevinquinnyo\Raid\Raid;

use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidTen extends AbstractRaid
{
    const LEVEL = 10;
    protected $drives = [];
    protected $hotSpares = [];
    protected $minimumDrives = 4;
    protected $mirrored = true;

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
        $result = $this->getTotalCapacity() / 2;
        if ($options['human'] === true) {
            return Number::toReadableSize($result);
        }   

        return $result;
    }
}
