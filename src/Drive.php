<?php
namespace kevinquinnyo\Raid;

use InvalidArgumentException;

class Drive
{
    protected $capacity = null;
    protected $type = null;
    protected $hotSpare = false;

    public function __construct(int $capacity, string $type, $hotSpare = false)
    {
        $this->capacity = $capacity;
        $this->validate($type);
        $this->type = $type;
        $this->hotSpare = $hotSpare;
    }

    protected function validate($type)
    {
        $types = ['ssd', 'hdd'];

        if (in_array($type, $types) === false) {
            throw new InvalidArgumentException('This is not a valid drive type.');
        }
    }

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setHotSpare($set = true)
    {
        $this->hotSpare = (bool)$set;
    }

    public function isHotSpare()
    {
        return $this->hotSpare;
    }
}
