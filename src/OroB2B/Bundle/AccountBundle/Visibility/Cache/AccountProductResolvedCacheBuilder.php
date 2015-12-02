<?php

namespace OroB2B\Bundle\AccountBundle\Visibility\Cache;

use OroB2B\Bundle\AccountBundle\Entity\Visibility\AccountProductVisibility;
use OroB2B\Bundle\AccountBundle\Entity\VisibilityResolved\AccountProductVisibilityResolved;
use OroB2B\Bundle\AccountBundle\Entity\VisibilityResolved\BaseProductVisibilityResolved;
use OroB2B\Bundle\CatalogBundle\Entity\Category;
use OroB2B\Bundle\ProductBundle\Entity\Product;
use OroB2B\Bundle\WebsiteBundle\Entity\Website;

class AccountProductResolvedCacheBuilder extends AbstractCacheBuilder implements CacheBuilderInterface
{
    /**
     * @param AccountProductVisibility $accountProductVisibility
     */
    public function resolveVisibilitySettings($accountProductVisibility)
    {
        $product = $accountProductVisibility->getProduct();
        $website = $accountProductVisibility->getWebsite();
        $account = $accountProductVisibility->getAccount();

        $selectedVisibility = $accountProductVisibility->getVisibility();

        $em = $this->registry->getManagerForClass(
            'OroB2BAccountBundle:VisibilityResolved\AccountProductVisibilityResolved'
        );
        $accountProductVisibilityResolved = $em
            ->getRepository('OroB2BAccountBundle:VisibilityResolved\AccountProductVisibilityResolved')
            ->findOneBy(['product' => $product, 'website' => $website, 'account' => $account]);

        if (!$accountProductVisibilityResolved && $selectedVisibility !== AccountProductVisibility::ACCOUNT_GROUP) {
            $accountProductVisibilityResolved = new AccountProductVisibilityResolved($website, $product, $account);
            $em->persist($accountProductVisibilityResolved);
        }

        if ($selectedVisibility == AccountProductVisibility::CATEGORY) {
            $category = $this->registry
                ->getManagerForClass('OroB2BCatalogBundle:Category')
                ->getRepository('OroB2BCatalogBundle:Category')
                ->findOneByProduct($product);
            if (!$category instanceof Category) {
                $accountProductVisibilityResolved->setVisibility($this->getVisibilityFromConfig($this->configManager));
                $accountProductVisibilityResolved->setSourceProductVisibility(null);
                $accountProductVisibilityResolved->setSource(null);
                $accountProductVisibilityResolved->setCategoryId(null);

                return;
            }
            $accountProductVisibilityResolved->setVisibility(
                $this->categoryVisibilityResolver->getCategoryVisibilityForAccount($category, $account)
            );
            $accountProductVisibilityResolved->setSourceProductVisibility($accountProductVisibility);
            $accountProductVisibilityResolved->setSource(BaseProductVisibilityResolved::SOURCE_CATEGORY);
            $accountProductVisibilityResolved->setCategoryId($category->getId());
        } elseif ($selectedVisibility == AccountProductVisibility::CURRENT_PRODUCT) {
            $accountProductVisibilityResolved->setSource(BaseProductVisibilityResolved::SOURCE_STATIC);
            $productVisibilityResolved = $this->registry
                ->getManagerForClass('OroB2BAccountBundle:VisibilityResolved\ProductVisibilityResolved')
                ->getRepository('OroB2BAccountBundle:VisibilityResolved\ProductVisibilityResolved')
                ->findOneBy(['product' => $product, 'website' => $website]);
            if (!$productVisibilityResolved) {
                $accountProductVisibilityResolved->setVisibility($this->getVisibilityFromConfig($this->configManager));
                $accountProductVisibilityResolved->setSourceProductVisibility(null);

                return;
            }
            $accountProductVisibilityResolved->setVisibility($productVisibilityResolved->getVisibility());
            $accountProductVisibilityResolved->setSourceProductVisibility(
                $productVisibilityResolved->getSourceProductVisibility()
            );
        } else {
            $this->resolveStaticValues(
                $accountProductVisibility,
                $accountProductVisibilityResolved,
                $selectedVisibility
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibilitySettingsSupported($visibilitySettings)
    {
        return $visibilitySettings instanceof AccountProductVisibility;
    }

    /**
     * {@inheritdoc}
     */
    public function updateResolvedVisibilityByCategory(Category $category)
    {
        // TODO: Implement updateResolvedVisibilityByCategory() method.
    }

    /**
     * {@inheritdoc}
     */
    public function updateProductResolvedVisibility(Product $product)
    {
        // TODO: Implement updateProductResolvedVisibility() method.
    }

    /**
     * {@inheritdoc}
     */
    public function buildCache(Website $website = null)
    {
        // TODO: Implement buildCache() method.
    }
}
