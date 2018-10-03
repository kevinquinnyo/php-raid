<?php
namespace kevinquinnyo\Raid;

use Cake\I18n\Number;
use kevinquinnyo\Raid\Drive;
use RuntimeException;

abstract class AbstractRaid
{
    protected $drives = [];
    protected $mirrored = false;
    protected $parity = false;
    protected $striped = false;
    protected $minimumDrives = 1;

    /**
     * Is Mirrored
     *
     * @return bool If the RAID is mirrored.
     */
    public function isMirrored()
    {
        return $this->mirrored;
    }

    /**
     * Get Minimum Drives
     *
     * @return int The minimum drives required for this RAID.
     */
    public function getMinimumDrives()
    {
        return $this->minimumDrives;
    }

    /**
     * Is Striped
     *
     * @return bool If the RAID is striped.
     */
    public function isStriped()
    {
        return $this->striped;
    }

    /**
     * Has Minimum Drive Count
     *
     * @return bool If the RAID has the required minimum drive count.
     */
    public function hasMinimumDriveCount()
    {
        return count($this->getDrives()) >= $this->minimumDrives;
    }

    /**
     * Is Uneven Mirror
     *
     * @return bool If the RAID is a mirrored RAID and has an uneven drive count.
     */
    public function isUnevenMirror()
    {
        if ($this->mirrored === false) {
            return false;
        }

        $isOdd = (count($this->getDrives()) % 2) === 1;

        return $isOdd;
    }

    /**
     * Valid Drive Count
     *
     * @return bool If the RAID has a valid drive count based on its level.
     */
    public function validDriveCount()
    {
        if ($this->hasMinimumDriveCount() === false || $this->isUnevenMirror() === true) {
            return false;
        }

        return true;
    }

    /**
     * Get Level
     *
     * @return int The RAID level.
     */
    public function getLevel()
    {
        return static::LEVEL;
    }

    /**
     * Get Drives
     *
     * @param $options Options - add 'withHotSpares' to include hot spare drives.
     * @return array The RAID's Drive objects.
     */
    public function getDrives($options = [])
    {
        $options += ['withHotSpares' => false];
        $drives = $this->drives;

        if ($options['withHotSpares'] === false) {
            $drives = [];
            foreach ($this->drives as $drive) {
                if ($drive->isHotSpare() === false) {
                    $drives[] = $drive;
                }
            }
        }

        return $drives;
    }

    /**
     * Get Hot Spares
     *
     * @return array The RAID's Drive objects listed as hot spares.
     */
    public function getHotSpares()
    {
        $hotSpares = [];
        $drives = $this->getDrives(['withHotSpares' => true]);

        foreach ($drives as $drive) {
            if ($drive->isHotSpare() === true) {
                $hotSpares[] = $drive;
            }
        }

        return $hotSpares;
    }

    /**
     * Set Drives
     *
     * @param array $drives An array of \kevinquinnyo\Raid\Drive Drive objects to set on the RAID.
     * @return self
     */
    public function setDrives($drives)
    {
        $this->validate($drives);
        $this->drives = $drives;

        return $this;
    }

    /**
     * Add Drive
     *
     * @param \kevinquinnyo\Raid\Drive $drive A drive to add to the RAID.
     * @return self
     */
    public function addDrive(Drive $drive)
    {
        $drives = $this->drives;
        $drives[] = $drive;

        return $this->setDrives($drives);
    }

    /**
     * Add Hot Spare
     *
     * @param \kevinquinnyo\Raid\Drive $drive A Drive to add to the list of hot spares.
     * @return self
     */
    public function addHotSpare(Drive $drive)
    {
        $drives = $this->getDrives();
        $drives[] = $drive->setHotSpare();
        $this->validate($drives);

        return $this->setDrives($drives);
    }

    /**
     * Get Capacity
     *
     * Get the usable capacity of the RAID in its current state.
     * This method differs slightly per RAID level implementation.
     *
     * @param array $options Additional options that the Raid objects methods allow.
     * @return int The usable capacity of the RAID.
     */
    abstract public function getCapacity(array $options = []);

    /**
     * Get Minimum Drive Size
     *
     * Return the size in capacity of the smallest drive in the array.
     *
     * @return int Capacity of smallest drive in array.
     */
    public function getMinimumDriveSize($options = [])
    {
        $options += ['withHotSpares' => false];
        $floor = null;
        $drives = $this->getDrives($options);

        foreach ($drives as $drive) {
            if (isset($floor) === false) {
                $floor = $drive->getCapacity();
            }

            if ($drive->getCapacity() < $floor) {
                $floor = $drive->getCapacity();
            }
        }

        return $floor;
    }

    /**
     * Get Drive Count
     *
     * @param array $options Add 'withHotSpares' if you wish to include hot spares in the count.
     * @return int The drive count for the RAID.
     */
    public function getDriveCount($options = [])
    {
        $options += ['withHotSpares' => false];
        $drives = $this->getDrives($options);

        return count($drives);
    }

    /**
     * Get Total Capacity
     *
     * Get the total capacity of the array.  This is not particularly useful as a public method
     * because each concrete Raid class has a `getCapacity` method which gives the real total
     * usable capacity based on the properties of the specific RAID level.
     *
     * It's a public method in case this value is useful. Note that by default, it uses the 'floor'
     * value of the smallest drive in the array by default.
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * - withHotSpares - Whether to include the hot spares in the sum.
     * - floor - Whether to pin the drives to the lowest capacity drive in the array when
     * creating the sum.
     * ```
     *
     * @param array $options Additional options to scope the results.
     * @return int The total capacity for the RAID.
     */
    public function getTotalCapacity($options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
            'floor' => true,
        ];
        $total = 0;
        $min = $this->getMinimumDriveSize();

        $drives = $this->getDrives($options);

        foreach ($drives as $drive) {
            $total += $options['floor'] === true ? $min : $drive->getCapacity();
        }

        if ($options['human'] === true) {
            $total = Number::toReadableSize($total);
        }

        return $total;
    }

    /**
     * Validate
     *
     * Validate the drives in the array.
     *
     * @param array $drives An array of \kevinquinnyo\Raid\Drive objects to validate.
     * @throws \RuntimeException If the drives are not compatible Drive classes or they
     * have a duplicate drive identifier.
     * @return bool If the drive objects are valid.
     */
    public function validate(array $drives)
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
