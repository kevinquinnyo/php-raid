<?php
namespace kevinquinnyo\Raid\Test\Raid;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidTen;

class RaidTenTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
            new Drive(1024, 'ssd', 3),
            new Drive(1024, 'ssd', 4),
        ];
        $raidTen = new RaidTen($drives);
        $this->assertSame(2048, $raidTen->getCapacity());
        $this->assertSame('2 KB', $raidTen->getCapacity(['human' => true]));
    }
    public function testGetLevel()
    {
        $raidTen = new RaidTen();
        $this->assertSame(10, $raidTen->getLevel());
    }
}
