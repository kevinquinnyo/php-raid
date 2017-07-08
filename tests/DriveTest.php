<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;

class DriveTest extends TestCase
{
    public function testGetCapacity()
    {
        $drive = new Drive('1k', 'ssd', 1);
        $this->assertSame(1024, $drive->getCapacity());
    }

    public function testGetCapacityWithHuman()
    {
        $drive = new Drive('1k', 'ssd', 1);
        $this->assertSame('1 KB', $drive->getCapacity(['human' => true]));
    }

    public function testGetType()
    {
        $drive = new Drive(1024, 'ssd', 1);
        $this->assertSame('ssd', $drive->getType());
    }
}
