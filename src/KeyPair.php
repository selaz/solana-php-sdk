<?php

namespace Tighten\SolanaPhpSdk;

use Tighten\SolanaPhpSdk\Exceptions\TodoException;
use Tighten\SolanaPhpSdk\Util\Ed25519Keypair;
use SodiumException;

/**
 * An account keypair used for signing transactions.
 */
class KeyPair
{
    protected Ed25519Keypair $_keypair;

    /**
     * @param Ed25519Keypair|null $keypair
     * @throws SodiumException
     */
    public function __construct(?Ed25519Keypair $keypair = null)
    {
        $this->_keypair = $keypair ?? Ed25519Keypair::generate();
    }

    /**
     * Generate a new random keypair
     */
    static function generate(): KeyPair
    {
        return new static();
    }

    /**
     * Create a keypair from a raw secret key byte array.
     *
     * This method should only be used to recreate a keypair from a previously
     * generated secret key. Generating keypairs from a random seed should be done
     * with the {@link Keypair.fromSeed} method.
     *
     * @param $secretKey
     * @return KeyPair
     */
    static public function fromSecretKey(string $secretKey): KeyPair
    {
        $publicKey = sodium_crypto_sign_publickey_from_secretkey($secretKey);

        return new static(
            new Ed25519Keypair($publicKey, $secretKey)
        );
    }

    /**
     * Generate a keypair from a 32 byte seed.
     *
     * @param string|array $seed
     * @return KeyPair
     * @throws SodiumException
     */
    static public function fromSeed($seed): KeyPair
    {
        $seed = is_string($seed)
            ? $seed
            : Ed25519Keypair::array2bin($seed);

        $keyPair = sodium_crypto_sign_seed_keypair($seed);

        return new static(
            Ed25519Keypair::from($keyPair)
        );
    }

    /**
     * The public key for this keypair
     *
     * @return PublicKey
     */
    public function getPublicKey(): PublicKey
    {
        return new PublicKey($this->_keypair->publicKey);
    }

    /**
     * The raw secret key for this keypair
     *
     * @return array
     */
    public function getSecretKey(): array
    {
        return $this->_keypair->secretKey;
    }
}
