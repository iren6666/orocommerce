<?php

namespace Oro\Bundle\ActionBundle\Tests\Functional\Controller;

use Oro\Bundle\ActionBundle\Configuration\ActionConfigurationProvider;
use Oro\Bundle\CacheBundle\Provider\FilesystemCache;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class WidgetControllerTest extends WebTestCase
{
    const ENTITY_CLASS = 'Oro\Bundle\TestFrameworkBundle\Entity\Product';

    /** @var FilesystemCache */
    protected $cacheProvider;

    protected function setUp()
    {
        $this->initClient();

        $this->cacheProvider = $this->getContainer()->get('oro_action.cache.provider');
    }

    protected function tearDown()
    {
        $this->cacheProvider->delete(ActionConfigurationProvider::ROOT_NODE_NAME);
    }

    /**
     * @dataProvider buttonsActionDataProvider
     *
     * @param array $config
     * @param $route
     * @param $entityId
     * @param $entityClass
     * @param $expected
     */
    public function testButtonsActionForRoutes(array $config, $route, $entityId, $entityClass, $expected)
    {
        $this->cacheProvider->save(ActionConfigurationProvider::ROOT_NODE_NAME, $config);

        $crawler = $this->client->request(
            'GET',
            $this->getUrl(
                'oro_action_widget_buttons',
                [
                    '_widgetContainer' => 'dialog',
                    'route' => $route,
                    'entityId' => $entityId,
                    'entityClass' => $entityClass,
                ]
            )
        );

        $result = $this->client->getResponse();

        $this->assertHtmlResponseStatusCodeEquals($result, 200);

        if ($expected) {
            $this->assertContains($expected, $crawler->html());
        } else {
            $this->assertEmpty($crawler);
        }
    }

    /**
     * @return array
     */
    public function buttonsActionDataProvider()
    {
        $label = 'oro.action.test.label';

        $config = [
            'oro_action_test_action' => [
                'label' => $label,
                'enabled' => true,
                'order' => 10,
                'applications' => ['backend', 'frontend'],
                'frontend_options' => [],
                'entities' => [],
                'routes' => []
            ]
        ];

        return [
            [
                'config' => array_merge_recursive(
                    $config,
                    ['oro_action_test_action' => ['entities' => [self::ENTITY_CLASS]]]
                ),
                'route' => 'oro_action_test_route',
                'entityId' => 42,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => $label
            ],
            [
                'config' => array_merge_recursive(
                    $config,
                    ['oro_action_test_action' => ['entities' => ['Oro\Bundle\ActionBundle\Entity\UnknownEntity']]]
                ),
                'route' => 'oro_action_test_route',
                'entityId' => 42,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => false
            ],
            [
                'config' => array_merge_recursive(
                    $config,
                    ['oro_action_test_action' => ['routes' => ['oro_action_test_route']]]
                ),
                'route' => 'oro_action_test_route',
                'entityId' => 42,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => $label
            ],
            [
                'config' => array_merge_recursive(
                    $config,
                    ['oro_action_test_action' => ['routes' => ['oro_action_unknown_route']]]
                ),
                'route' => 'oro_action_test_route',
                'entityId' => 42,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => false
            ],
            [
                'config' => $config,
                'route' => 'oro_action_test_route',
                'entityId' => 42,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => false
            ],
            [
                'config' => array_merge_recursive(
                    $config,
                    ['oro_action_test_action' =>
                        [
                            'entities' => [self::ENTITY_CLASS],
                            'routes' => ['oro_action_test_route']
                        ]
                    ]
                ),
                'route' => 'oro_action_test_route',
                'entityId' => null,
                'entityClass' => self::ENTITY_CLASS,
                'expected' => $label
            ]
        ];
    }
}