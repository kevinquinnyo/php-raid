<?php
namespace kevinquinnyo\Raid;

class RaidLevel
{
    protected $disks = null;

    public function __construct(int $disks)
    {
        $this->disks = $disks;
    }
