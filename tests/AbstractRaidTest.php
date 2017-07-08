<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\AbstractRaid;
use \kevinquinnyo\Raid\Raid\RaidFive;

class AbstractRaidTest extends TestCase
{
    public function testGetMinimumDriveSize()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $raidClass->setDrives($drives);
        $this->assertSame(1024, $raidClass->getMinimumDriveSize());
    }

    public function testAddDrive()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $newDrive = new Drive(1024, 'ssd', 3);
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $concreteRaid->setDrives($drives);
        $concreteRaid->addDrive($newDrive);
        $drives = $concreteRaid->getDrives();

        $this->assertSame(3, count($drives));
    }
}
