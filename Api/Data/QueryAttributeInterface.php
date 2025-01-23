<?php

namespace Riverstone\AiContentGenerator\Api\Data;

interface QueryAttributeInterface
{
    public function getValue(): string;
    public function getName(): string;
}
