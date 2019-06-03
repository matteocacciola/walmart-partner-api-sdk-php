<?php

namespace Walmart;

use fillup\A2X;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Partial Walmart API client implemented with Guzzle.
 *
 * @method array list(array $config = [])
 * @method array get(array $config = [])
 * @method array acknowledge(array $config = [])
 */
class Order extends BaseClient
{
    const STATUS_CREATED = 'Created';
    const STATUS_ACKNOWLEDGED = 'Acknowledged';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_CANCELLED = 'Cancelled';

    const CANCEL_REASON = 'CANCEL_BY_SELLER';

    /**
     * @param array $config
     * @param string $env
     *
     * @throws \Exception
     */
    public function __construct(array $config = [], $env = self::ENV_PROD)
    {
        if (!isset($config['wmConsumerChannelType'])) {
            throw new \Exception('wmConsumerChannelType is required in configuration for Order APIs', 1467486702);
        }

        $this->descriptionPath = __DIR__ . '/descriptions/order.php';

        // Create the client.
        parent::__construct(
            $config,
            $env
        );
    }

    public function __call($name, array $arguments)
    {
        /*
         * Overriding call to list() since I cannot define a method with the same name as a reserved keyword.
         */
        if ($name === 'list') {
            return $this->listAll($arguments[0]);
        }

        return parent::__call($name, $arguments);
    }

    /**
     * List released orders
     *
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    public function listReleased(array $config = [])
    {
        try {
            return $this->privateListReleased($config);
        } catch (\Exception $e) {
            if ($e instanceof RequestException) {
                /*
                 * ListReleased and List return 404 error if no results are found, even for successful API calls,
                 * So if result status is 404, transform to 200 with empty results.
                 */
                /** @var ResponseInterface $response */
                $response = $e->getResponse();
                if ((string)$response->getStatusCode() === '404') {
                    return [
                        'statusCode' => 200,
                        'list' => [
                            'meta' => [
                                'totalCount' => 0
                            ]
                        ],
                        'elements' => []
                    ];
                }
                throw $e;
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    public static function listReleasedWithAllCursors(array $config = [])
    {
        try {
            $config['limit'] = 200;
            $client = new self($config);

            $response = $client->listReleased($config);

            $arrResponses[] = $response;
            if (isset($response['meta']['nextCursor'])) {
                do {
                    $config['nextCursor'] = $response['meta']['nextCursor'];

                    $client = new self($config);
                    $response = $client->listReleased($config);
                    $arrResponses[] = $response;
                } while (isset($response['meta']['nextCursor']));
            }

            return self::retrieveFinalResponseFromAllCursors($arrResponses);
        } catch (\Exception $e) {
            if ($e instanceof RequestException) {
                /*
                 * ListReleased and List return 404 error if no results are found, even for successful API calls,
                 * So if result status is 404, transform to 200 with empty results.
                 */
                /** @var ResponseInterface $response */
                $response = $e->getResponse();
                if ((string)$response->getStatusCode() === '404') {
                    return [
                        'statusCode' => 200,
                        'list' => [
                            'meta' => [
                                'totalCount' => 0
                            ]
                        ],
                        'elements' => []
                    ];
                }
                throw $e;
            } else {
                throw $e;
            }
        }
    }

