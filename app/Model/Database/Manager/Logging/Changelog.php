<?php

namespace App\Model\Database\Manager\Logging;

use Nette\Utils\Json;
use Nette\Utils\JsonException;

class Changelog
{
    public function __construct(private array $before = [], private array $after = [])
    {

    }

    /**
     * @return array
     */
    public function getBefore(): array
    {
        return $this->before;
    }

    /**
     * @return array
     */
    public function getAfter(): array
    {
        return $this->after;
    }

    /**
     * @throws JsonException
     */
    public function json(): string
    {
        return Json::encode([
            "before" => $this->getBefore(),
            "after" => $this->getAfter(),
        ]);
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return $this->json();
    }
}