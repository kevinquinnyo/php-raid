<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidOne;

class RaidOneTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
        ];
        $raidOne = new RaidOne($drives);
        $this->assertSame(1024, $raidOne->getCapacity());
        $this->assertSame('1 KB', $raidOne->getCapacity(['human' => true]));
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
        $raidOne = new RaidOne($drives);
        $this->assertSame(1024, $raidOne->getCapacity());
    }
    public function testGetLevel()
    {
        $raidOne = new RaidOne();
        $this->assertSame(1, $raidOne->getLevel());
    }
}
