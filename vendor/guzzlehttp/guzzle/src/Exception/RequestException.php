<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 *
 */

namespace EbayVendor\GuzzleHttp\Exception;

use EbayVendor\GuzzleHttp\Promise\PromiseInterface;
use EbayVendor\Psr\Http\Message\RequestInterface;
use EbayVendor\Psr\Http\Message\ResponseInterface;
use EbayVendor\Psr\Http\Message\UriInterface;
/**
 * HTTP Request exception
 */
class RequestException extends TransferException
{
    /** @var RequestInterface */
    private $request;
    /** @var ResponseInterface|null */
    private $response;
    /** @var array */
    private $handlerContext;
    public function __construct($message, RequestInterface $request, ResponseInterface $response = null, \Exception $previous = null, array $handlerContext = [])
    {
        // Set the code of the exception if the response is set and not future.
        $code = $response && !$response instanceof PromiseInterface ? $response->getStatusCode() : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
        $this->handlerContext = $handlerContext;
    }
    /**
     * Wrap non-RequestExceptions with a RequestException
     *
     * @param RequestInterface $request
     * @param \Exception       $e
     *
     * @return RequestException
     */
    public static function wrapException(RequestInterface $request, \Exception $e)
    {
        return $e instanceof RequestException ? $e : new RequestException($e->getMessage(), $request, null, $e);
    }
    /**
     * Factory method to create a new exception with a normalized error message
     *
     * @param RequestInterface  $request  Request
     * @param ResponseInterface $response Response received
     * @param \Exception        $previous Previous exception
     * @param array             $ctx      Optional handler context.
     *
     * @return self
     */
    public static function create(RequestInterface $request, ResponseInterface $response = null, \Exception $previous = null, array $ctx = [])
    {
        if (!$response) {
            return new self('Error completing request', $request, null, $previous, $ctx);
        }
        $level = (int) \floor($response->getStatusCode() / 100);
        if ($level === 4) {
            $label = 'Client error';
            $className = ClientException::class;
        } elseif ($level === 5) {
            $label = 'Server error';
            $className = ServerException::class;
        } else {
            $label = 'Unsuccessful request';
            $className = __CLASS__;
        }
        $uri = $request->getUri();
        $uri = static::obfuscateUri($uri);
        // Client Error: `GET /` resulted in a `404 Not Found` response:
        // <html> ... (truncated)
        $message = \sprintf('%s: `%s %s` resulted in a `%s %s` response', $label, $request->getMethod(), $uri, $response->getStatusCode(), $response->getReasonPhrase());
        $summary = static::getResponseBodySummary($response);
        if ($summary !== null) {
            $message .= ":\n{$summary}\n";
        }
        return new $className($message, $request, $response, $previous, $ctx);
    }
    /**
     * Get a short summary of the response
     *
     * Will return `null` if the response is not printable.
     *
     * @param ResponseInterface $response
     *
     * @return string|null
     */
    public static function getResponseBodySummary(ResponseInterface $response)
    {
        return \EbayVendor\GuzzleHttp\Psr7\get_message_body_summary($response);
    }
    /**
     * Obfuscates URI if there is a username and a password present
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    private static function obfuscateUri(UriInterface $uri)
    {
        $userInfo = $uri->getUserInfo();
        if (\false !== ($pos = \strpos($userInfo, ':'))) {
            return $uri->withUserInfo(\substr($userInfo, 0, $pos), '***');
        }
        return $uri;
    }
    /**
     * Get the request that caused the exception
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * Get the associated response
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * Check if a response was received
     *
     * @return bool
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }
    /**
     * Get contextual information about the error from the underlying handler.
     *
     * The contents of this array will vary depending on which handler you are
     * using. It may also be just an empty array. Relying on this data will
     * couple you to a specific handler, but can give more debug information
     * when needed.
     *
     * @return array
     */
    public function getHandlerContext()
    {
        return $this->handlerContext;
    }
}
