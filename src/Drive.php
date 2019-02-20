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
    protected $identifier = null;

    /**
     * Constructor.
     *
     * Create a drive object.
     *
     * Options:
     *
     * ```
     * - hotSpare - If this drive should be considered a hot spare.
     * ```
     *
     * @param mixed $capacity The capacity in bytes or human readable format, e.g. - '500GB', '5T', etc.
     * @param string $type The drive media type, e.g. - 'ssd' or 'hdd'.
     * @param string $identifier A unique identifier for the drive.
     * @param array $options Additional options.
     */
    public function __construct($capacity, string $type, string $identifier, $options = [])
    {
        $options += [
            'hotSpare' => false,
        ];
        if (ctype_digit($capacity)) {
            $bytes = (int)$capacity;
        }
        $this->capacity = isset($bytes) === true ? $bytes : (int) Text::parseFileSize($capacity);
        $this->validate($type);
        $this->type = $type;
        $this->identifier = $identifier;
        $this->hotSpare = $options['hotSpare'];
    }

    /**
     * Get Identifier
     *
     * @return string The unique identifier for this drive.
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set Identifier
     *
     * @param string $identifier The unique identifier to set for this drive.
     * @return self
     */
    public function setIdentifier(string $identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Validate
     *
     * Validate the drive type is supported.
     *
     * @param mixed $type The drive type to check.
     * @throws \InvalidArgumentException if the drive is type is not supported.
     * @return void
     */
    protected function validate($type)
    {
        $types = ['ssd', 'hdd'];

        if (in_array($type, $types) === false) {
            throw new InvalidArgumentException('This is not a valid drive type.');
        }
    }

    /**
     * Get Capacity
     *
     * Options:
     * ```
     * - human - Whether to return the results in human readble format
     * or bytes.
     * ```
     *
     * @param array $options Additional options to pass.
     * @return string|int The capacity in bytes or human readable format.
     */
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

    /**
     * Get Type
     *
     * @return string The drive type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set Hot Spare
     *
     * @param bool $set Default true, use false to make the drive not a hot spare.
     * @return self
     */
    public function setHotSpare(bool $set = true)
    {
        $this->hotSpare = (bool)$set;

        return $this;
    }

    /**
     * Is Hot Spare
     *
     * @return bool If the drive is a hot spare.
     */
    public function isHotSpare()
    {
        return $this->hotSpare;
    }
}
