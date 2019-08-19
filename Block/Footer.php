<?php
/**
 * Created by Andrew Stepanchuk.
 * Date: 19.08.19
 * Time: 16:32
 */

namespace Netzexpert\DynamicCopyright\Block;

use Magento\Customer\Model\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\Store;
use Magento\Theme\Block\Html\Footer as OriginalFooter;

class Footer extends OriginalFooter
{
    const CACHE_TAG = 'footer_';

    /**
     * @var DateTime
     */
    private $dateTime;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        DateTime $dateTime,
        array $data = []
    ) {
        parent::__construct($context, $httpContext, $data);
        $this->dateTime = $dateTime;
    }

    public function getIdentities()
    {
        return [
            Store::CACHE_TAG,
            self::CACHE_TAG . '_' .
                $this->getRequest()->getModuleName() .
                '_' . $this->getRequest()->getControllerName() .
                '_' . $this->getRequest()->getActionName() .
                '_' . $this->getRequest()->getParam('id')
        ];
    }

    public function getCacheKeyInfo()
    {
        return [
            'PAGE_FOOTER',
            $this->_storeManager->getStore()->getId(),
            (int)$this->_storeManager->getStore()->isCurrentlySecure(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(Context::CONTEXT_AUTH),
            $this->getTemplateFile(),
            'template' => $this->getTemplate(),
            $this->getRequest()->getModuleName(),
            $this->getRequest()->getControllerName(),
            $this->getRequest()->getActionName(),
            $this->getRequest()->getParam('id')
        ];
    }

    /**
     * Retrieve copyright information
     *
     * @return string
     */
    public function getCopyright()
    {
        if (!$this->_copyright) {
            $title = $this->_layout->getBlock('page.main.title')->getPageTitle();
            $this->_copyright = $title . ' &copy; ' . $this->dateTime->gmtDate('Y') .
                ' ' . $this->_scopeConfig->getValue('dynamic_copyright/general/suffix');
        }
        return $this->_copyright;
    }
}
