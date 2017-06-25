<?php
namespace kevinquinnyo\Raid;

use kevinquinnyo\Raid\Drive;
use RuntimeException;

abstract class AbstractRaid
{
    protected $drives = [];
    protected $hotSpares = [];

    public function getLevel()
    {
        return static::LEVEL;
    }

    public function getDrives()
    {
        return $this->drives;
    }

    public function getHotSpares()
    {
        return $this->hotSpares;
    }

    public function setDrives($drives)
    {
        $this->validate($drives);
        $this->drives = $drives;

        return $this;
    }

    public function addDrive($drive)
    {
        $drives = $this->drives;
        $drives[] = $drive;

        return $this->setDrives($drives);
    }

    public function addHotSpare(Drive $hotSpare)
    {
        $hotSpares = $this->hotSpares;
        $hotSpares[] = $hotSpare;

        $this->validate($hotSpares);

        return $this;
    }

    abstract public function getCapacity();

    public function getMinimumDriveSize()
    {
        $floor = null;

        foreach ($this->drives as $drive) {
            if ($drive->isHotSpare() === true) {
                continue;
            }
            if (isset($floor) === false) {
                $floor = $drive->getCapacity();
            }

            if ($drive->getCapacity() < $floor) {
                $floor = $drive->capacity;
            }
        }

        return $floor;
    }

    public function getDriveCount($withHotSpares = true)
    {
        $total = 0;

        foreach ($this->drives as $drive) {
            if ($withHotSpares === false && $drive->isHotSpare() === false) {
                $total += 1;
            }
        }

        return $total;
    }

    public function getTotalCapacity($withHotSpares = false)
    {
        $total = 0;
        $min = $this->getMinimumDriveSize();

        foreach ($this->drives as $drive) {
            if ($withHotSpares === false && $drive->isHotSpare() === false) {
                $total += $min;
            }
        }

        return $total;
    }

    public function validate($drives)
    {
        foreach ($drives as $drive) {
            if (($drive instanceof Drive) === false) {
                throw new RuntimeException(sprintf('Drive must be an instance of %s', Drive::class));
            }
        }

        return true;
    }
}
