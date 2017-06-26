<?php
namespace kevinquinnyo\Raid;

use Cake\I18n\Number;
use Cake\Utility\Text;
use InvalidArgumentException;

class Drive
{
    protected $capacity = null;
    protected $type = null;
    protected $hotSpare = false;

    public function __construct($capacity, string $type, $options = [])
    {
        $options += [
            'hotSpare' => false,
        ];
        $this->capacity = Text::parseFileSize($capacity);
        $this->validate($type);
        $this->type = $type;
        $this->hotSpare = $options['hotSpare'];
    }

    protected function validate($type)
    {
        $types = ['ssd', 'hdd'];

        if (in_array($type, $types) === false) {
            throw new InvalidArgumentException('This is not a valid drive type.');
        }
    }

    public function getCapacity($options = [])
    {
        $options += [
            'human' => false,
        ];
        if ($options['human'] === true) {
            return Number::toReadableSize($this->capacity);
        }

        return $this->capacity;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setHotSpare($set = true)
    {
        $this->hotSpare = (bool)$set;
    }

    public function isHotSpare()
    {
        return $this->hotSpare;
    }
}
