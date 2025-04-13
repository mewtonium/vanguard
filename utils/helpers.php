<?php

declare(strict_types=1);

if (! function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object or class.
     */
    function class_basename(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (! function_exists('to_date')) {
    /**
     * Creates a `DateTime` instance from the given date string.
     * 
     * Only the following formats are accepted:
     * - `Y-m-d`
     * - `Y-m-d H:i`
     * - `Y-m-d H:i:s`
     *
     * @throws \DateException
     */
    function to_date(string $datetime, bool $immutable = true, bool $throw = false): ?\DateTimeInterface
    {
        $formats = [
            'Y-m-d',
            'Y-m-d H:i',
            'Y-m-d H:i:s',
        ];

        foreach ($formats as $format) {
            $date = $immutable
                ? DateTimeImmutable::createFromFormat($format, $datetime)
                : DateTime::createFromFormat($format, $datetime);

            if ($date !== false) {
                return match ($format) {
                    'Y-m-d' => $date->setTime(hour: 0, minute: 0, second: 0),
                    'Y-m-d H:i' => $date->setTime(hour: (int) $date->format('H'), minute: (int) $date->format('i'), second: 0),
                    'Y-m-d H:i:s' => $date,
                };
            }
        }

        if ($throw) {
            throw new \DateException("An invalid datetime string was provided: '{$datetime}'");
        }

        return null;
    }
}
