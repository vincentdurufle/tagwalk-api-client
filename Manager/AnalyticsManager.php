<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class AnalyticsManager
{
    public const EVENT_PAGE = 'page';
    public const EVENT_PHOTO_LIST = 'photo_list';
    public const EVENT_PHOTO_VIEW = 'photo_view';
    public const EVENT_PHOTO_ZOOM = 'photo_zoom';
    public const EVENT_MOODBOARD_ADD = 'moodboard_add';
    public const EVENT_REQUEST_ADD = 'request_add';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider          $apiProvider
     * @param LoggerInterface|null $logger
     */
    public function __construct(ApiProvider $apiProvider, ?LoggerInterface $logger = null)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string      $slug
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool
     */
    public function media(string $slug, array $query = [], ?string $clientIp = null): bool
    {
        $response = $this->apiProvider->request('POST', "/api/analytics/media/$slug", [
            RequestOptions::HEADERS     => ['X-Client-IP' => $clientIp],
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        $status = $response->getStatusCode();
        $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
        if (!$success) {
            $this->logger->error('AnalyticsManager::media', [
                'code'    => $response->getStatusCode(),
                'message' => $response->getBody()->getContents(),
            ]);
        }

        return $success;
    }

    /**
     * @param string      $slug
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool
     */
    public function streetstyle(string $slug, array $query = [], ?string $clientIp = null): bool
    {
        $response = $this->apiProvider->request('POST', "/api/analytics/streetstyle/$slug", [
            RequestOptions::HEADERS     => ['X-Client-IP' => $clientIp],
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        $status = $response->getStatusCode();
        $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
        if (!$success) {
            $this->logger->error('AnalyticsManager::streetstyle', [
                'code'    => $response->getStatusCode(),
                'message' => $response->getBody()->getContents(),
            ]);
        }

        return $success;
    }

    /**
     * @param Request     $request
     * @param string      $route
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool success
     */
    public function page(Request $request, string $route, array $query = [], ?string $clientIp = null): bool
    {
        $response = $this->apiProvider->request('POST', "/api/analytics/page/$route", [
            RequestOptions::QUERY   => $query,
            RequestOptions::HEADERS => [
                'X-Client-IP'       => $clientIp ?? $request->getClientIp(),
                'X-User-Agent'      => $request->headers->get('User-Agent'),
                'X-accept-language' => $request->headers->get('accept-language'),
            ],
        ]);
        $status = $response->getStatusCode();
        $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
        if (!$success) {
            $this->logger->error('AnalyticsManager::page', [
                'code'    => $response->getStatusCode(),
                'message' => $response->getBody()->getContents(),
            ]);
        }

        return $success;
    }

    /**
     * @param Request     $request
     * @param string      $route
     * @param string      $event
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool success
     */
    public function photos(
        Request $request,
        string $route,
        string $event,
        array $query = [],
        ?string $clientIp = null
    ): bool {
        $response = $this->apiProvider->request('POST', "/api/analytics/photos/$route/$event", [
            RequestOptions::QUERY   => $query,
            RequestOptions::HEADERS => [
                'X-Client-IP'       => $clientIp ?? $request->getClientIp(),
                'X-User-Agent'      => $request->headers->get('User-Agent'),
                'X-accept-language' => $request->headers->get('accept-language'),
            ],
        ]);
        $status = $response->getStatusCode();
        $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
        if (!$success) {
            $this->logger->error('AnalyticsManager::photos', [
                'code'    => $response->getStatusCode(),
                'message' => $response->getBody()->getContents(),
            ]);
        }

        return $success;
    }
}
