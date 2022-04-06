<?php
declare(strict_types=1);

namespace DachcomDigital\SocialDataYoutube\FormEngine\Element;

use DachcomDigital\SocialData\FormEngine\Element\AbstractConnectorStatusElement;

class YoutubeConnectorStatusElement extends AbstractConnectorStatusElement
{
    
    public function render()
    {
        if (!$this->validateConnectorConfiguration()) {
           return ['html' => $this->renderStatusMessage('Missing connector configuration!', 'warning', [], 'actions-exclamation-triangle')];
        }
        
        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] = $this->renderStatusMessage('automatically connected', 'success', [], 'actions-check');
        $html[] = '</div>';
    
        $result = [];
        $result['html'] = implode(LF, $html);
        
        return $result;
    }
    
    protected function validateConnectorConfiguration(): bool
    {
        $connectorConfiguration = $this->getConnectorConfiguration();
        return !empty($connectorConfiguration) && !empty($connectorConfiguration['api_key']) && !empty($connectorConfiguration['channel_id']);
    }
}
