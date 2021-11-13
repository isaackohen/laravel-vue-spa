<?php
/**
 * This file is a part of "furqansiddiqui/ethereum-rpc" package.
 * https://github.com/furqansiddiqui/ethereum-rpc
 *
 * Copyright (c) 2020 Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/furqansiddiqui/ethereum-rpc/blob/master/LICENSE
 */

declare(strict_types=1);

namespace EthereumRPC\Contracts\ABI;

/**
 * Class Event
 * @package EthereumRPC\Contracts\ABI
 */
class Event
{
    /** @var string */
    public $name;
    /** @var null|array */
    public $inputs;
    /** @var bool */
    public $anonymous;
}
