<?php

namespace Fahim\ZipCodeValidator\Plugin\Controller\Account;

use Magento\Customer\Controller\Account\LoginPost as Login;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;

class LoginPost
{
    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\App\RequestInterface $httpRequest
     * @param AccountRedirect $accountRedirect
     * @param \Fahim\ZipCodeValidator\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $httpRequest,
        AccountRedirect $accountRedirect,
        \Fahim\ZipCodeValidator\Helper\Data $helper
    ) {
        $this->_httpRequest = $httpRequest;
        $this->accountRedirect = $accountRedirect;
        $this->_helper = $helper;
    }

    /**
     * Plugin function for begore execute
     *
     * @return void
     */
    public function beforeExecute()
    {
        if ($this->_helper->getEnableDisable()) {
            $currentUrl = $this->_httpRequest->getServer('HTTP_REFERER');
            $this->accountRedirect->setRedirectCookie($currentUrl);
        }
    }
}
