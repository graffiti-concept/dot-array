<?php
/**
 *
 * Project: Aurora
 * @author: Graffiti Concept <aurora.github@gmail.com>
 * Created by PhpStorm 16 Jul 2025 at 09:01 CET.
 */

namespace Aurora\Generic\Dot;

class DotArrayService
{
    /**
     * @param string|\stdClass $source
     * @param \Exception|null $error
     * @return array
     */
    public static function import(string|\stdClass $source, ?\Exception &$error = null): array
    {
        if (is_string($source)) {
            return self::arrayFromJson($source, $error) ?? [];
        }

        return self::stdObjectToArray($source, $error) ?? [];
    }

    public static function arrayFromJson(?string $json, ?\Exception &$error = null): ?array
    {
        if ($json !== null && strlen($json) > 1) {
            try {
                return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            } catch (\Exception $e) {
                $error = $e;
            }
        }
        $error = new \JsonException('Syntax error');

        return null;
    }

    public static function jsonFromArray(array $value, ?\Exception &$error = null): ?string
    {
        try {
            return json_encode($value, JSON_THROW_ON_ERROR);
        } catch (\Exception  $e) {
            $error = $e;
        }

        return null;
    }

    public static function stdObjectToArray(\stdClass $stdObject, ?\Exception $error = null): ?array
    {
        try {
            $data = json_encode($stdObject, JSON_THROW_ON_ERROR);
        } catch (\Exception  $e) {
            $error = $e;

            return null;
        }

        return self::arrayFromJson($data, $error);
    }

    public static function exists(int|string $key, array &$source): bool
    {
        if ($key === '') {
            return false;
        }

        if (array_key_exists($key, $source)) {
            return true;
        }

        $branch = new BranchReference;

        return self::setupBranchReference($branch, $key, $source);
    }

    public static function isEmpty(int|string|null $key, array &$source): bool
    {
        if ($key === null || $key === '') {
            return $source === [];
        }

        if (array_key_exists($key, $source)) {
            return empty($source[$key]);
        }

        $branch = new BranchReference;

        return self::setupBranchReference($branch, $key, $source) ? empty($branch->getBranchValue()) : true;
    }

    public static function count(int|string|null $key, array &$source): int
    {
        if ($key === null || $key === '') {
            return count($source);
        }
        $branch = new BranchReference;
        if (self::setupBranchReference($branch, $key, $source)) {
            return is_array($branch->getBranchValue()) ? count($branch->getBranchValue()) : -1;
        }

        return 0;
    }

    public static function delete(int|string $key, array &$source): bool
    {
        $branch = new BranchReference;
        if (self::setupBranchReference($branch, $key, $source)) {
            unset($branch->getBranchRoot()[$branch->getBranchKey()]);

            return true;
        }

        return false;
    }

    public static function get(int|string $key, array &$source, bool &$finded = false): mixed
    {
        $branchRef = new BranchReference;

        return (($finded = self::setupBranchReference($branchRef, $key, $source))) ? $branchRef->getBranchValue() : null;
    }

    public static function getMultiple(array &$source, array $arrayOfValues): array
    {
        if ($arrayOfValues === []) {
            return [];
        }

        $result = [];
        foreach ($arrayOfValues as $keyInfo) {
            $currentPath = null;
            $newPath = null;
            $assignValueType = 0;

            $keyInfo = is_array($keyInfo) ? $keyInfo : [$keyInfo];
            if (count($keyInfo) === 1) {
                $currentPath = $newPath = end($keyInfo);
            } elseif (count($keyInfo) === 2) {
                [$currentPath, $newPath] = array_values($keyInfo);
            } elseif (count($keyInfo) > 2) {
                [$currentPath, $newPath, $assignValueType] = array_values($keyInfo);
            }

            if (in_array(gettype($currentPath), ['string', 'int', 'float'], true)) {
                if (!in_array(gettype($newPath), ['string', 'int', 'float'], true)) {
                    $newPath = $currentPath;
                }
                $finded = false;
                $value = match ($assignValueType) {
                    1, 'int' => self::getInt($currentPath, $source),
                    2, 'float' => self::getFloat($currentPath, $source),
                    3, 'string' => self::getString($currentPath, $source),
                    4, 'array' => self::getArray($currentPath, $source),
                    5, 'object' => self::getObject($currentPath, $source),
                    6, 'bool', 'boolean' => self::getBool($currentPath, $source),

                    default => self::get($currentPath, $source, $finded),
                };

                if ($finded || ($assignValueType !== 0 && $value !== null)) {
                    $result[$newPath] = $value;
                }
            }
        }

        return $result;
    }

