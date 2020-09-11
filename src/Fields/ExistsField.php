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

class ExistsField implements EndNodeInterface
{
    use EndNodeHelper;
    use Creator;
    use Packable;

    public function __construct(...$fields)
    {
        if (isset($fields[0]) && is_array($fields[0])) {
            $this->setValue($fields[0]);
        } else {
            $this->setValue($fields);
        }
    }

    public function children(): array
    {
        return $this->getValue();
    }

    public function check($args = []): bool
    {
        foreach ((array)$this->getValue() as $item) {
            if (! isset($args[$item])) {
                return false;
            }
        }

        return true;
    }

    public function condition()
    {
        return Condition::CONDITION_HAS_FIELDS;
    }
}
