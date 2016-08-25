<?php

namespace Oro\Bundle\PricingBundle\Tests\Unit\Form\Type;

use Symfony\Component\Form\PreloadedExtension;

use Oro\Component\Testing\Unit\FormIntegrationTestCase;
use Oro\Bundle\CurrencyBundle\Entity\Price;
use Oro\Bundle\PricingBundle\Form\Type\ProductAttributePriceType;
use Oro\Bundle\PricingBundle\Form\Type\ProductAttributePriceCollectionType;
use Oro\Bundle\PricingBundle\Entity\PriceAttributePriceList;
use Oro\Bundle\PricingBundle\Entity\PriceAttributeProductPrice;
use Oro\Bundle\ProductBundle\Entity\Product;
use Oro\Bundle\ProductBundle\Entity\ProductUnit;
use Oro\Bundle\ProductBundle\Entity\ProductUnitPrecision;
use Oro\Bundle\PricingBundle\Tests\Unit\Form\Extension\Stub\RoundingServiceStub;

class ProductAttributePriceCollectionTypeTest extends FormIntegrationTestCase
{
    /**
     * @return array
     */
    protected function getExtensions()
    {
        $extensions = [
            new PreloadedExtension(
                [
                    ProductAttributePriceCollectionType::NAME => new ProductAttributePriceCollectionType(),
                    ProductAttributePriceType::NAME => new ProductAttributePriceType(new RoundingServiceStub())
                ],
                []
            )
        ];

        return $extensions;
    }

    public function testSubmit()
    {
        $data = [
            (new PriceAttributeProductPrice())->setPrice(Price::create('100', 'USD')),
            (new PriceAttributeProductPrice())->setPrice(Price::create('80', 'EUR')),
        ];
        $form = $this->factory->create(ProductAttributePriceCollectionType::NAME, $data, []);

        $form->submit([
            [ProductAttributePriceType::PRICE => '200'],
            [ProductAttributePriceType::PRICE => '300'],
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->isValid());
    }

    public function testFormViewVariablesAdded()
    {
        $attributePriceList = new PriceAttributePriceList();
        $attributePriceList->addCurrencyByCode('USD')
            ->addCurrencyByCode('EUR')
            ->setName('MAP');

        $product = new Product();
        $product->addUnitPrecision((new ProductUnitPrecision())->setUnit((new ProductUnit())->setCode('item')));

        $price1 = (new PriceAttributeProductPrice())
            ->setPrice(Price::create('100', 'USD'))
            ->setProduct($product)
            ->setPriceList($attributePriceList);

        $price2 = (new PriceAttributeProductPrice())
            ->setPrice(Price::create('80', 'EUR'))
            ->setProduct($product)
            ->setPriceList($attributePriceList);

        $form = $this->factory->create(ProductAttributePriceCollectionType::NAME, [$price1, $price2], []);
        $view = $form->createView();

        $this->assertSame(['EUR', 'USD'], $view->vars['currencies']);
        $this->assertSame(['item'], $view->vars['units']);
        $this->assertSame($attributePriceList->getName(), $view->vars['label']);
    }
}