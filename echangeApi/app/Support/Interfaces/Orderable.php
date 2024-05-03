<?php

namespace App\Support\Interfaces;

/**
 * @param  mixed  $product
 */
interface Orderable
{
    public function getItemId(): string;

    public function getSku(): string;

    public function getName(): string;

    public function getExcerpt(): ?string;

    public function getProductId(): string;
}
