<?php

namespace Pucene\Tests\Functional\Comparison;

class GetComparisonTest extends ComparisonTestCase
{
    public function testGet()
    {
        $elasticsearchDocument = $this->elasticsearchIndex->get('my_type', 'Q435');
        $puceneDocument = $this->puceneIndex->get('my_type', 'Q435');

        $this->assertEquals($elasticsearchDocument, $puceneDocument);
    }
}
