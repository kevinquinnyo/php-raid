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
        $raidZero = new RaidOne($drives);
        $this->assertSame(2048, $raidZero->getCapacity());
    }
    public function testGetCapacityWithHotSpares()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd', true),
            new Drive(1024, 'ssd', true),
        ];
        $raidZero = new RaidOne($drives);
        $this->assertSame(3072, $raidZero->getCapacity());
    }
}
