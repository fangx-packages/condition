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

namespace Fangx\Condition\Fields;

use Fangx\Condition\Condition;
use Fangx\Condition\Contracts\EndNodeInterface;
use Fangx\Condition\Creator;
use Fangx\Condition\EndNodeHelper;
use Fangx\Condition\Packable;

class LteField implements EndNodeInterface
{
    use EndNodeHelper;
    use Creator;
    use Packable;

    public function check($args = []): bool
    {
        if (! isset($args[$this->getField()])) {
            return false;
        }

        return $args[$this->getField()] <= $this->getValue();
    }

    public function condition()
    {
        return Condition::CONDITION_LTE;
    }
}
