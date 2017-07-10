<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidZero;

class RaidZeroTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $raidZero = new RaidZero($drives);
        $this->assertSame(2048, $raidZero->getCapacity());
        $this->assertSame('2 KB', $raidZero->getCapacity(['human' => true]));
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
        $raidZero = new RaidZero($drives);
        $this->assertSame(3072, $raidZero->getCapacity());
    }

    public function testGetLevel()
    {
        $raidZero = new RaidZero();
        $this->assertSame(0, $raidZero->getLevel());
    }
}
