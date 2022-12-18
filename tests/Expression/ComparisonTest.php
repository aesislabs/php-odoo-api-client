<?php

namespace Aesislabs\Component\Odoo\Tests\Expression;

use Aesislabs\Component\Odoo\DBAL\Expression\Comparison;
use ReflectionException;

/**
 * @coversDefaultClass \Aesislabs\Component\Odoo\DBAL\Expression\Comparison
 */
class ComparisonTest extends AbstractDomainTest
{
    /**
     * @covers ::setFieldName
     * @covers ::getFieldName
     * @covers ::setOperator
     * @covers ::getOperator
     * @covers ::setValue
     * @covers ::getValue
     *
     * @throws ReflectionException
     */
    public function testAccessorsAndMutators(): void
    {
        $comparison = new Comparison('foo', Comparison::EQUAL_TO, 'bar');

        $this
            ->createObjectTester($comparison)
            ->assertPropertyAccessorsAndMutators('fieldName', 'bar')
            ->assertPropertyAccessorsAndMutators('operator', Comparison::NOT_EQUAL_TO)
            ->assertPropertyAccessorsAndMutators('value', 'mixed')
        ;
    }

    /**
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $comparison = new Comparison('foo', Comparison::EQUAL_TO, 'bar');

        $this->assertEquals(['foo', '=', 'bar'], $comparison->toArray());
    }
}
