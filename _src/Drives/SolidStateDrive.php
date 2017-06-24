<?php
namespace kevinquinnyo\Raid;

use kevinquinnyo\Drive;

class SolidStateDrive extends Drive implements DriveInterface
{
    protected $capacity = null;

    public function __construct(int $capacity)
    {
        $this->capacity = $capacity;
    }

    public function getCapacity()
    {
        return $this->capacity;
    }
}
