<?php

use BitWasp\Bitcoin\Address\AddressCreator;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Script\WitnessScript;
use BitWasp\Bitcoin\Transaction\Factory\SignData;
use BitWasp\Bitcoin\Transaction\Factory\Signer;
use BitWasp\Bitcoin\Transaction\TransactionFactory;
use BitWasp\Bitcoin\Transaction\TransactionOutput;

require __DIR__ . "/../../../vendor/autoload.php";

/**
 * This example shows a 2-of-2 P2WSH multisig
 * output being spent, sending some to another
 * address, and the rest to our own address again (the change)
 *
 * We use the WitnessScript to decorate the multisig
 * script so we can create the output script/address
 * easily.
 *
 * The witnessScript is assigned to a SignData instance
 * because the unsigned transaction doesn't have the
 * witnessScript yet.
 */


$privKeyFactory = new PrivateKeyFactory();
$privateKey1 = $privKeyFactory->fromHexCompressed('0ad53d138174a027cef804b9416c8637cb159ee02b4c80ae6be4ca711275df33', true);
$publicKey1 = $privateKey1->getPublicKey();

$privateKey2 = $privKeyFactory->fromHexCompressed("efe0f32bfcd7179a8b071e96d9f91b877432cb843775f2f80341ee022341b36d", true);
$publicKey2 = $privateKey2->getPublicKey();

// The witnessScript needs to be known when spending
$witnessScript = new WitnessScript(
    ScriptFactory::scriptPubKey()->multisig(2, [$publicKey1, $publicKey2])
);

$spendFromAddress = $witnessScript->getAddress();
$addressCreator = new AddressCreator();
$sendToAddress = $addressCreator->fromString('TMrHAprLZ98oR24SBe2eufxJGpf7qdUraH');
echo "Spend from {$spendFromAddress->getAddress()}\n";
echo "Send to {$sendToAddress->getAddress()}\n";

$addressCreator = new AddressCreator();
$transaction = TransactionFactory::build()
    ->input('87f7b7639d132e9817f58d3fe3f9f65ff317dc780107a6c10cba5ce2ad1e4ea1', 0)
    ->payToAddress(1500000, $sendToAddress)
    ->payToAddress(12345123, $spendFromAddress) // don't forget your change output!
    ->get();

$txOut = new TransactionOutput(1501000, $witnessScript->getOutputScript());
$signData = (new SignData())
    ->p2wsh($witnessScript)
;

$signer = new Signer($transaction);
$input = $signer->input(0, $txOut, $signData);
$input->sign($privateKey1);
$input->sign($privateKey2);

$signed = $signer->get();

echo "txid: {$signed->getTxId()->getHex()}\n";
echo "raw: {$signed->getHex()}\n";
echo "input valid? " . ($input->verify() ? "true" : "false") . PHP_EOL;
