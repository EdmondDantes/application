<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

interface RequestContextInterface
{
    public function getRemoteAddress(): ?string;
    public function getRemotePort(): ?int;
    public function getRequestTime(): int;
}