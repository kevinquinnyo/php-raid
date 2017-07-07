<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidSix;

class RaidSixTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
        ];
        $raidSix = new RaidSix($drives);
        $this->assertSame(2048, $raidSix->getCapacity());
        $this->assertSame('2 KB', $raidSix->getCapacity(['human' => true]));
    }
    public function testGetCapacityWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd', ['hotSpare' => true]),
            new Drive(1024, 'ssd', ['hotSpare' => true]),
        ];
        $raidSix = new RaidSix($drives);
        $this->assertSame(2048, $raidSix->getCapacity());
    }
    public function testGetLevel()
    {
        $raidSix = new RaidSix();
        $this->assertSame(6, $raidSix->getLevel());
    }
}
