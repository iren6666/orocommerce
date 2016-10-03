<?php

namespace Oro\Bundle\AccountBundle\Async\Visibility;

use Oro\Bundle\AccountBundle\Entity\Visibility\CategoryVisibility;
use Oro\Bundle\AccountBundle\Model\MessageFactoryInterface;
use Oro\Bundle\AccountBundle\Visibility\Cache\CacheBuilderInterface;
use Oro\Bundle\ProductBundle\Model\ProductMessageHandler;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CategoryVisibilityProcessor extends AbstractVisibilityProcessor
{

    /**
     * @var ProductMessageHandler
     */
    protected $productMessageHandler;

    /**
     * @param RegistryInterface $registry
     * @param MessageFactoryInterface $messageFactory
     * @param LoggerInterface $logger
     * @param CacheBuilderInterface $cacheBuilder
     * @param ProductMessageHandler $productMessageHandler
     */
    public function __construct(
        RegistryInterface $registry,
        MessageFactoryInterface $messageFactory,
        LoggerInterface $logger,
        CacheBuilderInterface $cacheBuilder,
        ProductMessageHandler $productMessageHandler
    ) {
        parent::__construct($registry, $messageFactory, $logger, $cacheBuilder);
        $this->productMessageHandler = $productMessageHandler;
    }

    /**
     * @param object|CategoryVisibility $entity
     */
    protected function resolveVisibilityByEntity($entity)
    {
        $this->cacheBuilder->resolveVisibilitySettings($entity);
    }
}