    public static function getString(int|string $key, array &$source, bool &$finded = false): mixed
    {
        $finded = false;
        $value = self::get($key, $source, $finded);
        if (!$finded) {
            return null;
        }

        $valueType = gettype($value);
        if (in_array($valueType, ['string', 'int', 'float', 'bool', 'boolean', 'null'], true)) {
            return (string)$value;
        }

        if ($valueType === 'object') {
            if (method_exists($value, '__toString')) {
                return $value;
            }

            $data = null;
            if ($value instanceof \JsonSerializable) {
                $data = $value->jsonSerialize();
            } elseif ($value instanceof \stdClass) {
                $data = self::stdObjectToArray($value);
            }

            if (is_array($data)) {
                return self::jsonFromArray($data);
            }
        }

        return null;
    }

    public static function setMultiple(array &$source, array $arrayOfValues): int
    {
        $result = [];
        foreach ($arrayOfValues as $key => $value) {
            $result[] = self::set($key, $source, $value);
        }

        return count(array_filter($result));
    }

    public static function set(int|string|null $key, array &$source, mixed $value): bool
    {
        $stringKey = preg_replace(['/[^[:print:]]/', '/\.+/', '/^\.+/', '/\.+$/'], ['', '.', '', ''], (string)($key ?? ''));
        if ($stringKey === null || $stringKey === '') {
            return false;
        }

        if (substr_count($stringKey, '.') === 0) {
            $source[$stringKey] = $value;

            return true;
        }

        $path = explode('.', $stringKey);
        $node =& $source;
        for ($i = 0; $i < count($path) - 1; $i++) {
            if (!array_key_exists($path[$i], $node) || !is_array($node[$path[$i]])) {
                $node[$path[$i]] = [];
            }

            $node =& $node[$path[$i]];
        }
        $node[$path[count($path) - 1]] = $value;

        return true;
    }

    public static function push(int|string $key, array &$source, mixed $value): void
    {
        if (is_int($key) || substr_count($key, '.') === 0) {
            if (array_key_exists($key, $source) && is_array($source[$key])) {
                array_push($source[$key], $value);
            } else {
                $source[$key] = [$value];
            }
            return;
        }

        $branchRef = new BranchReference;
        if (self::setupBranchReference($branchRef, $key, $source)) {
            $branch =& $branchRef->getBranch();
            if (is_array($branchRef->getBranchValue())) {
                array_push($branch, $value);
            } else {
                $branch[$branchRef->getBranchKey()] = $value;
            }
        } else {
            self::set($branchRef->getBranchKey() . '.0', $source, [$value]);
        }
    }

    public static function getInt(int|string $key, array &$source, ?int $minValue = null, ?int $maxValue = null): ?int
    {
        $finded = false;
        $value = self::get($key, $source, $finded);
        if (!$finded) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            $value = (int)$value;
            return (($minValue !== null && $value < $minValue) || ($maxValue !== null && $value > $maxValue)) ? null : $value;
        }

