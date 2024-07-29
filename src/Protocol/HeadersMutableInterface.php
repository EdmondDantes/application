<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

interface HeadersMutableInterface extends HeadersInterface
{
    public function setHeaders(array $headers): static;
    
    public function setHeader(string $header, string|array $value): static;
    
    public function resetHeaders(): static;
}