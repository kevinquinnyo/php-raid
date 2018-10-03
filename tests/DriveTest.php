<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use InvalidArgumentException;

class DriveTest extends TestCase
{
    /**
     * testValidate
     *
     * @group asdf
     */
    public function testValidate()
    {
        $this->expectException(InvalidArgumentException::class);
        $drive = new Drive('1k', 'well this is not a valid drive type', 1);
    }
    public function testSetIdentifier()
    {
        $drive = new Drive('1k', 'ssd', 1);
        $this->assertEquals(1, $drive->getIdentifier());

        $drive->setIdentifier('new identifier');
        $this->assertEquals('new identifier', $drive->getIdentifier());
    }
    public function testGetCapacity()
    {
        $drive = new Drive('1k', 'ssd', 1);
        $this->assertSame(1024.0, $drive->getCapacity());
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
