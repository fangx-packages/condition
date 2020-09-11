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

class NetworkField implements EndNodeInterface
{
    use EndNodeHelper;
    use Creator;
    use Packable;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function check($args = []): bool
    {
        if (! isset($args[$this->getField()]) || ! filter_var($args[$this->getField()], FILTER_VALIDATE_IP)) {
            return false;
        }

        if (filter_var($this->getValue(), FILTER_VALIDATE_IP)) {
            return $args[$this->getField()] === $this->getValue();
        }

        $ip = (float)(sprintf('%u', ip2long($args[$this->getField()])));

        [$start, $end] = $this->getNetworkRange();

        return $ip >= $start && $ip <= $end;
    }

    public function condition()
    {
        return Condition::CONDITION_NETWORK;
    }

    protected function getNetworkRange()
    {
        $parse = explode('/', $this->getValue());
        if (! isset($parse[1])) {
            return [0, 0];
        }
        $start = (float)(sprintf('%u', ip2long($parse[0])));
        $len = pow(2, 32 - $parse[1]);
        $end = $start + $len - 1;

        return [$start, $end];
    }
}
