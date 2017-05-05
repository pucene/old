<?php

namespace Pucene\Component\Pucene;

interface TermStatisticsInterface
{
    public function documentCount(string $field, string $term);

    public function inverseDocumentFrequency(string $field, string $term);
}
