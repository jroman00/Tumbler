<?php
namespace Acme\Libraries;

use InvalidArgumentException;

/**
 * Tumbler library.
 */
class Tumbler
{
    /**
     * @var string
     */
    const BEFORE_START = '-';

    /**
     * @var string
     */
    const START = 'a';

    /**
     * @var string
     */
    const MIDDLE = 'm';

    /**
     * @var string
     */
    const END = 'z';

    /**
     * The final position.
     * @var string
     */
    protected $position = '';

    /**
     * Get the new position.
     *
     * @param string|null $previous
     * @param string|null $next
     * @return string
     * @throws InvalidArgumentException
     */
    public function getPositionBetween($previous, $next)
    {
        $previous = $previous !== null ? strtolower($previous) : '';
        $next = $next !== null ? strtolower($next) : '';

        if (preg_match('/[^-a-z]/', $previous) || preg_match('/[^-a-z]/', $previous)) {
            throw new InvalidArgumentException(
                'Invalid previous and/or next values: previous=' . $previous . ', next=' . $next
            );
        }

        return $this->buildPosition($previous, $next, 0);
    }

    /**
     * Build a new position.
     *
     * @param string $previous
     * @param string $next
     * @param int $index
     * @return string
     */
    protected function buildPosition($previous, $next, $index)
    {
        if ($index >= strlen($previous) && $index >= strlen($next)) {
            return self::START;
        }

        if ($index >= strlen($previous)) {
            return $this->getPreviousPosition($next, $index);
        }

        if ($index >= strlen($next)) {
            return $this->getNextPosition($previous, $index);
        }

        if (ord($previous[$index]) + 1 < ord($next[$index]) && ord($previous[$index]) >= ord(self::START)) {
            return chr((ord($previous[$index]) + 1));
        }

        return $previous[$index] . $this->buildPosition($previous, $next, $index + 1);
    }

    /**
     * Get the next available position.
     *
     * @param string $old
     * @param int $index
     * @return string
     */
    public function getNextPosition($old, $index)
    {
        if (!isset($old[$index]) || $old[$index] === self::BEFORE_START) {
            return self::START;
        }

        if (ord($old[$index]) < ord(self::END)) {
            return chr((ord($old[$index]) + 1));
        }

        if ($index + 1 >= strlen($old)) {
            return self::END . self::START;
        }

        return $old[$index] . $this->getNextPosition($old, $index + 1);
    }

    /**
     * Get the previous available position.
     *
     * @param string $old
     * @param int $index
     * @return string
     */
    public function getPreviousPosition($old, $index)
    {
        if (!isset($old[$index])) {
            return self::END;
        }

        if (ord($old[$index]) > ord(self::START)) {
            return chr((ord($old[$index]) - 1));
        }

        if ($index + 1 >= strlen($old)) {
            return self::BEFORE_START . self::MIDDLE;
        }

        return $old[$index] . $this->getPreviousPosition($old, $index + 1);
    }
}

