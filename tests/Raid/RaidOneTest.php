<?php
namespace kevinquinnyo\Raid\Test\Raid;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidOne;

class RaidOneTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $raidOne = new RaidOne($drives);
        $this->assertSame(1024, $raidOne->getCapacity());
        $this->assertSame('1 KB', $raidOne->getCapacity(['human' => true]));
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
        $raidOne = new RaidOne($drives);
        $this->assertSame(1024, $raidOne->getCapacity());
        $this->assertSame('1 KB', $raidOne->getCapacity(['human' => true]));
    }

    public function testGetLevel()
    {
        $raidOne = new RaidOne();
        $this->assertSame(1, $raidOne->getLevel());
    }

    public function testGetParitySize()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
        ];
        $raidOne = new RaidOne($drives);
        $this->assertSame(2048, $raidOne->getParitySize());
        $this->assertSame("2 KB", $raidOne->getParitySize(['human' => true]));
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
        $raidOne = new RaidOne($drives);
        $this->assertSame(3072, $raidOne->getParitySize());
        $this->assertSame("3 KB", $raidOne->getParitySize(['human' => true]));
    }
}