        return null;
    }

    public static function getFloat(int|string $key, array &$source, ?float $minValue = null, ?float $maxValue = null): ?float
    {
        $finded = false;
        $value = self::get($key, $source, $finded);
        if (!$finded) {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            $value = (float)$value;
            return (($minValue !== null && $value < $minValue) || ($maxValue !== null && $value > $maxValue)) ? null : $value;
        }

        return null;
    }

    public static function getBool(int|string $key, array &$source): ?bool
    {
        $finded = false;
        $value = self::get($key, $source, $finded);

        return $finded ? filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : null;
    }

    public static function getArray(int|string $key, array &$source, bool &$finded = false): ?array
    {
        $finded = false;
        $value = self::get($key, $source, $finded);

        return $finded && is_array($value) ? $value : null;
    }

    public static function getObject(int|string $key, array &$source, bool &$finded = false): ?object
    {
        $finded = false;
        $value = self::get($key, $source, $finded);

        return $finded && is_object($value) ? $value : null;
    }

    public static function getIterator(int|string|null $key, array &$source): \Iterator
    {
        if ($key === '' || $key === null) {
            return new \ArrayIterator($source);
        }
        $branch = new BranchReference;
        if (self::setupBranchReference($branch, $key, $source)) {
            return new \ArrayIterator(is_array($branch->getBranchValue()) ? $branch->getBranch() : [$branch->getBranchValue()]);
        }

        return new \ArrayIterator();
    }

    public static function clone(int|string|null $key, array &$source, string $newInstanceClassName, bool $deleteOriginalTwig = false): ?object
    {
        $workArray = [];
        if ($key === null || $key === '') {
            $workArray = $source;
            if ($deleteOriginalTwig) {
                $source = [];
            }
        } else {
            $branch = new BranchReference;
            if (!self::setupBranchReference($branch, $key, $source)) {
                return new $newInstanceClassName([]);
            }
            $workArray = $branch->getBranch();
            if ($deleteOriginalTwig) {
                unset($branch->getBranchRoot()[$branch->getBranchKey()]);
            }
        }

        return new $newInstanceClassName($workArray);
    }

    public static function map(int|string|null $key, array &$source, callable $callable, string $newInstanceClassName, bool $recursive = false): object
    {
        $workArray = [];
        if ($key === null || $key === '') {
            $workArray = &$source;
        } else {
            $branch = new BranchReference;
            if (!self::setupBranchReference($branch, $key, $source)) {
                return new $newInstanceClassName([]);
            }
            $workArray = &$branch->getBranch();
        }

        if ($workArray === []) {
            return new $newInstanceClassName([]);
        }

        if (!$recursive) {
            return new $newInstanceClassName(array_map($callable, $workArray));
        }

        return new $newInstanceClassName([]);
    }

    public static function dotify(array &$data): array
    {
        if ($data === []) {
            return [];
        }

        $out = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($data));
        foreach ($iterator as $leafValue) {
            $keys = [];
            foreach (range(0, $iterator->getDepth()) as $depth) {
                if ($iterator->getSubIterator($depth) !== null) {
                    $keys[] = $iterator->getSubIterator($depth)->key();
                }
            }
            $out[implode('.', $keys)] = $leafValue;
        }

        return $out;
    }

    /**
     * @param BranchReference $branch
     * @param int|string|null $searchKey
     * @param array $source
     * @return bool
     */

    private static function setupBranchReference(BranchReference $branch, int|string|null $searchKey, array &$source = []): bool
    {
        if ($searchKey === null || $searchKey === '' || $searchKey === '.') {
            return false;
        }
        $searchKey = (string)$searchKey;
        if (array_key_exists($searchKey, $source)) {
            $branch->initialize($searchKey, $source);

            return true;
        }

        $searchKey = preg_replace(['/[^[:print:]]/', '/\.+/', '/^\.+/', '/\.+$/'], ['', '.', '', ''], $searchKey);
        if ($searchKey === null || $searchKey === '') {
            return false;
        }

        $rootPath = explode('.', $searchKey);
        $scan = &$source;
        $maxIndex = count($rootPath) - 1;
        for ($x = 0; $x < $maxIndex; $x++) {
            if (($scan[$rootPath[$x]] ?? null) === null) {
                return false;
            }
            $scan =& $scan[$rootPath[$x]];
        }

        if (is_array($scan) && array_key_exists($rootPath[$maxIndex], $scan)) {
            $branch->initialize($rootPath[$maxIndex], $scan);

            return true;
        }

        return false;
    }
}

