<?php

namespace Oro\Bundle\RedirectBundle\Tests\Unit\Entity;

use Oro\Bundle\LocaleBundle\Entity\Localization;
use Oro\Bundle\RedirectBundle\Entity\Redirect;
use Oro\Bundle\RedirectBundle\Entity\Slug;
use Oro\Bundle\ScopeBundle\Entity\Scope;
use Oro\Component\Testing\Unit\EntityTestCaseTrait;

class SlugTest extends \PHPUnit_Framework_TestCase
{
    use EntityTestCaseTrait;
    
    public function testProperties()
    {
        $properties = [
            ['id', 1],
            ['url', '/test/page'],
            ['slugPrototype', 'page'],
            ['routeName', 'oro_cms_page_view'],
            ['routeParameters', ['id' => 1]],
            ['localization', new Localization()]
        ];

        $this->assertPropertyAccessors(new Slug(), $properties);

        $this->assertPropertyCollections(new Slug(), [
            ['redirects', new Redirect()],
            ['scopes', new Scope()]
        ]);
    }

    public function testToString()
    {
        $url = '/test';
        $slug = new Slug();
        $slug->setUrl($url);
        $this->assertEquals($url, (string)$slug);
    }
}
