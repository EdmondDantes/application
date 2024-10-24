<?php
declare(strict_types=1);

namespace IfCastle\Application\Bootloader\Builder;

interface ZeroContextInterface
{
    public function getApplicationDirectory(): string;
    
    public function getApplicationType(): string;
    
    /**
     * @return string[]
     */
    public function getExecutionRoles(): array;
    
    public function getRuntimeTags(): array;
}