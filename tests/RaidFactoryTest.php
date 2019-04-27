<?php
namespace kevinquinnyo\Raid\Test;

use \PHPUnit\Framework\TestCase;
use \kevinquinnyo\Raid\Drive;
use \kevinquinnyo\Raid\RaidFactory;
use \kevinquinnyo\Raid\Raid\RaidZero;
use \kevinquinnyo\Raid\Raid\RaidOne;
use \kevinquinnyo\Raid\Raid\RaidFive;
use \kevinquinnyo\Raid\Raid\RaidSix;
use \kevinquinnyo\Raid\Raid\RaidTen;
use \kevinquinnyo\Raid\Raid\RaidSHR;
use InvalidArgumentException;

class RaidFactoryTest extends TestCase
{
    public function testCreateInvalidRaid()
    {
        $this->expectException(InvalidArgumentException::class);
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create('-999999', $drives);
    }
    public function testCreateRaidZero()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create(0, $drives);
        $this->assertInstanceOf(RaidZero::class, $raid);
    }
    public function testCreateRaidOne()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create(1, $drives);
        $this->assertInstanceOf(RaidOne::class, $raid);
    }
    public function testCreateRaidFive()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create(5, $drives);
        $this->assertInstanceOf(RaidFive::class, $raid);
    }
    public function testCreateRaidSix()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create(6, $drives);
        $this->assertInstanceOf(RaidSix::class, $raid);
    }
    public function testCreateRaidTen()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create(10, $drives);
        $this->assertInstanceOf(RaidTen::class, $raid);
    }
    public function testCreateSHRRaid()
    {
        $drives = [
            new Drive(1024, 'ssd', 1),
            new Drive(1024, 'ssd', 2),
        ];
        $factory = new RaidFactory();
        $raid = $factory->create('SHR', $drives);
        $this->assertInstanceOf(RaidSHR::class, $raid);
    }
}
