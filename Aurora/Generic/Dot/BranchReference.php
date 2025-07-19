<?php
/**
 *
 * Project: Aurora
 * @author: Graffiti Concept <aurora.github@gmail.com>
 * Created by PhpStorm 17 Jul 2025 at 15:04 CET.
 */


namespace Aurora\Generic\Dot;

class BranchReference
{
    private string $branchKey = '';
    private array $branchRoot = [];

    public function initialize(string $branchKey, array &$branchRoot): self
    {
        $this->branchKey = $branchKey;
        $this->branchRoot = &$branchRoot;

        return $this;
    }

    public function getBranchKey(): string
    {
        return $this->branchKey;
    }

    public function &getBranch(): array
    {
        return is_array($this->branchRoot[$this->branchKey]) ? $this->branchRoot[$this->branchKey] : [$this->branchKey => $this->branchRoot[$this->branchKey]];
    }

    public function getBranchValue(): mixed
    {
        return $this->branchRoot[$this->branchKey] ?? null;
    }

    public function &getBranchRoot(): array
    {
        return $this->branchRoot;
    }
}