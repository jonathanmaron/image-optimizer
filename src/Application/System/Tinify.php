<?php
declare(strict_types=1);

namespace Application\System;

use Throwable;
use Tinify\AccountException;
use Tinify\ClientException;
use Tinify\ConnectionException;
use Tinify\Exception;
use Tinify\ServerException;
use function Tinify\fromFile;
use function Tinify\setKey;
use function Tinify\validate;

class Tinify extends AbstractSystem implements InterfaceSystem
{
    /**
     * Tinify API key
     *
     * @var string
     */
    private $apiKey;

    /**
     * Tinify constructor
     *
     * @param $options
     */
    public function __construct($options)
    {
        if (!array_key_exists('api_key', $options)) {
            $format  = "Missing 'api_key' key in 'options' array at '%s'";
            $message = sprintf($format, __METHOD__);
            throw new InvalidArgumentException($message);
        }

        $this->setApiKey($options['api_key']);
    }

    /**
     * Get Tinify API key
     *
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * Set Tinify API key
     *
     * @param string $apiKey
     *
     * @return Tinify
     */
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Pass the image filename to service and optimize image
     *
     * @param string $filename
     *
     * @return bool
     */
    public function optimize(string $filename): bool
    {
        $ret = false;

        try {
            setKey($this->getApiKey());
            validate();
            $apiKeyIsValid = true;
        } catch (Exception $e) {
            $apiKeyIsValid = false;
            // validation of API key failed
        }

        if (!$apiKeyIsValid) {
            return $ret;
        }

        try {
            $tinify = fromFile($filename);
            $ret    = is_int($tinify->toFile($filename));
        } catch (AccountException $e) {
            // verify your API key and account limit
        } catch (ClientException $e) {
            // check your source image and request options
        } catch (ServerException $e) {
            // temporary issue with the Tinify API
        } catch (ConnectionException $e) {
            // network connection error occurred
        } catch (Throwable $e) {
            // something else went wrong, unrelated to the Tinify API
        }

        return $ret;
    }
}
