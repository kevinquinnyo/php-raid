<?php
namespace kevinquinnyo\Raid;

class RaidLevel
{
    protected $drives = null;

    public function __construct(int $drives)
    {
        $this->drives = $drives;
    }
