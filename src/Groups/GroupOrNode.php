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

namespace Fangx\Condition\Groups;

use Fangx\Condition\Condition;
use Fangx\Condition\Contracts\GroupNodeInterface;
use Fangx\Condition\Contracts\NodeInterface;
use Fangx\Condition\Creator;
use Fangx\Condition\GroupNodeHelper;

class GroupOrNode implements GroupNodeInterface
{
    use GroupNodeHelper;
    use Creator;

    public function check($args = []): bool
    {
        foreach ($this->children() as $child) {
            /** @var NodeInterface $child */
            if ($child->check($args)) {
                return true;
            }
        }

        return false;
    }

    public function condition()
    {
        return Condition::CONDITION_OR;
    }
}
