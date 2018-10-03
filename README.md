<p align="center">
    <a href="https://travis-ci.org/php-raid/php-raid" target="_blank">
        <img alt="Build Status" src="https://img.shields.io/travis/kevinquinnyo/php-raid/master.svg?style=flat-square">
    </a>
    <a href="https://codecov.io/github/php-raid/php-raid" target="_blank">
        <img alt="Coverage Status" src="https://img.shields.io/codecov/c/github/kevinquinnyo/php-raid.svg?style=flat-square">
    </a>
</p>
# php-raid
###### A general purpose RAID (Redundant Array of Independent Disks) library in PHP.

This library can be used to build objects that represent RAIDs. This is useful if you are wanting to
manage and keep track of servers in a datacenter environment or for a home lab as part of a larger
project.

The library itself simply allows you to create object representations of a RAID and its underlying drives. It manages the total usable capacity of various RAID types for you, as well as hot spares, and general RAID validation for things like minimum drive count for a specific RAID level.

Currently compatible RAID types are:

- RAID 0
- RAID 1
- RAID 5
- RAID 6
- RAID 10

## Usage

To create a RAID object, you should determine which type of RAID you would like to create, and initialize
it with an array of `\kevinquinnyo\Raid\Drive` objects.

```php
use kevinquinnyo\Raid\Drive;
use kevinquinnyo\Raid\RaidTen;
use kevinquinnyo\Raid\RaidZero;

$drives = [
    new Drive('1TB', 'ssd', '61cf6218-f378-4c7b-8b81-c09e84a1a86f'),
    new Drive('1TB', 'ssd', 'c76bee9a-dce6-433c-acc3-b82947335dd2'),
    new Drive('1TB', 'ssd', '31125db2-6043-4955-818a-af9d259905eb'),
    new Drive('1TB', 'ssd', '302db7a1-bb2c-4fb9-a9e1-30636e8f312c'),
];

$options = ['human' => true];

$raidTen = new RaidTen($drives);

echo $raidTen->getCapacity(); // 2199023255552
echo $raidTen->getCapacity($options); // 2 TB

$raidZero = new RaidZero($drives);
echo $raidZero->getCapacity(); // 4398046511104
echo $raidZero->getCapacity($options); // 4 TB
```

This library is most useful if you are doing some kind of inventory management already
and are storing drives (SSD, HDD) into some kind of persistent RDBMS and would like to
also start keeping persistent state of RAID in that system.

It's also a great jumping off point if you want to do something really cool like automate
the configuration of hardware or software RAID in your datacenter/environment.

## Documentation

Coming soon. For now see the [tests](/tests).

## Contribution

I welcome it and would appreciate it.  Fork the repository and create a feature branch off the 'develop' branch and open a pull request.
