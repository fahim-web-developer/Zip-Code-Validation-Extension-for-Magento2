<?php
/**
 * Fahim software.
 * @category Fahim
 * @package Fahim_ZipCodeValidator
 * @author Fahim
 * @copyright Copyright (c) Fahim Software Private Limited (https://Fahim.com)
 * @license https://store.Fahim.com/license.html
 */
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
$value = $objectManager->create('Customvendorname\Custommodulename\Helper\Data')->getConfig('zipcodevalidator/wk_zipcodevalidatorstatus/message');
echo $value; die;
$helper = $block->getZipCodeHelper();
$product = $block->getProduct();
$type = $product->getTypeId();
$stock = $block->getStockValue($product->getId());
if ($helper->getEnableDisable()
    && ($type != 'virtual' && $type != 'downloadable')
    && $block->isDisplayValidatorField($product->getId())
    && $stock
) {
    $data = $block->getJsonEncodedData([
        'url' => $block->getUrl(),
        'currenturl' => $block->getCurrentUrl(),
        'isCustomerActive' => $block->getCustomerStatus()
    ]);
    $postcode = $block->getCustomerZipcode(); ?>

    <div class="wk-zcv-zipbox">
        <div class="wk-zcv-zip">
            <div class="wk-zcv-wrapper">
                <div class="wk-zcv-zipcodeform">
                    <form autocomplete="off">
                        <input type="text"
                            name="zipcode"
                            placeholder="<?= $block->escapeHtml(__('Enter Delivery Pincode')); ?>"
                            class="wk-zcv-zipform0"
                            title="<?= $block->escapeHtml(__('Enter Delivery Pincode')); ?>"
                            data-id="<?= $block->escapeHtml($product->getId()); ?>"
                            seller-data-id="0"
                            value="<?= $block->escapeHtml($postcode); ?>"
                            autocomplete="off"/>
                        <div id="wk-zcv-check0" data-pro-id="<?= $block->escapeHtml($product->getId()); ?>" data-id="0">
                            <span><?= $block->escapeHtml(__('Check Availability')); ?></span>
                        </div>
                    </form>
                    <div class="wk-zcv-zipcookie0">
                        <ul id="wk-zcv-addr0"></ul>
                        <ul id="wk-zcv-cookie0"></ul>
                        <ul id="wk-zcv-login0"></ul>
                    </div>
                </div>
                <div class="wk-zcv-loader0"></div>
            </div>
            <div class="wk-zcv-ziperror0" id="wk-zcv-error"></div>
            <div class="wk-zcv-zipsuccess0"></div>
        </div>
    </div>
    <div class="wk-zcv-login-popup" id="modal-popup">
        <span class="close-login-popup">x</span>
        <?= /* @noEscape */ $block->getLayout()->createBlock(
            \Magento\Customer\Block\Form\Login::class
        )->setData(
            'captcha_status',
            $helper->getCapchaConfig()
        )->setTemplate(
            "Fahim_ZipCodeValidator::customer/form/login.phtml"
        )->toHtml();?>
    </div>
    <script type="text/x-magento-init">
    {
        "body": {
            "Fahim_ZipCodeValidator/js/viewonproduct": <?= /* @noEscape */ $data ?>
        }
    }
    </script>
    <?php
}?>
