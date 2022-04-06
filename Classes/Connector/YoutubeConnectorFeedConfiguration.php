<?php
declare(strict_types=1);

namespace DachcomDigital\SocialDataYoutube\Connector;

use DachcomDigital\SocialData\Connector\ConnectorFeedConfigurationInterface;
use DachcomDigital\SocialDataYoutube\FormEngine\Element\YoutubeConnectorStatusElement;

final class YoutubeConnectorFeedConfiguration implements ConnectorFeedConfigurationInterface
{
    public function getFlexFormFile(): string
    {
        return 'EXT:social_data_youtube/Configuration/FlexForms/YoutubeConnectorConfiguration.xml';
    }
    
    public function getStatusFormElementClass(): string
    {
        return YoutubeConnectorStatusElement::class;
    }
}
