<?php
namespace kevinquinnyo\Raid;

use Cake\I18n\Number;
use Cake\Utility\Text;
use kevinquinnyo\Raid\Drive;
use RuntimeException;

abstract class AbstractRaid
{
    protected $drives = [];
    protected $mirrored = false;
    protected $parity = false;
    protected $striped = false;
    protected $minimumDrives = 1;
    protected $drivesFailureSupported = 0;

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
     * Get Drives Failure Supported Count
     *
     * @return int The drives failure supported count of this RAID.
     */
    public function getDrivesFailureSupported()
    {
        return $this->drivesFailureSupported;
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
     * Options:
     *
     * ```
     * - sortBy - Whether to sort the drives by capacity
     * - sortOrder - If sorted, the order (ascending ('ASC') or descending ('DESC')), ascending by default
     * - withHotSpares - Whether to include the hot spares in the drives.
     * ```
     *
     * @param array $options Additional options to scope the results.
     * @return array The RAID's Drive objects.
     */
    public function getDrives(array $options = [])
    {
        $options += [
            'sortBy' => null,
            'sortOrder' => 'ASC',
            'withHotSpares' => false,
        ];

        if ($options['withHotSpares'] === false) {
            $drives = [];

            foreach ($this->drives as $drive) {
                if ($drive->isHotSpare() === false) {
                    array_push($drives, $drive);
                }
            }
        } else {
            $drives = $this->drives;
        }

        if ($options['sortBy'] === 'capacity') {
            if ($options['sortOrder'] === 'ASC') {
                usort($drives, function ($a, $b) {
                    return $a->getCapacity() - $b->getCapacity(); // sorted by ASC order
                });
            } else {
                usort($drives, function ($a, $b) {
                    return $b->getCapacity() - $a->getCapacity(); // sorted by DESC order
                });
            }
        }

        return $drives;
    }

    /**
     * Get Hot Spare Drives
     *
     * Options:
     *
     * ```
     * - sortBy - Whether to sort the drives by capacity
     * - sortOrder - If sorted, the order (ascending ('ASC') or descending ('DESC')), ascending by default
     * ```
     *
     * @param array $options Additional options to scope the results.
     * @return array The RAID's Drive objects listed as hot spare drives.
     */
    public function getHotSpareDrives(array $options = [])
    {
        $options += [
            'sortBy' => null,
            'sortOrder' => 'ASC',
        ];

        $hotSpareDrives = [];

        foreach ($this->drives as $drive) {
            if ($drive->isHotSpare() === true) {
                array_push($hotSpareDrives, $drive);
            }
        }

        if ($options['sortBy'] === 'capacity') {
            if ($options['sortOrder'] === 'ASC') {
                usort($drives, function ($a, $b) {
                    return $a->getCapacity() - $b->getCapacity(); // sorted by ASC order
                });
            } else {
                usort($hotSpareDrives, function ($a, $b) {
                    return $b->getCapacity() - $a->getCapacity(); // sorted by DESC order
                });
            }
        }

        return $hotSpareDrives;
    }

    /**
     * Set Drives
     *
     * @param array $drives An array of \kevinquinnyo\Raid\Drive Drive objects to set on the RAID.
     * @return self
     */
    public function setDrives(array $drives)
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
     * @param \kevinquinnyo\Raid\Drive $drive A Drive to add to the list of hot spares drives.
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
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * ```
     *
     * @param array $options Additional options to pass.
     * @return int|string Usable capacity of the RAID in bytes or human readable format.
     */
    abstract public function getCapacity(array $options = []);

    /**
     * Get Minimum Drive Size
     *
     * Return the capacity of the smallest drive in the array.
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * - withHotSpares - Whether to include the hot spares in the search.
     * ```
     *
     * @param array $options Additional options to pass.
     * @return int|string Capacity of the smallest drive in the array.
     */
    public function getMinimumDriveSize(array $options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
        ];
        $minimumDriveSize = $this->getDrives($options)[0]->getCapacity();
        $drives = $this->getDrives($options);

        foreach ($drives as $drive) {
            if ($drive->getCapacity() < $minimumDriveSize) {
                $minimumDriveSize = $drive->getCapacity();
            }
        }

        if ($options['human'] === true) {
            $minimumDriveSize = Number::toReadableSize($minimumDriveSize);
        }

        return $minimumDriveSize;
    }

    /**
     * Get Maximum Drive Size
     *
     * Return the capacity of the biggest drive in the array.
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * - withHotSpares - Whether to include the hot spares in the search.
     * ```
     *
     * @param array $options Additional options to pass.
     * @return int|string Capacity of the biggest drive in the array.
     */
    public function getMaximumDriveSize(array $options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
        ];
        $maximumDriveSize = $this->drives[0]->getCapacity();

