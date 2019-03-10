<?php

declare(strict_types=1);

namespace Isocontent\Tests\E2E;

use Isocontent\Isocontent;
use Isocontent\Parser\DOMParser;
use PHPUnit\Framework\TestCase;

class ParseHTMLTest extends TestCase
{
    /**
     * @var Isocontent
     */
    private $isocontent;

    public function setUp(): void
    {
        $this->isocontent = new Isocontent(new DOMParser);

        parent::setUp();
    }

    /**
     * @dataProvider dataProvider
     */
    public function test_output_is_conform_to_html(string $htmlInput, array $expectedOutput): void
    {
        $this->assertEquals($expectedOutput, $this->isocontent->buildAST($htmlInput)->toArray());
    }

    public function dataProvider(): array
    {
        return [
            [
                'Foo',
                [[
                    'type' => 'block',
                    'block_type' => 'paragraph',
                    'children' => [['type' => 'text', 'value' => 'Foo']],
                ]]
            ],
            [
                '<span>Foo</span>',
                [[
                    'type' => 'block',
                    'block_type' => 'inline_text',
                    'children' => [['type' => 'text', 'value' => 'Foo']],
                ]]
            ],
            [
                '<dir>Foo</dir>',
                [[
                    'type' => 'block',
                    'block_type' => 'generic',
                    'children' => [['type' => 'text', 'value' => 'Foo']],
                ]]
            ],
            [
                '<p><span>Foo</span></p>',
                [[
                    'type' => 'block',
                    'block_type' => 'paragraph',
                    'children' => [[
                        'type' => 'block',
                        'block_type' => 'inline_text',
                        'children' => [['type' => 'text', 'value' => 'Foo']],
                    ]],
                ]]
            ],
            [
                '<p><span>Foo</span> <span>Bar</span> <span>Baz</span></p>',
                [[
                    'type' => 'block',
                    'block_type' => 'paragraph',
                    'children' => [
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Foo']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Bar']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Baz']],
                        ],
                    ],
                ]]
            ],
            [
                '<p>
                    <span>Foo</span>
                    <span>Bar</span>
                    <span>Baz</span>
                </p>',
                [[
                    'type' => 'block',
                    'block_type' => 'paragraph',
                    'children' => [
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Foo']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Bar']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Baz']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                    ],
                ]]
            ],
            [
                '<p>
                    <span>Foo</span>
                    <span><em>Emphasis</em> text</span>
                    <span>Baz</span>
                </p>',
                [[
                    'type' => 'block',
                    'block_type' => 'paragraph',
                    'children' => [
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Foo']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [
                                [
                                    'type' => 'block',
                                    'block_type' => 'emphasis',
                                    'children' => [['type' => 'text', 'value' => 'Emphasis']],
                                ],
                                ['type' => 'text', 'value' => ' text'],
                            ],
                        ],
                        ['type' => 'text', 'value' => ' '],
                        [
                            'type' => 'block',
                            'block_type' => 'inline_text',
                            'children' => [['type' => 'text', 'value' => 'Baz']],
                        ],
                        ['type' => 'text', 'value' => ' '],
                    ],
                ]]
            ],
        ];
    }
}