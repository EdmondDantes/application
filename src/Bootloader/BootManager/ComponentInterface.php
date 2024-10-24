<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

interface ComponentInterface
{
    public function defineDescription(string $description): static;
    
    public function getDescription(): string;
    
    public function isNew(): bool;
    
    public function isActivated(): bool;
    
    public function activate(): void;
    
    public function deactivate(): void;
    
    public function getGroups(): array;
    
    public function add(
        array  $bootloaders,
        array  $applications    = [],
        array  $runtimeTags     = [],
        array  $excludeTags     = [],
        bool    $isActive       = true,
        ?string $group          = null
    ): static;
    
    public function deleteGroup(string $group): static;
    
    public function activateGroup(string $group): static;
    
    public function deactivateGroup(string $group): static;
    
    public function asSaved(): static;
}