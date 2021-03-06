<?php
namespace Walmart;

use fillup\A2X;
use GuzzleHttp\Post\PostFile;

/**
 * Partial Walmart API client implemented with Guzzle.
 *
 * @method array list(array $config = [])
 * @method array get(array $config = [])
 * @method array retire(array $config = [])
 */
class Item extends BaseClient
{
    /**
     * @param array $config
     * @param string $env
     */
    public function __construct(array $config = [], $env = self::ENV_PROD)
    {
        $this->descriptionPath = __DIR__ . '/descriptions/item.php';

        // Create the client.
        parent::__construct(
            $config,
            $env
        );

    }

    /**
     * @param array $items
     * @return array
     * @throws \Exception
     */
    public function bulk($items)
    {
        if ( ! is_array($items)) {
            throw new \Exception('Items is not an array', 1466349189);
        }

        $schema = [
            '/MPItemFeed/MPItem' => [
                'sendItemsAs' => 'MPItem',
                'includeWrappingTag' => false,
            ]
        ];

        $a2x = new A2X($items, $schema);
        $xml = $a2x->asXml();

        $file = new PostFile('file', $xml, 'file.xml', ['Content-Type' => 'text/xml']);

        return $this->bulkUpdate([
            'file' => $file,
        ]);
    }

    /**
     * @param array $config
     * @param string $env
     *
     * @return array
     */
    public static function listAll(array $config = [], $env = self::ENV_PROD)
    {
        $limit = 20;
        $offset = 0;
        $continue = true;
        $items = [];
        do {
            try {
                $client = new self($config, $env);
                $items[] = $client->list(['limit' => $limit, 'offset' => $offset]);
                $offset += $limit;
            } catch (\Exception $ex) {
                $continue = false;
            }
        } while ($continue);

        return $items;
    }
}