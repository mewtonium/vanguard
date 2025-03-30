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
     */
    function to_date(string $datetime, bool $immutable = true): ?\DateTimeInterface
    {
        try {
            $date = $immutable
                ? new \DateTimeImmutable($datetime)
                : new \DateTime($datetime);
        } catch (\DateException $e) {
            return null;
        }

        return $date;
    }
}