        foreach ($this->getDrives($options) as $drive) {
            if ($drive->getCapacity() > $maximumDriveSize) {
                $maximumDriveSize = $drive->getCapacity();
            }
        }

        if ($options['human'] === true) {
            $maximumDriveSize = Number::toReadableSize($maximumDriveSize);
        }

        return $maximumDriveSize;
    }

    /**
     * Get Drive Count
     *
     * @param array $options Add 'withHotSpares' if you wish to include hot spares in the count.
     * @return int The drive count for the RAID.
     */
    public function getDriveCount(array $options = [])
    {
        $options += [
            'withHotSpares' => false,
        ];
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
     * @return int|string The total capacity for the RAID.
     */
    public function getTotalCapacity(array $options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
            'floor' => false,
        ];
        $total = 0;
        $minimumDriveSize = $this->getMinimumDriveSize();
        foreach ($this->getDrives($options) as $drive) {
            $total += $options['floor'] === true ? $minimumDriveSize : $drive->getCapacity();
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

    /**
     * Get parity total size
     *
     * Get the total size reserved for parity (unusable by data but not lossed).
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * ```
     *
     * @param array $options Additional options to scope the results.
     * @return int|string The total size reserved for parity of the RAID.
     */
    abstract public function getParitySize(array $options = []);

    /**
     * Get lossed space
     *
     * Get lossed space size (unusable and unused space, neither by parity nor by data).
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * - withHotSpares - Whether to include the hot spares in the sum.
     * ```
     *
     * @param array $options Additional options to scope the results.
     * @return int|string The lossed space size of the RAID.
     */
    public function getLossedSpace(array $options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
        ];
        $lossedSpace = $this->getTotalCapacity($options) - $this->getCapacity() - $this->getParitySize();

        if ($options['human'] === true) {
            $lossedSpace = Number::toReadableSize($lossedSpace);
        }

        return $lossedSpace;
    }

    /**
     * Get number of drives of the given capacity
     *
     * @param mixed $capacity The capacity of the drives you're looking for, in bytes or human readable format, e.g. - '500GB', '5T', etc.
     * @param array $options Add 'withHotSpares' if you wish to include hot spares in the count.
     * @return int The lossed capacity of the RAID.
     */
    public function getNumberOfDrivesOfThisCapacity($capacity, array $options = [])
    {
        $options += [
            'withHotSpares' => false,
        ];
        if (ctype_digit($capacity)) {
            $capacity = (int)$capacity;
        } else {
            $capacity = (int)Text::parseFileSize($capacity);
        }

        $numberOfDrivesOfThisCapacity = 0;
        $temp = $this->getDrives($options);
        foreach ($temp as $drive) {
            if ($drive->getCapacity() === $capacity) {
                ++$numberOfDrivesOfThisCapacity;
            }
        }

        return $numberOfDrivesOfThisCapacity;
    }

    /**
     * Get next maximum drive capacity
     *
     * Options:
     *
     * ```
     * - human - Whether to convert the result into human readable units, e.g. - 4 TB, 500 GB, etc
     * - withHotSpares - Whether to include the hot spares in the sum.
     * ```
     *
     * @param mixed $maximumDriveCapacity The capacity of the biggest drive you know, in bytes or human readable format, e.g. - '500GB', '5T', etc.
     * @param array $options Additional options to scope the results.
     * @return int|string The next bigger drive's capacity of the drives array, return 0 if no smaller drive than the capacity provided.
     */
    public function getNextMaximumDriveCapacity($maximumDriveCapacity, array $options = [])
    {
        $options += [
            'human' => false,
            'withHotSpares' => false,
        ];
        if (ctype_digit($maximumDriveCapacity)) {
            $maximumDriveCapacity = (int)$maximumDriveCapacity;
        } else {
            $maximumDriveCapacity = (int)Text::parseFileSize($maximumDriveCapacity);
        }

        $capacityOfNextLargerDrive = 0;
        foreach ($this->getDrives($options) as $drive) {
            if ($drive->getCapacity() > $capacityOfNextLargerDrive && $drive->getCapacity() < $maximumDriveCapacity) {
                $capacityOfNextLargerDrive = $drive->getCapacity();
            }
        }

        if ($options['human'] === true) {
            $capacityOfNextLargerDrive = Number::toReadableSize($capacityOfNextLargerDrive);
        }

        return $capacityOfNextLargerDrive;
    }
}
