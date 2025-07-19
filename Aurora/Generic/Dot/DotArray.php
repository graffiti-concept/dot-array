<?php
/**
 *
 * Project: Aurora
 * @author: Graffiti Concept <aurora.github@gmail.com>
 * Created by PhpStorm 16 Jul 2025 at 08:37 CET.
 */


namespace Aurora\Generic\Dot;

class DotArray
{
    private array $input = [];
    private string $dotArrayAccessService;

    public function __construct(array|string|\stdClass $input = [], string $dotArrayAccessService = DotArrayService::class)
    {
        $error = null;
        $this->input = is_array($input) ? $input : $dotArrayAccessService::import($input, $error);
        $this->dotArrayAccessService = $dotArrayAccessService;
    }

    public function __invoke(int|string $key = ''): \Iterator
    {
        return $this->dotArrayAccessService::getIterator($key, $this->input);
    }

    public function __toString(): string
    {
        return $this->dotArrayAccessService::jsonFromArray($this->input) ?? '';
    }

    public function __serialize(): array
    {
        return $this->input;
    }

    public function __unserialize(array $input): void
    {
        $this->input = $input;
    }

    public function has(int|string $key): bool
    {
        return $this->dotArrayAccessService::exists($key, $this->input);
    }

    public function isEmpty(null|int|string $key): bool
    {
        return $this->dotArrayAccessService::isEmpty($key, $this->input);
    }

    public function count(null|int|string $key): int
    {
        return $this->dotArrayAccessService::count($key, $this->input);
    }

    public function delete(null|int|string $key): bool
    {
        return $this->dotArrayAccessService::delete($key, $this->input);
    }

    public function get(null|int|string $key, bool &$finded = false): mixed
    {
        return $this->dotArrayAccessService::get($key, $this->input, $finded);
    }

    public function getMultiple(array $arrayOfValues): array
    {
        return $this->dotArrayAccessService::getMultiple($this->input, $arrayOfValues);
    }

    public function getString(null|int|string $key, bool &$finded = false): ?string
    {
        return $this->dotArrayAccessService::getString($key, $this->input, $finded);
    }

    public function getInt(int|string $key, ?int $minValue = null, ?int $maxValue = null): ?int
    {
        return $this->dotArrayAccessService::getInt($key, $this->input, $minValue, $maxValue);
    }

    public function getFloat(int|string $key, ?float $minValue = null, ?float $maxValue = null): ?float
    {
        return $this->dotArrayAccessService::getFloat($key, $this->input, $minValue, $maxValue);
    }

    public function getBool(int|string $key): ?bool
    {
        return $this->dotArrayAccessService::getBool($key, $this->input);
    }

    public function getObject(int|string $key): ?object
    {
        return $this->dotArrayAccessService::getObject($key, $this->input);
    }

    public function getArray(int|string $key): ?array
    {
        return $this->dotArrayAccessService::getArray($key, $this->input);
    }

    public function set(int|string|null $key, mixed $value): bool
    {
        return $this->dotArrayAccessService::set($key, $this->input, $value);
    }

    public function setMultiple(array $arrayOfValues): int
    {
        return $this->dotArrayAccessService::setMultiple($this->input, $arrayOfValues);
    }

    public function push(int|string $key, mixed $value): self
    {
        return $this->dotArrayAccessService::push($key, $this->input, $value);
    }

    public function clone(int|string $key, bool $deleteOriginalTwig = false): self
    {
        return $this->dotArrayAccessService::clone($key, $this->input, __CLASS__, $deleteOriginalTwig);
    }


    public function map(int|string $key, callable $callable, bool $recursive = false): self
    {
        return $this->dotArrayAccessService::map($key, $this->input, $callable, __CLASS__, $recursive);
    }

    /*
        public function mapSelf(int|string $key, callable $callable, bool $recursive = false): self
        {
            $this->dotArrayAccessService::map($key, $this->input, $callable, $recursive, __CLASS__);
        }
    */
    public function dotify(): array
    {
        return $this->dotArrayAccessService::dotify($this->input);
    }

    public function toArray(): array
    {
        return $this->input;
    }
}