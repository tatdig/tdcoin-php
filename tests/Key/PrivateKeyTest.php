<?php

declare(strict_types=1);

namespace BitWasp\Bitcoin\Tests\Key;

use BitWasp\Bitcoin\Crypto\EcAdapter\Adapter\EcAdapterInterface;
use BitWasp\Bitcoin\Crypto\Random\Random;
use BitWasp\Bitcoin\Key\Factory\PrivateKeyFactory;
use BitWasp\Bitcoin\Network\NetworkFactory;
use BitWasp\Bitcoin\Tests\AbstractTestCase;

class PrivateKeyTest extends AbstractTestCase
{
    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testCreatePrivateKey(EcAdapterInterface $ecAdapter)
    {
        $hex = '4141414141414141414141414141414141414141414141414141414141414141';

        $factory = new PrivateKeyFactory($ecAdapter);
        $privateKey = $factory->fromHexUncompressed($hex);

        $this->assertSame($privateKey->getBuffer()->getHex(), '4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertFalse($privateKey->isCompressed());
        $this->assertTrue($privateKey->isPrivate());
        $this->assertSame(
            '04eec7245d6b7d2ccb30380bfbe2a3648cd7a942653f5aa340edcea1f2836866198bd9fc8678e246f23f40bfe8d928d3f37a51642aed1d5b471a1a0db4f71891ea',
            $privateKey->getPublicKey()->getBuffer()->getHex()
        );
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testCreatePrivateKeyCompressed(EcAdapterInterface $ecAdapter)
    {
        $hex = '4141414141414141414141414141414141414141414141414141414141414141';

        $factory = new PrivateKeyFactory($ecAdapter);
        $privateKey = $factory->fromHexCompressed($hex);

        $this->assertSame($privateKey->getBuffer()->getHex(), '4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertTrue($privateKey->isCompressed());
        $this->assertTrue($privateKey->isPrivate());
        $this->assertSame(
            '02eec7245d6b7d2ccb30380bfbe2a3648cd7a942653f5aa340edcea1f283686619',
            $privateKey->getPublicKey()->getBuffer()->getHex()
        );
    }

    /**
     * @dataProvider getEcAdapters
     * @expectedException \Exception
     * @param EcAdapterInterface $ecAdapter
     */
    public function testCreatePrivateKeyFailure(EcAdapterInterface $ecAdapter)
    {
        $hex = 'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFEBAAEDCE6AF48A03BBFD25E8CD0364141';
        $factory = new PrivateKeyFactory($ecAdapter);
        $factory->fromHexCompressed($hex);
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testGenerateNewUncompressed(EcAdapterInterface $ecAdapter)
    {
        $factory = new PrivateKeyFactory($ecAdapter);
        $privateKey = $factory->generateUncompressed(new Random());
        $this->assertFalse($privateKey->isCompressed());
        $this->assertTrue($privateKey->isPrivate());
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testIsCompressed(EcAdapterInterface $ecAdapter)
    {
        $random = new Random();
        $factory = new PrivateKeyFactory($ecAdapter);
        $key = $factory->generateCompressed($random);
        $this->assertTrue($key->isCompressed());

        $key = $factory->generateUncompressed($random);
        $this->assertFalse($key->isCompressed());
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testGenerateNewCompressed(EcAdapterInterface $ecAdapter)
    {
        $factory = new PrivateKeyFactory($ecAdapter);
        $privateKey = $factory->generateCompressed(new Random());
        $this->assertTrue($privateKey->isCompressed());
        $this->assertTrue($privateKey->isPrivate());
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testGetWif(EcAdapterInterface $ecAdapter)
    {
        $network = NetworkFactory::tdcoin();
        $privKeyFactory = new PrivateKeyFactory($ecAdapter);

        $privateKey = $privKeyFactory->fromHexUncompressed('4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertSame($privateKey->toWif($network), '4bVPj3gtwxzPTmoMNPVYSNktCcBuMMLssKP4GKbrZQEAWz6293R');
        $this->assertSame($privateKey->toWif(), '4bVPj3gtwxzPTmoMNPVYSNktCcBuMMLssKP4GKbrZQEAWz6293R');

        $privateKey = $privKeyFactory->fromHexCompressed('4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertSame($privateKey->toWif($network), 'GsEKJas6Hc8J9AjauQGym12gzEK84pnjX68MQd6qk1X715sivcxq');
        $this->assertSame($privateKey->toWif(), 'GsEKJas6Hc8J9AjauQGym12gzEK84pnjX68MQd6qk1X715sivcxq');
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testGetPubKeyHash(EcAdapterInterface $ecAdapter)
    {
        $keyFactory = new PrivateKeyFactory($ecAdapter);

        $privateKey = $keyFactory->fromHexUncompressed('4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertSame('d00baafc1c7f120ab2ae0aa22160b516cfcf9cfe', $privateKey->getPubKeyHash()->getHex());

        $privateKey = $keyFactory->fromHexCompressed('4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertSame('c53c82d3357f1f299330d585907b7c64b6b7a5f0', $privateKey->getPubKeyHash()->getHex());
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testSerialize(EcAdapterInterface $ecAdapter)
    {
        $keyFactory = new PrivateKeyFactory($ecAdapter);
        $privateKey = $keyFactory->fromHexUncompressed('4141414141414141414141414141414141414141414141414141414141414141');
        $this->assertSame('4141414141414141414141414141414141414141414141414141414141414141', $privateKey->getBuffer()->getHex());
    }

    /**
     * @dataProvider getEcAdapters
     * @param EcAdapterInterface $ecAdapter
     */
    public function testFromWif(EcAdapterInterface $ecAdapter)
    {
        $math = $ecAdapter->getMath();
        $regular = array(
            '4cHSrU1JYFNSF62RrKu9Cid7nEfNu7bX75sohBRhZtZLRyAEpsM' => 'f0e4c2f76c58916ec258f246851bea091d14d4247a2fc3e18694461b1816e13b',
            '4cWF9hJtH7yMe5sWYwkUngaLCJzgj6y434Aoqv3wvozT1iSKis9' => '2413fb3709b05939f04cf2e92f7d0897fc2596f9ad0b8a9ea855c7bfebaae892',
            '4bnDHpZKE77DZM7KrbzUZQp5W8JjagH7jXsVCwoAP93vS89msai' => '421c76d77563afa1914846b010bd164f395bd34c2102e5e99e0cb9cf173c1d87'
        );

        $factory = new PrivateKeyFactory($ecAdapter);
        foreach ($regular as $wif => $hex) {
            $private = $factory->fromWif($wif);
            $this->assertTrue($math->cmp(gmp_init($hex, 16), $private->getSecret()) == 0);
            $this->assertFalse($private->isCompressed());
        }

        $compressed = array(
            'Gw5BRJU8so94BMQFFCp8hMq5gopVsUEGZhS9C4VFUpV1iimnfXvn' => 'b3615879ebf2a64542db64e29d87ae175479bafae275cdd3caf779507cac4f5b',
            'GwWFrPLK76JzR5P2xz4kEVEjLJ4S5JKgX49srqQS8ZpprCgugozt' => '109dac331c97d41c6be9db32a2c3fa848d1a637807f2ab5c0e009cfb8007d1a0',
            'GuYcBEvLfYsKGYuHH3XHSr1sQi8wJxsGmySF84YgCSkBurZyNiiJ' => '50e36e410b227b70a1aa1abb28f1997aa6ec7a9ccddd4dc3ed708a18a0202b2f'
        );

        foreach ($compressed as $wif => $hex) {
            $private = $factory->fromWif($wif);
            $this->assertTrue($math->cmp(gmp_init($hex, 16), $private->getSecret()) == 0);
            $this->assertTrue($private->isCompressed());
        }
    }

    /**
     * @dataProvider getEcAdapters
     * @expectedException \BitWasp\Bitcoin\Exceptions\Base58ChecksumFailure
     */
    public function testInvalidWif(EcAdapterInterface $ecAdapter)
    {
        $factory = new PrivateKeyFactory($ecAdapter);
        $factory->fromWif('5akdgashdgkjads');
    }
}
