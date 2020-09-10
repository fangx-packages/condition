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

trait EndNodeHelper
{
    protected $field;

    protected $value;

    public function __construct(string $field = '', $value = null)
    {
        $this->setField($field)->setValue($value);
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setField(string $field)
    {
        $this->field = $field;
        return $this;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function children(): array
    {
        return [$this->field => $this->value];
    }
}
