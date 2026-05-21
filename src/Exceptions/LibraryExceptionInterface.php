<?php

declare(strict_types=1);

namespace ConstupFoss\SymfonyAwsSecretsBundle\Exceptions;

use Throwable;

interface LibraryExceptionInterface extends Throwable
{
    public function getType(): string;

    public function getDebugMessage(): ?string;

    public function getLibraryName(): string;
}