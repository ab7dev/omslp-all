<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Modified by borlabs on 28-April-2026 using {@see https://github.com/BrianHenryIE/strauss}.
 */

use Borlabs\Cookie\Dependencies\Twig\Environment;
use Borlabs\Cookie\Dependencies\Twig\Extension\StringLoaderExtension;
use Borlabs\Cookie\Dependencies\Twig\TemplateWrapper;

/**
 * @internal
 *
 * @deprecated since Twig 3.9
 */
function borlabs_twig_template_from_string(Environment $env, $template, ?string $name = null): TemplateWrapper
{
    trigger_deprecation('twig/twig', '3.9', 'Using the internal "%s" function is deprecated.', __FUNCTION__);

    return StringLoaderExtension::templateFromString($env, $template, $name);
}
