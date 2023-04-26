<?php
namespace Fahim\ZipCodeValidator\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ZipAddButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * CustomButton constructor.
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->request = $context->getRequest();
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $regionId = $this->request->getParam('region_id');
        $data = [
            'label' => __('Add Entry'),
            'class' => 'primary',
            'id' => 'zip-add-button',
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/zipcode/new', ['region_id' => $regionId])),
            'sort_order' => 20,
        ];
        return $data;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
