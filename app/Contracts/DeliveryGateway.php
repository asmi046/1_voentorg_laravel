<?php

namespace App\Contracts;

interface DeliveryGateway
{
    public function listPoints(array $context): array;

    public function quote(array $context): array;
}
