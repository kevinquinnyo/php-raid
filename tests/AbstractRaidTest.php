<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\AbstractRaid;
use ReflectionClass;
use RuntimeException;

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

    public function testValidateThrowsExceptionWhenDriveIsNotADrive()
    {
        $this->expectException(RuntimeException::class);
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $raidClass->validate(['invalid']);
    }
    /**
     * testValidateThrowsExceptionWhenDriveIsNotADrive
     *
     * @group asdf
     */
    public function testValidateThrowsExceptionWhenDriveIdentifierAlreadyPresent()
    {
        $this->expectException(RuntimeException::class);
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $drives = [
            new Drive(1024, 'ssd', 'DUPLICATE IDENTIFIER'),
            new Drive(2048, 'ssd', 'DUPLICATE IDENTIFIER'),
        ];
        $raidClass->setDrives($drives);
        $raidClass->validate($existingDrive);
    }

    public function testGetHotSpares()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->assertEquals([], $raidClass->getHotSpares());
    }

    public function testAddHotSpare()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        // ensure there are no hot spares to begin with.
        $this->assertEquals([], $raidClass->getHotSpares());

        $newDrive = new Drive(1024, 'ssd', 1);
        $raidClass = $raidClass->addHotSpare($newDrive);

        $this->assertEquals([$newDrive], $raidClass->getHotSpares());
    }

    public function testGetDriveCount()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $this->assertEquals(3, $raidClass->getDriveCount());
    }

    public function testGetTotalCapacity()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $this->assertEquals(2048, $raidClass->getTotalCapacity());
    }

    public function testGetTotalCapacityWithHotSpares()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $options = ['withHotSpares' => true];
        $this->assertEquals('3072', $raidClass->getTotalCapacity($options));
    }

    public function testGetTotalCapacityWithHuman()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $options = ['human' => true];
        $this->assertEquals('2 KB', $raidClass->getTotalCapacity($options));
    }

    public function testGetTotalCapacityWithFloorFalse()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $options = ['floor' => false];
        $this->assertEquals(3072, $raidClass->getTotalCapacity($options));
    }

    public function testGetDriveCountWithHotSpares()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $hotSpare = new Drive(1024, 'ssd', 4);
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);

        $options = ['withHotSpares' => true];
        $this->assertEquals(4, $raidClass->getDriveCount($options));
    }

    public function testIsMirrored()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->assertFalse($raidClass->isMirrored());
    }

    public function testIsStriped()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->assertFalse($raidClass->isStriped());
    }

    public function testIsUnevenMirrorWithIsMirroredFalse()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();

        // ensure isMirrored is false first
        $this->assertFalse($raidClass->isMirrored());

        $this->assertFalse($raidClass->isUnevenMirror());
    }

    public function testGetMinimumDrives()
    {
        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $this->assertEquals(1, $raidClass->getMinimumDrives());
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

    public function testGetMinimumDriveSizeWithHotSpareWithoutHotSpareOption()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $hotSpare = new Drive(512, 'ssd', 4);

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);
        $this->assertSame(1024, $raidClass->getMinimumDriveSize());
    }

    public function testGetMinimumDriveSizeWithHotSpare()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(2048, 'ssd', 2),
            new Drive(2048, 'ssd', 3),
        ];

        $hotSpare = new Drive(512, 'ssd', 4);

        $concreteRaid = $this->getMockForAbstractClass(AbstractRaid::class);
        $raidClass = new $concreteRaid();
        $raidClass->setDrives($drives);
        $raidClass->addHotSpare($hotSpare);
        $this->assertSame(512, $raidClass->getMinimumDriveSize(['withHotSpares' => true]));
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
