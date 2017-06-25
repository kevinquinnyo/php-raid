<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\Raid\RaidTen;

class RaidTenTest extends TestCase
{
    public function testGetCapacity()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
            new Drive(1024, 'ssd'),
        ];
        $raidTen = new RaidTen($drives);
        $this->assertSame(2048, $raidTen->getCapacity());
    }
    public function testGetLevel()
    {
        $raidTen = new RaidTen();
        $this->assertSame(10, $raidTen->getLevel());
    }
}
