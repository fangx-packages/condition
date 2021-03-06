## Fangx's Packages

### Install

Via Composer

```
composer require fangx/condition
```

### Usage

```php

use Fangx\Condition\Condition;
use Fangx\Condition\Fields\ContainsField;
use Fangx\Condition\Fields\EqualsField;
use Fangx\Condition\Fields\ExistsField;
use Fangx\Condition\Fields\GteField;
use Fangx\Condition\Fields\GtField;
use Fangx\Condition\Fields\LteField;
use Fangx\Condition\Fields\LtField;
use Fangx\Condition\Fields\NetworkField;
use Fangx\Condition\Fields\OneOfField;
use Fangx\Condition\Fields\RegexpField;
use Fangx\Condition\Groups\GroupAndNode;
use Fangx\Condition\Groups\GroupNotNode;
use Fangx\Condition\Groups\GroupOrNode;
use PHPUnit\Framework\TestCase;

class ConditionTest extends TestCase
{
    public function testMixNode()
    {
        $node = $this->node();

        $this->assertFalse($node->check());

        $this->assertTrue($node->check(['f-not' => 'ff', 'b-not' => 'bb', 'b-or-1' => 'b1', 'f-or-1' => 'f1', 'ip' => '192.168.128.54']));

        $this->assertEquals('{"and":[{"and":[{"or":[{"equals":{"f-or-1":"f1"}},{"equals":{"f-or-2":"f2"}}]},{"or":[{"equals":{"b-or-1":"b1"}},{"equals":{"b-or-2":"b2"}}]}]},{"and":[{"not":[{"equals":{"f-not":"f"}}]},{"not":[{"equals":{"b-not":"b"}}]}]},{"has_fields":["f-not","b-not"]},{"network":{"ip":"192.168.128.54"}}]}', $node->encode());
    }

    public function testUnpackNode()
    {
        $this->assertEmpty(Condition::unpack([]));

        $node = Condition::unpack($this->node()->pack());

        $this->assertFalse($node->check());

        $this->assertTrue($node->check(['f-not' => 'ff', 'b-not' => 'bb', 'b-or-1' => 'b1', 'f-or-1' => 'f1', 'ip' => '192.168.128.54']));

        $this->assertEquals('{"and":[{"and":[{"or":[{"equals":{"f-or-1":"f1"}},{"equals":{"f-or-2":"f2"}}]},{"or":[{"equals":{"b-or-1":"b1"}},{"equals":{"b-or-2":"b2"}}]}]},{"and":[{"not":[{"equals":{"f-not":"f"}}]},{"not":[{"equals":{"b-not":"b"}}]}]},{"has_fields":["f-not","b-not"]},{"network":{"ip":"192.168.128.54"}}]}', $node->encode());
    }

    public function testDecodeNode()
    {
        $node = Condition::decode($this->node()->encode());

        $this->assertFalse($node->check());

        $this->assertTrue($node->check(['f-not' => 'ff', 'b-not' => 'bb', 'b-or-1' => 'b1', 'f-or-1' => 'f1', 'ip' => '192.168.128.54']));

        $this->assertEquals('{"and":[{"and":[{"or":[{"equals":{"f-or-1":"f1"}},{"equals":{"f-or-2":"f2"}}]},{"or":[{"equals":{"b-or-1":"b1"}},{"equals":{"b-or-2":"b2"}}]}]},{"and":[{"not":[{"equals":{"f-not":"f"}}]},{"not":[{"equals":{"b-not":"b"}}]}]},{"has_fields":["f-not","b-not"]},{"network":{"ip":"192.168.128.54"}}]}', $node->encode());
    }

    public function testGroupAndNode()
    {
        $node = GroupAndNode::create([
            EqualsField::create('f', 'foo'),
            EqualsField::create('b', 'bar'),
        ]);

        $this->assertTrue($node->check(['f' => 'foo', 'b' => 'bar']));
        $this->assertFalse($node->check(['f' => 'foo']));
        $this->assertFalse($node->check(['b' => 'bar']));
        $this->assertFalse($node->check());
        $this->assertEquals('{"and":[{"equals":{"f":"foo"}},{"equals":{"b":"bar"}}]}', $node->encode());
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
        $this->assertFalse($node->check());
        $this->assertEquals('{"or":[{"equals":{"f":"foo"}},{"equals":{"b":"bar"}}]}', $node->encode());
    }

    public function testGroupNotNode()
    {
        $node = GroupNotNode::create([
            EqualsField::create('f', 'foo'),
            EqualsField::create('b', 'bar'),
        ]);

        $this->assertFalse($node->check(['f' => 'foo', 'b' => 'bar']));
        $this->assertFalse($node->check(['f' => 'foo1', 'b' => 'bar']));
        $this->assertTrue($node->check(['f' => 'foo1', 'b' => 'bar1']));
        $this->assertFalse($node->check(['f' => 'foo']));
        $this->assertFalse($node->check(['b' => 'bar']));
        $this->assertTrue($node->check());
        $this->assertEquals('{"not":[{"equals":{"f":"foo"}},{"equals":{"b":"bar"}}]}', $node->encode());
    }

    public function testEqualsNode()
    {
        $node = EqualsField::create('f', 'foo');

        $this->assertTrue($node->check(['f' => 'foo']));
        $this->assertFalse($node->check());
        $this->assertEquals('{"equals":{"f":"foo"}}', $node->encode());
    }

    public function testOneOfNode()
    {
        $node = OneOfField::create('f', ['foo', 'fooo']);

        $this->assertTrue($node->check(['f' => 'foo']));
        $this->assertTrue($node->check(['f' => 'fooo']));
        $this->assertFalse($node->check());
        $this->assertEquals('{"one_of":{"f":["foo","fooo"]}}', $node->encode());
    }

    public function testContainsNode()
    {
        $node = ContainsField::create('f', 'hello');

        $this->assertTrue($node->check(['f' => 'hello world']));
        $this->assertFalse($node->check(['f' => 'hi world']));
        $this->assertFalse($node->check());
        $this->assertEquals('{"contains":{"f":"hello"}}', $node->encode());
    }

    public function testGteNode()
    {
        $node = GteField::create('f', 2);

        $this->assertFalse($node->check(['f' => 1]));
        $this->assertTrue($node->check(['f' => 2]));
        $this->assertTrue($node->check(['f' => 3]));
        $this->assertFalse($node->check());
        $this->assertEquals('{"gte":{"f":2}}', $node->encode());
    }

    public function testGtNode()
    {
        $node = GtField::create('f', 2);

        $this->assertFalse($node->check(['f' => 1]));
        $this->assertFalse($node->check(['f' => 2]));
        $this->assertTrue($node->check(['f' => 3]));
        $this->assertFalse($node->check());
        $this->assertEquals('{"gt":{"f":2}}', $node->encode());
    }

    public function testLteNode()
    {
        $node = LteField::create('f', 2);

        $this->assertTrue($node->check(['f' => 1]));
        $this->assertTrue($node->check(['f' => 2]));
        $this->assertFalse($node->check(['f' => 3]));
        $this->assertFalse($node->check());
        $this->assertEquals('{"lte":{"f":2}}', $node->encode());
    }

    public function testLtNode()
    {
        $node = LtField::create('f', 2);

        $this->assertTrue($node->check(['f' => 1]));
        $this->assertFalse($node->check(['f' => 2]));
        $this->assertFalse($node->check(['f' => 3]));
        $this->assertFalse($node->check());
        $this->assertEquals('{"lt":{"f":2}}', $node->encode());
    }

    public function testRegexpNode()
    {
        $node = RegexpField::create('f', '/^\d$/');

        $this->assertTrue($node->check(['f' => 1]));
        $this->assertTrue($node->check(['f' => '1']));
        $this->assertFalse($node->check(['f' => '1.5']));
        $this->assertFalse($node->check());
        $this->assertEquals('{"regexp":{"f":"\/^\\\d$\/"}}', $node->encode());
    }

    public function testHasFieldsNode()
    {
        $node = ExistsField::create('foo', 'bar');

        $this->assertTrue($node->check(['foo' => 'f', 'bar' => 'b']));
        $this->assertFalse($node->check(['foo' => 'f']));
        $this->assertFalse($node->check(['bar' => 'b']));
        $this->assertEquals('{"has_fields":["foo","bar"]}', $node->encode());

        $node = ExistsField::create(['foo', 'bar']);

        $this->assertTrue($node->check(['foo' => 'f', 'bar' => 'b']));
        $this->assertFalse($node->check(['foo' => 'f']));
        $this->assertFalse($node->check(['bar' => 'b']));
        $this->assertEquals('{"has_fields":["foo","bar"]}', $node->encode());
    }

    public function testNetworkNode()
    {
        $this->assertFalse(NetworkField::create('ip', '192.168.128.54')->check());
        $this->assertFalse(NetworkField::create('ip', '192.168.128.54')->check(['ip' => 'ip_address']));
        $this->assertTrue(NetworkField::create('ip', '192.168.128.54')->check(['ip' => '192.168.128.54']));
        $this->assertFalse(NetworkField::create('ip', '192.168.128.54')->check(['ip' => '192.168.128.53']));
        $this->assertTrue(NetworkField::create('ip', '192.168.128.0/8')->check(['ip' => '192.168.128.54']));
        $this->assertFalse(NetworkField::create('ip', '192.168.128.0/8')->check(['ip' => '192.168.110.54']));
        $this->assertTrue(NetworkField::create('ip', '192.168.0.0/16')->check(['ip' => '192.168.128.54']));
        $this->assertTrue(NetworkField::create('ip', '192.168.0.0/16')->check(['ip' => '192.168.110.54']));
        $this->assertFalse(NetworkField::create('ip', '192.168.0.0/16')->check(['ip' => '192.110.110.54']));
        $this->assertFalse(NetworkField::create('ip', 'ip_address')->check(['ip' => '192.110.110.54']));

        $this->assertEquals('{"network":{"ip":"192.168.128.54"}}', NetworkField::create('ip', '192.168.128.54')->encode());
        $this->assertEquals('{"network":{"ip":"192.168.128.0\/8"}}', NetworkField::create('ip', '192.168.128.0/8')->encode());
    }

    private function node()
    {
        return GroupAndNode::create([
            GroupAndNode::create([
                GroupOrNode::create([
                    EqualsField::create('f-or-1', 'f1'),
                    EqualsField::create('f-or-2', 'f2'),
                ]),
                GroupOrNode::create([
                    EqualsField::create('b-or-1', 'b1'),
                    EqualsField::create('b-or-2', 'b2'),
                ]),
            ]),
            GroupAndNode::create([
                GroupNotNode::create([
                    EqualsField::create('f-not', 'f'),
                ]),
                GroupNotNode::create([
                    EqualsField::create('b-not', 'b'),
                ]),
            ]),
            ExistsField::create('f-not', 'b-not'),
            NetworkField::create('ip', '192.168.128.54'),
        ]);
    }
}

```
