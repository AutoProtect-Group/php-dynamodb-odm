<?php

declare(strict_types=1);

namespace Autoprotect\DynamodbODM\Annotation\Types;

interface EnumInterface
{
    /**
     * If it's a backed enum then we need to know if we need to use tryFrom or just from
     *
     * @return bool
     */
    public function isStrict(): bool;
}
