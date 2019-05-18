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
     * @return array
     */
    public function listAll()
    {
        $limit = 20;
        $offset = 0;
        $continue = true;
        $feeds = [];
        do {
            try {
                $feeds[] = $this->list(['limit' => $limit, 'offset' => $offset]);
                $offset += $limit;
            } catch (\Exception $ex) {
                $continue = false;
            }
        } while ($continue);

        return $feeds;
    }
}