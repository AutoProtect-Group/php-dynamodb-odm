<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Model;

interface UnitEnumerationInterface extends EnumerationInterface
{
    public function toScalar(): string;

    public static function from(string $key): self;

    public static function tryFrom(string $key): ?self;
}
