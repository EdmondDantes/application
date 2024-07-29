<?php
declare(strict_types=1);

namespace IfCastle\Application\Environment;

use IfCastle\DI\ContainerInterface;

interface EnvironmentInterface      extends ContainerInterface
{
    public function get(string $key): mixed;
    
    public function isExist(string $key): bool;
    
    public function find(string ...$path): mixed;
    
    public function is(string ...$path): bool;
    
    public function set(string $key, mixed $value): static;
    
    public function del(string $key): static;
    
    public function destroy(string $key): static;
    
    public function merge(array $data): static;
    
    public function getParentEnvironment(): ?EnvironmentInterface;
}