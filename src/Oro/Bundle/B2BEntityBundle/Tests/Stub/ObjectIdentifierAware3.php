<?php

namespace Oro\Bundle\B2BEntityBundle\Tests\Stub;

use Oro\Bundle\B2BEntityBundle\Storage\ObjectIdentifierAwareInterface;
use Oro\Bundle\B2BEntityBundle\Storage\ObjectIdentifierGeneratorTrait;

class ObjectIdentifierAware3 implements ObjectIdentifierAwareInterface
{
    use ObjectIdentifierGeneratorTrait;
}
