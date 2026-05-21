<?php

declare(strict_types=1);

namespace ConstupFoss\SymfonyAwsSecretsBundle\Exceptions;

use Exception;

abstract class LibraryException extends Exception implements LibraryExceptionInterface
{
    protected string $type;
    protected ?string $debugMessage;
    protected string $libraryName = 'constup-foss/symfony-aws-secrets-bundle';

    public function getType(): string
    {
        return $this->type;
    }

    public function getDebugMessage(): ?string
    {
        return $this->debugMessage;
    }

    public function getLibraryName(): string
    {
        return $this->libraryName;
    }
}