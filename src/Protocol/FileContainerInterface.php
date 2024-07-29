<?php
declare(strict_types=1);

namespace IfCastle\Application\Protocol;

use IfCastle\DI\DisposableInterface;

interface FileContainerInterface    extends DisposableInterface
{
    public function getFileName(): string;
    
    public function getMimeType(): ?string;
    
    public function getFileSize(): int;
    
    public function getContents(): string;
    
    public function getStream(): ?ReadableStream;
    
    public function flushTo(string $fileName): static;
    
    public function isEmpty(): bool;
    
    public function isStream(): bool;
    
    public function getError(): ?\Throwable;
}