<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const NAME = 'kefzce_crypto_currency_exchanges';

    /**
     * @var bool|null
     */
    private $debug;

    /**
     * @param string|null $debug
     */
    public function __construct($debug = null)
    {
        $this->debug = $debug;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $tb = new TreeBuilder(self::NAME, 'variable');
            $rootNode = $tb->getRootNode();
        } else {
            $tb = new TreeBuilder();
            $rootNode = $tb->root(self::NAME, 'variable');
        }

        return $tb;
    }

    /**
     * @internal
     *
     * @param TreeBuilder $builder
     * @param string|null $name
     * @param string      $type
     *
     * @return ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    public static function getRootNodeWithoutDeprecation(TreeBuilder $builder, string $name, string $type = 'array')
    {
        // BC layer for symfony/config 4.1 and older
        return method_exists($builder, 'getRootNode') ? $builder->getRootNode() : $builder->root($name, $type);
    }
}
