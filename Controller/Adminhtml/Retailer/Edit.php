<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Smile
 * @package   Smile\Retailer
 * @author    Romain Ruaud <romain.ruaud@smile.fr>
 * @copyright 2016 Smile
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Smile\Retailer\Controller\Adminhtml\Retailer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Smile\Retailer\Api\Data\RetailerInterface;
use Smile\Retailer\Controller\Adminhtml\AbstractRetailer;

/**
 * Retailer Adminhtml Edit controller.
 *
 * @category Smile
 * @package  Smile\Retailer
 * @author   Romain Ruaud <romain.ruaud@smile.fr>
 */
class Edit extends AbstractRetailer
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $retailerId = (int) $this->getRequest()->getParam("entity_id");

        $retailer = null;
        $isExistingRetailer = (bool) $retailerId;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/rorua.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('EXECUTE ACTION');
        $logger->info($retailerId);

        if ($isExistingRetailer) {
            try {
                $retailer = $this->retailerRepository->get($retailerId);
                $this->coreRegistry->register('current_seller', $retailer);
                $resultPage->getConfig()->getTitle()->prepend(__('Edit %1', $retailer->getName()));
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException($e, __('Something went wrong while editing the retailer.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                $logger->info($e->getTraceAsString());
                return $resultRedirect;
            } catch (\Exception $e) {
                $logger->info($e->getTraceAsString());
            }
        }

        if (!$isExistingRetailer) {
            $retailer = $this->retailerFactory->create();
            $this->coreRegistry->register('current_seller', $retailer);
            $resultPage->getConfig()->getTitle()->prepend(__('New Retailer'));
        }

        $resultPage->addBreadcrumb(__('Retailer'), __('Retailer'));

        return $resultPage;
    }
}