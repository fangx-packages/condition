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

namespace Fangx\Condition;

use Fangx\Condition\Contracts\EndNodeInterface;
use Fangx\Condition\Contracts\GroupNodeInterface;
use Fangx\Condition\Contracts\NodeInterface;
use Fangx\Condition\Fields\ContainsField;
use Fangx\Condition\Fields\EqualsField;
use Fangx\Condition\Fields\ExistsField;
use Fangx\Condition\Fields\GteField;
use Fangx\Condition\Fields\GtField;
use Fangx\Condition\Fields\LteField;
use Fangx\Condition\Fields\LtField;
use Fangx\Condition\Fields\NetworkField;
use Fangx\Condition\Fields\RegexpField;
use Fangx\Condition\Groups\GroupAndNode;
use Fangx\Condition\Groups\GroupNotNode;
use Fangx\Condition\Groups\GroupOrNode;

class Condition
{
    // group condition
    const CONDITION_AND = 'and';

    const CONDITION_OR = 'or';

    const CONDITION_NOT = 'not';

    // end condition
    const CONDITION_EQUALS = 'equals';

    const CONDITION_CONTAINS = 'contains';

    const CONDITION_REGEXP = 'regexp';

    const CONDITION_GT = 'gt';

    const CONDITION_GTE = 'gte';

    const CONDITION_LT = 'lt';

    const CONDITION_LTE = 'lte';

    const CONDITION_NETWORK = 'network';

    const CONDITION_HAS_FIELDS = 'has_fields';

    const CONDITION_ONE_OF = 'one_of';

    public static function pack(NodeInterface $node)
    {
        $encode = [];
        if (self::isEndNode($node)) {
            $encode = $node->children();
        } elseif (self::isGroupNode($node)) {
            foreach ($node->children() as $child) {
                if (self::isValidNode($child)) {
                    /* @var NodeInterface $child */
                    $encode[] = static::pack($child);
                }
            }
        }

        return [
            $node->condition() => $encode,
        ];
    }

    public static function encode(NodeInterface $node)
    {
        return json_encode(static::pack($node));
    }

    public static function decode($node)
    {
        return static::unpack(json_decode($node, true));
    }

    public static function unpack(array $node)
    {
        foreach ($node as $condition => $child) {
            if (($class = self::isValidNode($condition)) && self::isGroupNode($condition)) {
                /** @var GroupNodeInterface $c */
                $c = new $class();
                foreach ($child as $item) {
                    $c->append(static::unpack($item));
                }
                return $c;
            }
            if (($class = self::isValidNode($condition)) && self::isEndNode($condition)) {
                if ($condition === Condition::CONDITION_HAS_FIELDS) {
                    return new $class(...$child);
                }
                return new $class(...static::unpack($child));
            }
            return [$condition, $child];
        }

        return null;
    }

    public static function isValidNode($condition)
    {
        if (is_object($condition)) {
            return $condition instanceof NodeInterface;
        }

        return [
            Condition::CONDITION_AND => GroupAndNode::class,
            Condition::CONDITION_OR => GroupOrNode::class,
            Condition::CONDITION_NOT => GroupNotNode::class,
            Condition::CONDITION_EQUALS => EqualsField::class,
            Condition::CONDITION_CONTAINS => ContainsField::class,
            Condition::CONDITION_REGEXP => RegexpField::class,
            Condition::CONDITION_LT => LtField::class,
            Condition::CONDITION_LTE => LteField::class,
            Condition::CONDITION_GT => GtField::class,
            Condition::CONDITION_GTE => GteField::class,
            Condition::CONDITION_NETWORK => NetworkField::class,
            Condition::CONDITION_HAS_FIELDS => ExistsField::class,
        ][$condition] ?? null;
    }

    public static function isGroupNode($condition)
    {
        if (is_object($condition)) {
            return $condition instanceof GroupNodeInterface;
        }

        return in_array($condition, [
            Condition::CONDITION_AND,
            Condition::CONDITION_OR,
            Condition::CONDITION_NOT,
        ]);
    }

    public static function isEndNode($condition)
    {
        if (is_object($condition)) {
            return $condition instanceof EndNodeInterface;
        }

        return in_array($condition, [
            Condition::CONDITION_EQUALS,
            Condition::CONDITION_CONTAINS,
            Condition::CONDITION_REGEXP,
            Condition::CONDITION_LT,
            Condition::CONDITION_LTE,
            Condition::CONDITION_GT,
            Condition::CONDITION_GTE,
            Condition::CONDITION_NETWORK,
            Condition::CONDITION_HAS_FIELDS,
        ]);
    }
}
