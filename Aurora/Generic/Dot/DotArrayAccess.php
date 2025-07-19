<?php
/**
 *
 * Project: Aurora
 * @author: Graffiti Concept <aurora.github@gmail.com>
 * Created by PhpStorm 17 Jul 2025 at 09:00 CET.
 */


namespace Aurora\Generic\Dot;

/**
 * @implements \ArrayAccess<int|string,mixed>
 */
final class DotArrayAccess extends DotArray implements \ArrayAccess
{
    /**
     * Whether or not an data exists by key
     *
     * @param int|string $key An data key to check for
     * @access public
     * @return boolean
     */
    public function __isset(int|string $key): bool
    {
        return parent::has($key);
    }

    /**
     * Assigns a value to the specified data
     *
     * @param mixed $key The data key to assign the value to
     * @param mixed $key The value to set
     * @access public
     */
    public function __set(mixed $key, mixed $value): void
    {
        parent::set($key, $value);
    }

    /**
     * Get a data by key
     *
     * @param int|string $key The key data to retrieve
     * @access public
     */
    public function &__get($key): mixed
    {
        return parent::get($key);
    }

    /**
     * Unsets an data by key
     *
     * @param int|string $key The key to unset
     * @access public
     */
    public function __unset($key): void
    {
        parent::delete($key);
    }

    public function offsetExists(mixed $key): bool
    {
        return parent::has($key);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return parent::get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        parent::set($offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        parent::delete($offset);
    }
}