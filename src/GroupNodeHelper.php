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

use Fangx\Condition\Contracts\GroupNodeInterface;
use Fangx\Condition\Contracts\NodeInterface;

trait GroupNodeHelper
{
    protected $children = [];

    public function __construct($children = [])
    {
        $this->children = $children;
    }

    public function children(): array
    {
        return $this->children;
    }

    public function append(NodeInterface $node): GroupNodeInterface
    {
        $this->children[] = $node;
        return $this;
    }
}
