<?php

declare(strict_types=1);

/**
 * Fangx's Packages
 *
 * @link     https://nfangxu.com
 * @document https://pkg.nfangxu.com
 * @contact  nfangxu@gmail.com
 * @author   nfangxu
 * @license  https://pkg.nfangxu.com/license
 */

namespace Fangx\Tests;

use Fangx\Condition\Condition;
use Fangx\Condition\Fields\EqualsField;
use Fangx\Condition\Groups\GroupAndNode;
use Fangx\Condition\Groups\GroupOrNode;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ConditionTest extends TestCase
{
    public function testGroupAndNode()
    {
        $node = GroupAndNode::create([
            EqualsField::create('f', 'foo'),
            EqualsField::create('b', 'bar'),
        ]);

        $this->assertTrue($node->check(['f' => 'foo', 'b' => 'bar']));
        $this->assertFalse($node->check(['f' => 'foo']));
        $this->assertFalse($node->check(['b' => 'bar']));
        $this->assertEquals('{"and":[{"equals":{"f":"foo"}},{"equals":{"b":"bar"}}]}', Condition::encode($node));
    }

    public function testGroupOrNode()
    {
        $node = GroupOrNode::create([
            EqualsField::create('f', 'foo'),
            EqualsField::create('b', 'bar'),
        ]);

        $this->assertTrue($node->check(['f' => 'foo', 'b' => 'bar']));
        $this->assertTrue($node->check(['f' => 'foo']));
        $this->assertTrue($node->check(['b' => 'bar']));
        $this->assertEquals('{"or":[{"equals":{"f":"foo"}},{"equals":{"b":"bar"}}]}', Condition::encode($node));
    }
}
