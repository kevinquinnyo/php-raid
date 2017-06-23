<?php
namespace kevinquinnyo\Raid;

use kevinquinnyo\Raid\Level;

class RaidOne implements RaidLevelInterface extends RaidLevel
{
    const level = 1;

    protected $drives = null;

    public function getDrives()
    {
        return $this->drives;
    }

    public function addDrive(DriveInterface $drive)
    {
        $this->drives[] = $drive;

        return $this;
    }

    protected function validateRemoval()
    {
        // validation per level, ie - if this werea raid 10 which 2 drives can i spare to lose? yikes.
        echo 'This breaks the RAID';

        return true;
    }

    protected function _removeDrive($drive, $allowDestruction = false)
    {
        if ($allowDestruction === false) {
            $this->validateRemoval();
        }

        if (isset($this->drives[$drive]) === false) {
            throw new RuntimeException('Unable to locate drive for removal.');
        }

        unset($this->drives[$drive]);

        return $this;
    }

    public function removeDrive($bay)
    {
        if (is_numeric($bay) === true) {
            $drive = $this->getDriveByBay($bay);

            return $this->_removeDrive($drive);
        }

        if (($bay instanceof DriveInterface) === false) {
            throw new InvalidArgumentException('You must supply a DriveInterface or a drive bay number to remove a drive from a RAID.');
        }

        return $this->_removeDrive($drive);
    }

    public function getLevel()
    {
        return self::level;
    }
}
