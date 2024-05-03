<?php

namespace App\Support\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface Deliverable
{
    public function getItemId(): string;

    public function getItem(): ?Model;

    public function getSku(): string;

    public function getName(): string;

    public function getExcerpt(): ?string;
}
