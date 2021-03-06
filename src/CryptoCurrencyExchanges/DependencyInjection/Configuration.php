<?php

declare(strict_types=1);

namespace Kefzce\CryptoCurrencyExchanges\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
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
     * @param bool $debug
     */
    public function __construct(bool $debug = false)
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
            $tb->getRootNode();
        } else {
            $tb = new TreeBuilder();
            $tb->root(self::NAME, 'variable');
        }

        return $tb;
    }

    /**
     * @param TreeBuilder $builder
     * @param string      $name
     * @param string      $type
     *
     * @return ArrayNodeDefinition|NodeDefinition
     *
     *@internal
     */
    public static function getRootNodeWithoutDeprecation(TreeBuilder $builder, string $name, string $type = 'array')
    {
        // BC layer for symfony/config 4.1 and older
        return method_exists($builder, 'getRootNode') ? $builder->getRootNode() : $builder->root($name, $type);
    }
}
