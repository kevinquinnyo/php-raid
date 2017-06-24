<?php
namespace kevinquinnyo\Raid;

interface RaidLevelInterface
{
    public function getDrives();

    public function addDrive();

    public function removeDrive();
    
    protected function validateRemoval();

    public function getLevel();
}
