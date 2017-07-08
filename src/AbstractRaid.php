<?php
namespace kevinquinnyo\Raid;

use Cake\I18n\Number;
use kevinquinnyo\Raid\Drive;
use RuntimeException;

abstract class AbstractRaid
{
    protected $drives = [];
    protected $hotSpares = [];
    protected $mirrored = false;
    protected $parity = false;
    protected $striped = false;
    protected $minimumDrives = 1;

    public function isMirrored()
    {
        return $this->mirrored;
    }

    public function getMinimumDrives()
    {
        return $this->minimumDrives;
    }

    public function isStriped()
    {
        return $this->striped;
    }

    public function hasMinimumDriveCount()
    {
        return $this->getDriveCount(false) >= $this->minimumDrives;
    }

    public function isUnevenMirror()
    {
        if ($this->mirrored === false) {
            return false;
        }

        $isOdd = ($this->getDriveCount(false) % 2) === 1;

        return $isOdd;
    }

    public function validDriveCount()
    {
        if ($this->hasMinimumDriveCount() === false || $this->isUnevenMirror() === true) {
            return false;
        }

        return true;
    }

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

    abstract public function getCapacity($options = []);

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

    public function getTotalDriveCount()
    {
        return count($this->getDrives()) + count($this->getHotSpares());
    }

    /* @deprecated - there's no point when you can just do (count)$raid->getDrives() and (count)$raid->getHostSpares() */
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

    public function getTotalCapacity($options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
        ];
        $total = 0;
        $min = $this->getMinimumDriveSize();

        foreach ($this->drives as $drive) {
            if ($options['withHotSpares'] === false && $drive->isHotSpare() === false) {
                $total += $min;
            }
        }

        if ($options['human'] === true) {
            $total = Number::toReadableSize($total);
        }

        return $total;
    }

    public function validate($drives)
    {
        $identifiers = [];
        foreach ($drives as $drive) {
            if (($drive instanceof Drive) === false) {
                throw new RuntimeException(sprintf('Drive must be an instance of %s', Drive::class));
            }

            $identifier = $drive->getIdentifier();

            if (in_array($identifier, $identifiers)) {
                throw new RuntimeException(sprintf('Drive identifier %s is already present.  Drive Identifiers must be unique in a RAID.', $identifier));
            }
            $identifiers[] = $identifier;
        }

        return true;
    }
}
