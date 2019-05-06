<?php
namespace kevinquinnyo\Raid\Test\Raid;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidSHR;

class RaidSHRTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
        ];
        $raidSHR = new RaidSHR($drives);
        $this->assertSame(2048, $raidSHR->getCapacity());
        $this->assertSame('2 KB', $raidSHR->getCapacity(['human' => true]));
    }

    public function testGetCapacityWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4, ['hotSpare' => true]),
            new Drive(1024, 'ssd', 5, ['hotSpare' => true]),
        ];
        $raidSHR = new RaidSHR($drives);
        $this->assertSame(2048, $raidSHR->getCapacity());
        $this->assertSame("2 KB", $raidSHR->getCapacity(['human' => true]));
    }

    public function testGetLevel()
    {
        $raidSHR = new RaidSHR();
        $this->assertSame('SHR', $raidSHR->getLevel());
    }

    public function testGetParitySize()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
        ];
        $raidSHR = new RaidSHR($drives);
        $this->assertSame(1024, $raidSHR->getParitySize());
        $this->assertSame("1 KB", $raidSHR->getParitySize(['human' => true]));
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
        $raidSHR = new RaidSHR($drives);
        $this->assertSame(1024, $raidSHR->getParitySize());
        $this->assertSame("1 KB", $raidSHR->getParitySize(['human' => true]));
    }
}
