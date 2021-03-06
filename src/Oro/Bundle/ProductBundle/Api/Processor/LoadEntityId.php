<?php

namespace Oro\Bundle\ProductBundle\Api\Processor;

use Oro\Bundle\ApiBundle\Processor\SingleItemContext;
use Oro\Component\ChainProcessor\ContextInterface;
use Oro\Component\ChainProcessor\ProcessorInterface;

class LoadEntityId implements ProcessorInterface
{
    const METHOD = 'getId';

    /**
     * {@inheritdoc}
     */
    public function process(ContextInterface $context)
    {
        if (!$context instanceof SingleItemContext) {
            return;
        }

        if (null !== $context->getId()) {
            return;
        }

        $entity = $context->getResult();
        if (is_object($entity) && method_exists($entity, self::METHOD)) {
            $method = self::METHOD;
            $context->setId($entity->$method());
        }
    }
}