    /**
     * List all orders
     *
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    public function listAll(array $config = [])
    {
        try {
            return $this->privateList($config);
        } catch (\Exception $e) {
            if ($e instanceof RequestException) {
                /*
                 * ListReleased and List return 404 error if no results are found, even for successful API calls,
                 * So if result status is 404, transform to 200 with empty results.
                 */
                /** @var ResponseInterface $response */
                $response = $e->getResponse();
                if ((string)$response->getStatusCode() === '404') {
                    return [
                        'statusCode' => 200,
                        'list' => [
                            'meta' => [
                                'totalCount' => 0
                            ]
                        ],
                        'elements' => []
                    ];
                }
                throw $e;
            } else {
                throw $e;
            }
        }
    }

    /**
     * @param array $config
     *
     * @return array
     * @throws \Exception
     */
    public static function listAllWithAllCursors(array $config = [])
    {
        try {
            $config['limit'] = 200;
            $client = new self($config);

            $response = $client->listAll($config);

            $arrResponses[] = $response;
            if (isset($response['meta']['nextCursor'])) {
                do {
                    $config['nextCursor'] = $response['meta']['nextCursor'];

                    $client = new self($config);
                    $response = $client->listAll($config);
                    $arrResponses[] = $response;
                } while (isset($response['meta']['nextCursor']));
            }

            return self::retrieveFinalResponseFromAllCursors($arrResponses);
        } catch (\Exception $e) {
            if ($e instanceof RequestException) {
                /*
                 * ListReleased and List return 404 error if no results are found, even for successful API calls,
                 * So if result status is 404, transform to 200 with empty results.
                 */
                /** @var ResponseInterface $response */
                $response = $e->getResponse();
                if ((string)$response->getStatusCode() === '404') {
                    return [
                        'statusCode' => 200,
                        'list' => [
                            'meta' => [
                                'totalCount' => 0
                            ]
                        ],
                        'elements' => []
                    ];
                }
                throw $e;
            } else {
                throw $e;
            }
        }
    }

    /**
     * Cancel an order
     *
     * @param string $purchaseOrderId
     * @param array $order
     *
     * @return array
     * @throws \Exception
     */
    public function cancel($purchaseOrderId, $order)
    {
        if (empty($purchaseOrderId)) {
            throw new \Exception("purchaseOrderId cannot be empty", 1448480746);
        }

        if (empty($order)) {
            throw new \Exception("Order array cannot be empty", 1448480746);
        }

        $schema = [
            '/orderCancellation' => [
                'namespace' => 'ns3',
                'childNamespace' => 'ns3',
            ],
            '/orderCancellation/orderLines' => [
                'sendItemsAs' => 'orderLine',
            ],
            '/orderCancellation/orderLines/orderLine/orderLineStatuses' => [
                'sendItemsAs' => 'orderLineStatus',
            ],
            '@namespaces' => [
                'ns3' => 'http://walmart.com/mp/v3/orders'
            ],
        ];

        $a2x = new A2X($order, $schema);
        $xml = $a2x->asXml();

        return $this->cancelOrder([
            'purchaseOrderId' => $purchaseOrderId,
            'order' => $xml,
        ]);
    }

    /**
     * Ship an order
     *
     * @param string $purchaseOrderId
     * @param array $order
     *
     * @return array
     * @throws \Exception
     */
    public function ship($purchaseOrderId, $order)
    {
        if (empty($purchaseOrderId)) {
            throw new \Exception("purchaseOrderId must be numeric", 1448480750);
        }

        if (empty($order)) {
            throw new \Exception("Order array cannot be empty", 1448480746);
        }

        $schema = [
            '/orderShipment' => [
            ],
            '/orderShipment/orderLines' => [
                'sendItemsAs' => 'orderLine',
            ],
            '/orderShipment/orderLines/orderLine/orderLineStatuses' => [
                'sendItemsAs' => 'orderLineStatus',
            ],
            '@namespaces' => [
                'ns2' => 'http://walmart.com/mp/v3/orders',
                'ns3' => 'http://walmart.com'
            ],
        ];

        $a2x = new A2X($order, $schema);
        $xml = $a2x->asXml();

        return $this->shipOrder([
            'purchaseOrderId' => $purchaseOrderId,
            'order' => $xml,
        ]);
    }

    /**
     * Refund an order
     *
     * @param string $purchaseOrderId
     * @param array $order
     *
     * @return array
     * @throws \Exception
     */
    public function refund($purchaseOrderId, $order)
    {
        if (empty($purchaseOrderId)) {
            throw new \Exception("purchaseOrderId must be numeric", 1448480783);
        }

        if (empty($order)) {
            throw new \Exception("Order array cannot be empty", 1448480746);
        }

        $schema = [
            '/orderRefund' => [
                'namespace' => 'ns3',
                'childNamespace' => 'ns3',
            ],
            '/orderRefund/orderLines' => [
                'sendItemsAs' => 'orderLine',
            ],
            '/orderRefund/orderLines/orderLine/refunds' => [
                'sendItemsAs' => 'refund',
            ],
            '/orderRefund/orderLines/orderLine/refunds/refund/refundCharges' => [
                'sendItemsAs' => 'refundCharge',
            ],
            '@namespaces' => [
                'ns3' => 'http://walmart.com/mp/v3/orders'
            ],
        ];

        $a2x = new A2X($order, $schema);
        $xml = $a2x->asXml();

        return $this->refundOrder([
            'purchaseOrderId' => $purchaseOrderId,
            'order' => $xml,
        ]);
    }

    /**
     * @param array $responses
     *
     * @return array
     */
    private static function retrieveFinalResponseFromAllCursors(array $responses)
    {
        $finalResponse = [
            'statusCode' => 200,
            'meta' => [
                'totalCount' => $responses[0]['meta']['totalCount']
            ],
            'elements' => [
                'order' => []
            ]
        ];
        $arrOrders = [];
        foreach ($responses as $response) {
            $remoteOrders = $response['elements']['order'];

            if (self::isAssociativeArray($remoteOrders)) {
                $remoteOrders = [$remoteOrders];
            }

            foreach ($remoteOrders as $remoteOrder) {
                $arrOrders[] = $remoteOrder;
            }
        }

        $finalResponse['elements']['order'] = $arrOrders;

        return $finalResponse;
    }

    /**
     * @param array $arr
     *
     * @return bool
     */
    private static function isAssociativeArray(array $arr) : bool
    {
        if ([] === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}