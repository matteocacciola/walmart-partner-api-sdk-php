<?php
namespace Walmart;

/**
 * Partial Walmart API client implemented with Guzzle.
 *
 * @method array list(array $config = [])
 * @method array get(array $config = [])
 * @method array getFeedItem(array $config = [])
 */
class Feed extends BaseClient
{
    /**
     * @param array $config
     * @param string $env
     */
    public function __construct(array $config = [], $env = self::ENV_PROD)
    {
        $this->descriptionPath = __DIR__ . '/descriptions/feed.php';

        // Create the client.
        parent::__construct(
            $config,
            $env
        );
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
        $feeds = [];
        do {
            try {
                $client = new self($config, $env);
                $feeds[] = $client->list(['limit' => $limit, 'offset' => $offset]);
                $offset += $limit;
            } catch (\Exception $ex) {
                $continue = false;
            }
        } while ($continue);

        return $feeds;
    }
}