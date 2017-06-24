<?php
namespace kevinquinnyo\Raid;

use kevinquinnyo\Drives\HardDrive;
use kevinquinnyo\Drives\SolidStateDrive;
use InvalidArgumentException;

class DriveFactory
{
    protected $types = [
        'hdd' => [
            'className' => 'HardDrive',
        ],
        'ssd' => [
            'className' =>'SolidStateDrive',
        ],
    ];

    public static function create(int $capacity, string $type)
    {
        $this->validate($capacity, $type);
        $className = __NAMESPACE__ . '\\Drives\\' . $this->types[$type]['className'];

        return new $className($capacity); 
    }

    protected function validate($capacity, $type)
    {
        if (isset($types[$type]) === false) {
            throw new InvalidArgumentException('Drive type not supported.');
        }

        if (is_numeric($capacity) === false) {
            throw new InvalidArgumentException('Capacity must be an integer (for now).');
        }
    }
}
