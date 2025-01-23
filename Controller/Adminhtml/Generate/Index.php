<?php
namespace Riverstone\AiContentGenerator\Controller\Adminhtml\Generate;

use Riverstone\AiContentGenerator\Model\OpenAI\OpenAiException;
use InvalidArgumentException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Riverstone\AiContentGenerator\Model\CompletionConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Riverstone\AiContentGenerator\Model\CompletionRequest\GeneralCompletion;

/**
 * Controller file for generator content
 */
class Index extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Riverstone_AiContentGenerator::generate';

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CompletionConfig
     */
    private $completionConfig;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var GeneralCompletion
     */
    protected $generalCompletion;

    /**
     * Controller file for generator content

     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CompletionConfig $completionConfig
     * @param ProductRepositoryInterface $productRepository
     * @param RequestInterface $request
     * @param GeneralCompletion $generalCompletion
     */

    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CompletionConfig $completionConfig,
        ProductRepositoryInterface $productRepository,
        RequestInterface $request,
        GeneralCompletion $generalCompletion
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->completionConfig = $completionConfig;
        $this->productRepository = $productRepository;
        $this->request = $request;
        $this->generalCompletion = $generalCompletion;
    }

    /**
     * Controller execute function

     * @throws LocalizedException
     */
    public function execute()
    {
        $resultPage = $this->jsonFactory->create();
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom234567.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        try {
            $prompt = $this->getRequest()->getParam('prompt');
            $logger->info('Prompt received: ' . $prompt);
            $result = $this->generalCompletion->query($prompt);
            $logger->info('Generated content: ' . $result);

        } catch (OpenAiException | InvalidArgumentException $e) {
            $resultPage->setData([
                'error' => $e->getMessage()
            ]);
            $logger->info(print_r($resultPage));
            return $resultPage;
        }

        $resultPage->setData([
            'result' => $result,'type' => 'success'
        ]);

        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Riverstone_AiContentGenerator::generate');
    }
}
