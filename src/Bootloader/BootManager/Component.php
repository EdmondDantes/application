<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\BootManager;

final class Component           implements ComponentInterface
{
    private array $groups       = [];
    
    private string $description  = '';
    
    private bool $isActivated    = true;
    
    private bool $isNew;
    
    public function __construct(public string $name, array|null $groups = null)
    {
        if($groups !== null) {
            $this->groups        = $groups;
            $this->isNew         = false;
        } else {
            $this->isNew         = true;
        }
    }
    
    #[\Override]
    public function isNew(): bool
    {
        return $this->isNew;
    }
    
    #[\Override]
    public function isActivated(): bool
    {
        return $this->isActivated;
    }
    
    #[\Override]
    public function activate(): void
    {
        $this->isActivated      = true;
    }
    
    #[\Override]
    public function deactivate(): void
    {
        $this->isActivated      = false;
    }
    
    #[\Override]
    public function defineDescription(string $description): static
    {
        $this->description       = $description;
        
        return $this;
    }
    
    #[\Override]
    public function getDescription(): string
    {
        return $this->description;
    }
    
    #[\Override] public function add(array   $bootloaders,
                                     array   $applications = [],
                                     array   $runtimeTags = [],
                                     array   $excludeTags = [],
                                     bool    $isActive = true,
                                     ?string $group = null
    ): static
    {
        $group                  = $group ?? 'bootloaders'.count($this->groups);
        
        if(array_key_exists($group, $this->groups)) {
            throw new \InvalidArgumentException('Group '.$group.' already exists');
        }
        
        $this->groups[$group]   = [
            'isActive'          => $isActive,
            'bootloader'        => $bootloaders,
            'forApplication'    => $applications,
            'runtimeTags'       => $runtimeTags,
            'excludeTags'       => $excludeTags
        ];
        
        return $this;
    }
    
    #[\Override]
    public function deleteGroup(string $group): static
    {
        if(array_key_exists($group, $this->groups)) {
            unset($this->groups[$group]);
        }
        
        return $this;
    }
    
    #[\Override]
    public function activateGroup(string $group): static
    {
        if(array_key_exists($group, $this->groups)) {
            $this->groups[$group]['isActive'] = true;
        }
        
        return $this;
    }
    
    #[\Override]
    public function deactivateGroup(string $group): static
    {
        if(array_key_exists($group, $this->groups)) {
            $this->groups[$group]['isActive'] = false;
        }
        
        return $this;
    }
    
    #[\Override]
    public function asSaved(): static
    {
        $this->isNew             = false;
        
        return $this;
    }
    
    public function getGroups(): array
    {
        return $this->groups;
    }
}