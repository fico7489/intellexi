<?php

namespace App\ESModule\Interface;

interface IndexInterface{
    public function getClassName(): string;
    public function getConfigModel(): array;
}
