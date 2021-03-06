<?php

namespace Oro\Bundle\PaymentBundle\Tests\Unit\Entity;

use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\RuleBundle\Entity\Rule;
use Oro\Bundle\PaymentBundle\Entity\PaymentMethodConfig;
use Oro\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRuleDestination;
use Oro\Bundle\PaymentBundle\Entity\PaymentMethodsConfigsRule;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;

class PaymentMethodsConfigsRuleTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;

    public function testAccessors()
    {
        $properties = [
            ['id', '1'],
            ['rule', new Rule()],
            ['currency', 'USD'],
            ['organization', new Organization()],
        ];

        $entity = new PaymentMethodsConfigsRule();

        $this->assertPropertyAccessors($entity, $properties);
        $this->assertPropertyCollection($entity, 'methodConfigs', new PaymentMethodConfig());
        $this->assertPropertyCollection($entity, 'destinations', new PaymentMethodsConfigsRuleDestination());
    }
}
