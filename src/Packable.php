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

trait Packable
{
    public function pack()
    {
        return Condition::pack($this);
    }

    public function encode()
    {
        return Condition::encode($this);
    }
}
