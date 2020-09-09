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
use Fangx\Condition\Contracts\NodeInterface;

class Condition
{
    // group condition
    const CONDITION_AND = 'and';

    const CONDITION_OR = 'or';

    const CONDITION_NOT = 'not';

    // end condition
    const CONDITION_EQUALS = 'equals';

    const CONDITION_CONTAINS = 'contains';

    const CONDITION_RANGE = 'range';

    const CONDITION_REGEXP = 'regexp';

    const CONDITION_GT = 'gt';

    const CONDITION_GTE = 'gte';

    const CONDITION_LT = 'lt';

    const CONDITION_LTE = 'lte';

    const CONDITION_NETWORK = 'network';

    const CONDITION_HAS_FIELDS = 'has_fields';

    public static function pack(NodeInterface $node)
    {
        $encode = [];
        if ($node instanceof EndNodeInterface) {
            $encode = $node->children();
        } else {
            foreach ($node->children() as $child) {
                /* @var NodeInterface $child */
                $encode[] = static::pack($child);
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

    public static function decode($json)
    {
        return static::unpack(json_decode($json, true));
    }

    public static function unpack($json)
    {
        return $json;
    }
}
