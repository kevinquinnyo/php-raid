<?php
namespace kevinquinnyo\Raid\Test\Raid;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidFive;

class RaidFiveTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
        ];
        $raidFive = new RaidFive($drives);
        $this->assertSame(2048, $raidFive->getCapacity());
        $this->assertSame('2 KB', $raidFive->getCapacity(['human' => true]));
    }

    public function testGetCapacityWithHotSparesWithoutHotSparesOption()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4, ['hotSpare' => true]),
            new Drive(1024, 'ssd', 5, ['hotSpare' => true]),
        ];
        $raidFive = new RaidFive($drives);
        $this->assertSame(2048, $raidFive->getCapacity());
    }
    public function testGetLevel()
    {
        $raidFive = new RaidFive();
        $this->assertSame(5, $raidFive->getLevel());
    }
}
