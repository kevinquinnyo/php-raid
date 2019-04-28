<?php
namespace kevinquinnyo\Raid\Test\Raid;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidSHR2;

class RaidSHR2Test extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4),
        ];
        $raidSHR2 = new RaidSHR2($drives);
        $this->assertSame(2048, $raidSHR2->getCapacity());
        $this->assertSame('2 KB', $raidSHR2->getCapacity(['human' => true]));
    }

    public function testGetCapacityWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4),
            new Drive(1024, 'ssd', 5, ['hotSpare' => true]),
            new Drive(1024, 'ssd', 6, ['hotSpare' => true]),
        ];
        $raidSHR2 = new RaidSHR2($drives);
        $this->assertSame(2048, $raidSHR2->getCapacity());
        $this->assertSame("2 KB", $raidSHR2->getCapacity(['human' => true]));
    }

    public function testGetLevel()
    {
        $raidSHR2 = new RaidSHR2();
        $this->assertSame('SHR2', $raidSHR2->getLevel());
    }

    public function testGetParitySize()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4),
        ];
        $raidSHR2 = new RaidSHR2($drives);
        $this->assertSame(2048, $raidSHR2->getParitySize());
        $this->assertSame("2 KB", $raidSHR2->getParitySize(['human' => true]));
    }

    public function testGetParitySizeWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4),
            new Drive(1024, 'ssd', 5, ['hotSpare' => true]),
            new Drive(1024, 'ssd', 6, ['hotSpare' => true]),
        ];
        $raidSHR2 = new RaidSHR2($drives);
        $this->assertSame(2048, $raidSHR2->getParitySize());
        $this->assertSame("2 KB", $raidSHR2->getParitySize(['human' => true]));
    }
}
