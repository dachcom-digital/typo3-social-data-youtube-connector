<?php
declare(strict_types=1);

namespace DachcomDigital\SocialDataYoutube\Connector;

use DachcomDigital\SocialData\Connector\ConnectorInterface;

class YoutubeConnector implements ConnectorInterface
{
    protected array $configuration;
    
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;
    }
    
    public function fetchItems(): array
    {
        if ($this->configuration['fetch_type'] === 'playlist') {
            return $this->fetchPlaylistItems($this->configuration['playlist_id']);
        }
        return $this->fetchChannelItems($this->configuration['channel_id']);
    }
    
    protected function fetchPlaylistItems(string $playlistId, $limit = 30): array
    {
        $client = $this->getClient();
        
        $fetchedItems = [];
        $service = new \Google\Service\YouTube($client);
        
        $params = [
            'playlistId' => $playlistId,
            'maxResults' => $limit
        ];
        
        $items = [];
        $nextPageToken = 'INIT';
        while (!empty($nextPageToken)) {
            
            if ($nextPageToken !== 'INIT') {
                $params['pageToken'] = $nextPageToken;
            }
            
            try {
                $response = $service->playlistItems->listPlaylistItems('snippet', $params);
            } catch (\Google\Service\Exception $e) {
                throw new \Exception(sprintf('google service youtube fetch error: %s [endpoint: %s]', implode(', ', array_map(static function ($e) {
                    return $e['message'];
                }, $e->getErrors())), 'listPlaylistItems'));
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('fetch error: %s [endpoint: %s]', $e->getMessage(), 'listPlaylistItems'));
            }
            
            if (!$response instanceof \Google\Service\YouTube\PlaylistItemListResponse) {
                break;
            }
            
            $items = array_merge($fetchedItems, $response->getItems());
            $nextPageToken = $response->getNextPageToken();
            
            if (count($fetchedItems) >= $limit) {
                break;
            }
        }
        
        if (!is_array($fetchedItems)) {
            return [];
        }
        
        $preparedItems = [];
        
        /** @var \Google\Service\YouTube\PlaylistItem $item */
        foreach ($items as $item) {
    
            $resource = $item->getSnippet()->getResourceId();
            $snippet = $item->getSnippet();
            
            $preparedItems[] = $this->getPreparedItem($resource, $snippet);
        }
        
        return $preparedItems;
    }
    
    protected function fetchChannelItems(string $channelId, int $limit = 30): array {
        $client = $this->getClient();
        
        $fetchedItems = [];
        $service = new \Google\Service\YouTube($client);
    
        $params = [
            'channelId'  => $channelId,
            'maxResults' => $limit,
            'order'      => 'date',
            'type'       => 'video'
        ];
    
        $items = [];
        $nextPageToken = 'INIT';
        while (!empty($nextPageToken)) {
        
            if ($nextPageToken !== 'INIT') {
                $params['pageToken'] = $nextPageToken;
            }
        
            try {
                $response = $service->search->listSearch('snippet', $params);
            } catch (\Google\Service\Exception $e) {
                throw new \Exception(sprintf('google service youtube fetch error: %s [endpoint: %s]', implode(', ', array_map(static function ($e) {
                    return $e['message'];
                }, $e->getErrors())), 'listSearch'));
            } catch (\Throwable $e) {
                throw new \Exception(sprintf('fetch error: %s [endpoint: %s]', $e->getMessage(), 'listSearch'));
            }
        
            if (!$response instanceof \Google\Service\YouTube\SearchListResponse) {
                break;
            }
        
            $items = array_merge($fetchedItems, $response->getItems());
            $nextPageToken = $response->getNextPageToken();
        
            if (count($items) >= $limit) {
                break;
            }
        }
        
        if (!is_array($items)) {
            return [];
        }
    
        $preparedItems = [];
    
        /** @var \Google\Service\YouTube\SearchResult $item */
        foreach ($items as $item) {
        
            $resource = $item->getId();
            $snippet = $item->getSnippet();
    
            $preparedItems[] = $this->getPreparedItem($resource, $snippet);
        }
    
        return $preparedItems;
    }
    
    protected function getPreparedItem(\Google\Service\YouTube\ResourceId $resource, \Google\Service\YouTube\SearchResultSnippet $snippet)
    {
        $thumbnailUrl = '';
        $thumbnail = $this->getThumbnail($snippet->getThumbnails());
        if ($thumbnail instanceof \Google\Service\YouTube\Thumbnail)
        {
            $thumbnailUrl = $thumbnail->getUrl();
        }
        return [
            'id'         => $resource->getVideoId(),
            'title'      => $snippet->getTitle(),
            'content'    => $snippet->getDescription(),
            'datetime'   => \DateTime::createFromFormat(\DateTime::ISO8601, $snippet->getPublishedAt()),
            'url'        => sprintf('https://youtu.be/%s', $resource->getVideoId()),
            'posterUrl'  => $thumbnailUrl
        ];
    }
    
    protected function getThumbnail(\Google\Service\YouTube\ThumbnailDetails $thumbnailDetails): ?\Google\Service\YouTube\Thumbnail
    {
        return $thumbnailDetails->getMaxres()
            ?? $thumbnailDetails->getHigh()
            ?? $thumbnailDetails->getMedium()
            ?? $thumbnailDetails->getStandard()
            ?? $thumbnailDetails->getDefault();
    }
    
    protected function getClient(): \Google\Client
    {
        $client = new \Google\Client();
        $client->setApplicationName('TYPO3 Social Data | Youtube Connector');
        $client->setDeveloperKey($this->configuration['api_key']);
        
        return $client;
    }
    

}
