<?php declare(strict_types=1);

namespace LZYT8\BetterInvoice;

use Shopware\Core\Framework\Plugin;

if (file_exists(dirname(__DIR__) . '/vendor/autoload.php'))
    require_once dirname(__DIR__) . '/vendor/autoload.php';

class BetterInvoice extends Plugin { }