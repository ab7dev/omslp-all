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

namespace Borlabs\Cookie\DtoList\Telemetry;

use Borlabs\Cookie\Dto\Telemetry\DropInDto;
use Borlabs\Cookie\DtoList\AbstractDtoList;

/**
 * @extends AbstractDtoList<DropInDto>
 */
class DropInDtoList extends AbstractDtoList
{
    public const DTO_CLASS = DropInDto::class;

    public function __construct(
        ?array $dropInList = null
    ) {
        parent::__construct($dropInList);
    }

    public static function __listFromJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $dropInData) {
            $className = static::DTO_CLASS;
            $dropIn = new $className();
            $dropIn->fileName = $dropInData->fileName;
            $dropIn->name = $dropInData->name ?? null;
            $dropIn->pluginUrl = $dropInData->pluginUrl ?? null;
            $dropIn->version = $dropInData->version ?? null;

            $list[$key] = $dropIn;
        }

        return $list;
    }

    public static function __listToJson(array $data)
    {
        $list = [];

        foreach ($data as $key => $dropIns) {
            $className = static::DTO_CLASS;
            $list[$key] = $className::prepareForJson($dropIns);
        }

        return $list;
    }
}
