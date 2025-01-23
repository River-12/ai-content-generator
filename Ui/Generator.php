<?php
namespace Riverstone\AiContentGenerator\Ui;

use Riverstone\AiContentGenerator\Model\CompletionConfig;
use Magento\Ui\Component\Container;

class Generator extends Container
{
    /**
     * Get Configuration for ui component

     * @return array
     */
    public function getConfiguration(): array
    {
        $config = parent::getConfiguration();

        /** @var CompletionConfig $completionConfig */
        $completionConfig = $this->getData('completion_config');

        return array_merge(
            $config,
            $completionConfig->getConfig(),
            [
                'settings' => [
                    'serviceUrl' => $this->context->getUrl('riverstone_aicontentgenerator/generate'),
                    'validationUrl' => $this->context->getUrl('riverstone_aicontentgenerator/validate'),
                ]
            ]
        );
    }
}
