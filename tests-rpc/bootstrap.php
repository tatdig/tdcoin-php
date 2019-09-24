<?php

$loader = @include __DIR__ . '/../vendor/autoload.php';

if (!$loader) {
    $loader = require __DIR__ . '/../../../../vendor/autoload.php';
}

\BitWasp\Bitcoin\Crypto\EcAdapter\EcSerializer::disableCache();

$net = BitWasp\Bitcoin\Network\NetworkFactory::tdcoin();
BitWasp\Bitcoin\Bitcoin::setNetwork($net);

$loader->addPsr4('BitWasp\\Bitcoin\\RpcTests\\', __DIR__);
