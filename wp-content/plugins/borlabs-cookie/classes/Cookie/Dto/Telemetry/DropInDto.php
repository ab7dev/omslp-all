<?php
/*
 *  Copyright (c) 2026 Borlabs GmbH. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs GmbH, https://borlabs.io
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dto\Telemetry;

use Borlabs\Cookie\Dto\AbstractDto;

class DropInDto extends AbstractDto
{
    public string $fileName;

    public ?string $name = null;

    public ?string $pluginUrl = null;

    public ?string $version = null;
}
