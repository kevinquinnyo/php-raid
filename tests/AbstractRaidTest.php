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
            new Drive(1024, 'ssd'),
            new Drive(2048, 'ssd'),
            new Drive(2048, 'ssd'),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid($drives);
        $raidClass->setDrives($drives);
        $this->assertSame(1024, $raidClass->getMinimumDriveSize());
    }

    public function testAddDrive()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
        ];
        $newDrive = new Drive(1024, 'ssd');
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $concreteRaid->addDrive($newDrive);
        $drives = $concreteRaid->getDrives();

        $this->assertSame(3, count($drives));
    }
}
