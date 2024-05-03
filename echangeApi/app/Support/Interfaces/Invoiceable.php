<?php

namespace App\Support\Interfaces;

interface Invoiceable
{
    public function getItemId(): string;

    public function getSku(): string;

    public function getName(): string;

    public function getExcerpt(): ?string;
}
