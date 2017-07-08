<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\AbstractRaid;
use \kevinquinnyo\Raid\Raid\RaidFive;

class AbstractRaidTest extends TestCase
{
    /**
     * Sets a protected property on a given object via reflection
     *
     * @param \kevinquinnyo\Raid\AbstractRaid $object Raid class in which protected value is being modified.
     * @param mixed $property Property on instance being modified.
     * @param mixed $value New value of the property being modified.
     *
     * @return void
     */
    public function setProtectedProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $reflectionProperty = $reflection->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($object, $value);
    }

    public function testGetMinimumDriveSize()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $raidClass->setDrives($drives);
        $this->assertSame(1024, $raidClass->getMinimumDriveSize());
    }

    public function testAddDrive()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $newDrive = new Drive(1024, 'ssd', 3);
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $concreteRaid->setDrives($drives);
        $concreteRaid->addDrive($newDrive);
        $drives = $concreteRaid->getDrives();

        $this->assertSame(3, count($drives));
    }

    public function testValidDriveCount()
    {
        $drives = [
            new Drive(1024, 'ssd'),
            new Drive(2048, 'ssd'),
            new Drive(2048, 'ssd'),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->setProtectedProperty($raidClass, 'minimumDrives', 4); 

        $raidClass->setDrives($drives);
        $this->assertFalse($raidClass->validDriveCount());
    }
}
