<?php
declare(strict_types=1);

namespace DachcomDigital\SocialDataYoutube\Connector;

use DachcomDigital\SocialData\Connector\AbstractConnectorDefinition;
use DachcomDigital\SocialData\Connector\ConnectorFeedConfigurationInterface;

class YoutubeConnectorDefinition extends AbstractConnectorDefinition {
    
    public function getConnectorFeedConfiguration(): ConnectorFeedConfigurationInterface
    {
        return new YoutubeConnectorFeedConfiguration();
    }
    
}
