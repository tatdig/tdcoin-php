<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Tests;

use BitWasp\Bitcoin\Address\AddressCreator;
use BitWasp\Bitcoin\Amount;
use BitWasp\Bitcoin\Uri;

class UriTest extends AbstractTestCase
{
    public function testDefault()
    {
        $string = 'TUj6nE7adBhyRithAM55aAwKtsGmNzoyZn';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);
        $this->assertEquals('bitcoin:'.$string, $uri->uri());
    }

    public function testAmount()
    {
        $string = 'TZCQrBK4JqJfDgYokpu3ZopL7dYURWxAwQ';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);

        $amount = new Amount();
        $uri->setAmount($amount, 1);

        $this->assertEquals('bitcoin:'.$string."?amount=0.00000001", $uri->uri());
    }

    public function testAmountBtc()
    {
        $string = 'TWr4oiRi6QfK4iajq4kgbukpj4V385SUSF';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);

        $uri->setAmountBtc('1');

        $this->assertEquals('bitcoin:'.$string."?amount=1", $uri->uri());
    }

    public function testLabel()
    {
        $string = 'TVhqFNFN2XfVPVgYsuTPRUo8xZwXBMimNn';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);
        $uri->setLabel('this is the label');

        $this->assertEquals('bitcoin:'.$string."?label=this+is+the+label", $uri->uri());
    }

    public function testMessage()
    {
        $string = 'TAyi3q829f4S3Xmnu6jz1Ac7XSP6pSCUcf';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);
        $uri->setMessage('this is the label');

        $this->assertEquals('bitcoin:'.$string."?message=this+is+the+label", $uri->uri());
    }

    public function testRequestUrl()
    {
        $string = 'TTqfvxaCAf48iytdB7qUoB64HxKaS2vZXr';
        $addrCreator = new AddressCreator();
        $address = $addrCreator->fromString($string);
        $uri = new Uri($address);
        $uri->setRequestUrl('https://example.com/request');

        $this->assertEquals('bitcoin:'.$string.'?r=https%3A%2F%2Fexample.com%2Frequest', $uri->uri());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBip21MustProvideAddress()
    {
        $address = null;
        new Uri($address);
    }

    public function testBip72Incompatible()
    {
        $address = null;
        $uri = new Uri($address, Uri::BIP0072);
        $uri->setRequestUrl('https://example.com/request');

        $this->assertEquals('bitcoin:?r=https%3A%2F%2Fexample.com%2Frequest', $uri->uri());
    }
}
