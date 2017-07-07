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
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
        ];
        $raidZero = new RaidZero($drives);
        $this->assertSame(2048, $raidZero->getCapacity());
        $this->assertSame('2 KB', $raidZero->getCapacity(['human' => true]));

    }
    public function testGetCapacityWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd', ['hotSpare' => true]),
            new Drive(1024, 'ssd', ['hotSpare' => true]),
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
