<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\AbstractRaid;
use \kevinquinnyo\Raid\Raid\RaidFive;
use ReflectionClass;

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
        $raidClass = new $concreteRaid();
        $raidClass->setDrives($drives);
        $raidClass->addDrive($newDrive);
        $drives = $raidClass->getDrives();

        $this->assertSame(3, count($drives));
    }

    public function testValidDriveCountWithoutEnoughDrives()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->setProtectedProperty($raidClass, 'minimumDrives', 4);

        $raidClass->setDrives($drives);
        $this->assertFalse($raidClass->validDriveCount());
    }

    public function testValidDriveCountWithOddDrives()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
            new Drive(2048, 'ssd', 4),
            new Drive(2048, 'ssd', 5),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        /* Basically this is a Raid 10 with 5 drives */
        $this->setProtectedProperty($raidClass, 'minimumDrives', 4);
        $this->setProtectedProperty($raidClass, 'mirrored', true);

        $raidClass->setDrives($drives);
        $this->assertFalse($raidClass->validDriveCount());
    }

    public function testValidDriveCountWithEvenDrivesPlusSpare()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
            new Drive(2048, 'ssd', 4),
            new Drive(2048, 'ssd', 5, ['hotSpare' => true]),
        ];

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        /* Basically this is a Raid 10 with 4 drives and one hot spare */
        $this->setProtectedProperty($raidClass, 'minimumDrives', 4);
        $this->setProtectedProperty($raidClass, 'mirrored', true);

        $raidClass->setDrives($drives);
        $this->assertTrue($raidClass->validDriveCount());
    }
}
