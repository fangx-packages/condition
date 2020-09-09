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

namespace Fangx\Condition\Contracts;

interface EndNodeInterface extends NodeInterface
{
    public function getField(): string;

    public function getValue();

    public function setField(string $field);

    public function setValue($value);
}
