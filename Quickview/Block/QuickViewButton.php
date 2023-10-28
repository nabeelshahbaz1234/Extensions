<?php
declare(strict_types=1);

/**
 * author: awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Block;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use RLTSquare\Quickview\Model\Config;

/**
 * Class QuickViewButton for rendering the button
 */
class QuickViewButton extends Template implements IdentityInterface
{
    /**
     * Cache tag prefix
     */
    const CACHE_TAG = 'rltsquare_quickview';

    /** @var string */
    protected $_template = 'RLTSquare_Quickview::button.phtml';
    /** @var Config */
    protected Config $quickViewConfig;
    /**
     * @var SerializerInterface
     */
    protected SerializerInterface $serializer;
    /** @var Product */
    private mixed $product;
    /** @var HttpContext */
    private HttpContext $httpContext;
    private $storeId;

    private $themeId;

    /**
     * QuickViewButton constructor.
     * @param Context $context
     * @param HttpContext $httpContext
     * @param SerializerInterface $serializer
     * @param Config $quickViewConfig
     * @param array $data
     * @throws NoSuchEntityException
     */
    public function __construct(
        Context             $context,
        HttpContext         $httpContext,
        SerializerInterface $serializer,
        Config              $quickViewConfig,
        array               $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->serializer = $serializer;
        $this->quickViewConfig = $quickViewConfig;
        $this->product = $data['product'] ?? null;
        $this->storeId = $this->_storeManager->getStore()->getId();
        $this->themeId = $this->_design->getDesignTheme()->getId();
        $this->addData([
            'cache_lifetime' => 86400
        ]);
    }

    /**
     * @return string
     */
    public function getJsonConfig()
    {
        $productId = $this->getProduct()->getId();

        return $this->serializer->serialize(
            [
                'position' => 'bottom-center',
                'path' => $this->getContainerPath(),
                'closeSeconds' => 5,
                'mode' => 'cat',
                'product' => $productId,
                'margin' => 10,
                'alignment' => 0,
            ]
        );
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getContainerPath()
    {
        return $this->quickViewConfig->getContainerPath();
    }

    public function CheckEnableDisable()
    {
        return $this->quickViewConfig->isEnabled();
    }

    /**
     * @return boolean|int
     */
    public function isAdminArea()
    {
        return $this->getArea() == 'adminhtml';
    }

    /**
     * @return array
     */
    public function getCacheKeyInfo()
    {
        $productId = $this->getProduct()->getId();

        return [
            'RLTSQUARE_PRODUCT_QUICK_VIEW',
            $this->storeId,
            $this->themeId,
            $this->httpContext->getValue(CustomerContext::CONTEXT_GROUP),
            'template' => $this->getTemplate(),
            $productId
        ];
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return array_merge(
            ($this->getProduct() instanceof IdentityInterface) ? $this->getProduct()->getIdentities() : [],
            [self::CACHE_TAG . '_' . $this->getProduct()->getId()]
        );
    }
}
