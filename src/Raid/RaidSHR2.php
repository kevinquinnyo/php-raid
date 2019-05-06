<?php
namespace kevinquinnyo\Raid\Raid;

use Cake\I18n\Number;
use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidSHR2 extends AbstractRaid
{
    const LEVEL = "SHR2";
    protected $drives = [];
    protected $mirrored = false;
    protected $parity = true;
    protected $striped = true;
    protected $minimumDrives = 4;
    protected $drivesFailureSupported = 2;

    /**
     * Constructor.
     *
     * @param array $drives An array of \kevinquinnyo\Raid\Drive objects to initialize the RAID with.
     */
    public function __construct(array $drives = [])
    {
        if (empty($drives) === false) {
            $this->validate($drives);
        }

        $this->setDrives($drives);
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
    public function getCapacity(array $options = [])
    {
        $options += [
            'human' => false,
        ];
        $numberOfParityDrives = $this->minimumDrives - 2; /* the different blocks of parities are distributed among all drives */
        $maximumDriveSizeOfRAID = $this->getMaximumDriveSize();
        if ($this->getNumberOfDrivesOfThisCapacity($maximumDriveSizeOfRAID) >= $this->minimumDrives) {
            $capacity = $this->getTotalCapacity() - ($maximumDriveSizeOfRAID * $numberOfParityDrives);
        } else {
            /* We are looking for the second drive with the highest capacity present in the drives array, the use of the first will be the same as the capacity of the second */
            $capacity = 0;
            $capacityOfSecondLargestDrive = $this->getNextMaximumDriveCapacity($maximumDriveSizeOfRAID);
            if ($this->getNumberOfDrivesOfThisCapacity($capacityOfSecondLargestDrive) >= $numberOfParityDrives) {
                foreach ($this->drives as $drive) {
                    if ($drive->getCapacity() < $capacityOfSecondLargestDrive) {
                        $capacity += $drive->getCapacity();
                    } else {
                        $capacity += $capacityOfSecondLargestDrive;
                    }
                }
                $capacity -= $capacityOfSecondLargestDrive * $numberOfParityDrives;
            } else {
                /* We are looking for the third drive with the highest capacity present in the drives array, the use of the two firsts will be the same as the capacity of the third */
                $capacityOfThirdLargestDrive = $this->getNextMaximumDriveCapacity($capacityOfSecondLargestDrive);
                foreach ($this->drives as $drive) {
                    if ($drive->getCapacity() < $capacityOfThirdLargestDrive) {
                        $capacity += $drive->getCapacity();
                    } else {
                        $capacity += $capacityOfThirdLargestDrive;
                    }
                }
                $capacity -= $capacityOfThirdLargestDrive * $numberOfParityDrives;
            }
        }

        if ($options['human'] === true) {
            return Number::toReadableSize($capacity);
        }

        return $capacity;
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
    public function getParitySize(array $options = [])
    {
        $options += [
            'human' => false,
        ];
        $numberOfParityDrives = $this->minimumDrives - 2; /* the different blocks of parities are distributed among all drives */
        $maximumDriveSizeOfRAID = $this->getMaximumDriveSize();
        if ($this->getNumberOfDrivesOfThisCapacity($maximumDriveSizeOfRAID) >= $this->minimumDrives) {
            $paritySize = $maximumDriveSizeOfRAID * $numberOfParityDrives;
        } else {
            /* We are looking for the second drive with the highest capacity present in the drives array, the use of the first will be the same as the capacity of the second */
            $capacityOfSecondLargestDrive = $this->getNextMaximumDriveCapacity($maximumDriveSizeOfRAID);
            if ($this->getNumberOfDrivesOfThisCapacity($capacityOfSecondLargestDrive) >= $numberOfParityDrives) {
                $paritySize = $capacityOfSecondLargestDrive * $numberOfParityDrives;
            } else {
                /* We are looking for the third drive with the highest capacity present in the drives array, the use of the two firsts will be the same as the capacity of the third */
                $capacityOfThirdLargestDrive = $this->getNextMaximumDriveCapacity($capacityOfSecondLargestDrive);
                $paritySize = $capacityOfThirdLargestDrive * $numberOfParityDrives;
            }
        }

        if ($options['human'] === true) {
            return Number::toReadableSize($paritySize);
        }

        return $paritySize;
    }
}
