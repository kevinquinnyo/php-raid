<?php
namespace kevinquinnyo\Raid\Raid;

use Cake\I18n\Number;
use kevinquinnyo\Raid\AbstractRaid;
use kevinquinnyo\Raid\Drive;

class RaidTen extends AbstractRaid
{
    const LEVEL = 10;
    protected $drives = [];
    protected $mirrored = true;
    protected $parity = false;
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

        $this->drivesFailureSupported = count($drives) / 2;
        $this->setDrives($drives);
    }

    /**
     * Get Capacity
     *
     * Options:
     *
     * ```
     * - human - Whether to return the results in human readable format.
     * ```
     * @param array $options Additional options to pass.
     * @return int|string Usable capacity of the RAID in bytes or human readable format.
     */
    public function getCapacity(array $options = [])
    {
        $options += [
            'human' => false,
        ];
        $drivesOrderedByCapacity = $this->getDrives(['orderBy' => 'capacity', 'sortOrder' => 'DESC']);
        $numberOfDrives = count($drivesOrderedByCapacity);
        $capacity = 0;
        for ($i = 0; $i + 1 < $numberOfDrives; $i += 2) {
            $firstDrive = $drivesOrderedByCapacity[$i];
            $secondDrive = $drivesOrderedByCapacity[$i + 1];
            $capacity += min($firstDrive->getCapacity(), $secondDrive->getCapacity());
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
        $drivesOrderedByCapacity = $this->getDrives(['orderBy' => 'capacity', 'sortOrder' => 'DESC']);
        $numberOfDrives = count($drivesOrderedByCapacity);
        $paritySize = 0;
        for ($i = 0; $i + 1 < $numberOfDrives; $i += 2) {
            $firstDrive = $drivesOrderedByCapacity[$i];
            $secondDrive = $drivesOrderedByCapacity[$i + 1];
            $paritySize += min($firstDrive->getCapacity(), $secondDrive->getCapacity());
        }

        if ($options['human'] === true) {
            return Number::toReadableSize($paritySize);
        }

        return $paritySize;
    }
}
