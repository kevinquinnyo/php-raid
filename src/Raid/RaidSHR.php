<?php
namespace kevinquinnyo\Raid\Raid;

use Cake\I18n\Number;
use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidSHR extends AbstractRaid
{
    const LEVEL = "SHR";
    protected $drives = [];
    protected $hotSpares = [];
    protected $minimumDrives = 2;
    protected $mirrored = false;
    protected $parity = true;
    protected $striped = true;

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
        $capacity = $this->getTotalCapacity() - $this->getMaximumDriveSize();

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
        $minimumDriveSizeOfRAID = $this->getMinimumDriveSize();
        $maximumDriveSizeOfRAID = $this->getMaximumDriveSize();
        if ($this->getNumberOfDrivesOfThisCapacity($maximumDriveSizeOfRAID) >= 2) {
            $paritySize = $maximumDriveSizeOfRAID;
        } else {
            /* We are looking for the second disk with the highest capacity present in the drives array, the use of the first will be the same as the capacity of the second */
            $paritySize = 0;
            foreach ($this->drives as $drive) {
                if ($drive->getCapacity() > $paritySize && $drive->getCapacity() < $maximumDriveSizeOfRAID) {
                    $paritySize = $drive->getCapacity();
                }
            }
        }

        if ($options['human'] === true) {
            return Number::toReadableSize($paritySize);
        }

        return $paritySize;
    }
}
