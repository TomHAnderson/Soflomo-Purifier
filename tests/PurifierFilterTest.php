<?php
/**
 * @license See the file LICENSE for copying permission.
 */

namespace Soflomo\Purifier\Test;

use HTMLPurifier;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Soflomo\Purifier\PurifierFilter;

class PurifierFilterTest extends TestCase
{
    /**
     * @var HTMLPurifier|MockObject
     */
    private $htmlPurifier;

    /**
     * @var PurifierFilter
     */
    private $filter;

    public function setUp()
    {
        $this->htmlPurifier = new HTMLPurifier();
        $this->filter       = new PurifierFilter($this->htmlPurifier);
    }

    public function testFilterWithCustomConfig()
    {
        $value = '<p><a>fo<script>alert();</script>obar</a></p>';

        $this->assertSame('<p><a>foobar</a></p>', $this->filter->filter($value));

        $this->filter->setConfig([
            'HTML.AllowedElements' => 'a',
        ]);

        $this->assertSame('<a>foobar</a>', $this->filter->filter($value));
    }

    public function testOptionsCanBeInitializedWithConstructor()
    {
        $options = [
            'config' => [
                'HTML.AllowedElements' => 'a',
                'HTML.DefinitionID'    => 'custom definitions',
                'Cache.DefinitionImpl' => null,
            ],
            'definitions' => [
                'HTML' => [
                    'addAttribute' => [
                        [ 'span', 'foo', 'Text' ],
                    ],
                ],
            ],
        ];
        $filter  = new PurifierFilter($this->htmlPurifier, $options);
        $this->assertEquals($options, $filter->getOptions());
    }

    public function testFilterWithCustomDefinitions()
    {
        $this->filter->setConfig([
            'HTML.DefinitionID'    => 'custom definitions',
            'Cache.DefinitionImpl' => null,
        ]);
        $this->filter->setDefinitions([
            'HTML' => [
                'addAttribute' => [
                    [ 'span', 'foo', 'Text' ],
                ],
            ],
        ]);

        $this->assertSame('<span foo="bar"></span>', $this->filter->filter('<span foo="bar"></span>'));
    }
}
