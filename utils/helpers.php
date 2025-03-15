<?php

if (! function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object or class.
     */
    function class_basename(object|string $class): string {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}
