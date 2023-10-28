<?php
declare(strict_types=1);

namespace RLTSquare\TaxRuleAssignment\Observer;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @class ApplyTaxRuleObserver
 */
class ApplyTaxRuleObserver implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * @var Product
     */
    private Product $product;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $attributeCollectionFactory;

    /**
     * @param RequestInterface $request
     * @param Product $product
     * @param CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        RequestInterface  $request,
        Product           $product,
        CollectionFactory $attributeCollectionFactory
    ) {
        $this->request = $request;
        $this->product = $product;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @param Observer $observer
     * @return array
     */
    public function execute(Observer $observer)
    {
        $productId = $this->request->getParam('id');
        if ($productId) {
            // Load the product
            $product = $this->product->load($productId);
            $taxClassId = $product->getCustomAttribute('tax_class_id')->getValue();

            // Retrieve tax attributes collection
            $attributeCollection = $this->attributeCollectionFactory->create()
                ->addFieldToFilter('entity_type_id', Product::ENTITY)
                ->addFieldToFilter('frontend_input', 'select')
                ->addFieldToFilter('source_model', 'Magento\Catalog\Model\ResourceModel\Eav\Attribute\Source\TaxClass')
                ->addFieldToSelect('attribute_code');

            $taxClassAttribute = $attributeCollection->getFirstItem();

            // Add the tax class attribute to the attributes array
            $result[] = [
                'code' => $taxClassAttribute->getAttributeCode(),
                'label' => __('Tax Class'),
                'value' => $taxClassId,
                'attribute_code' => $taxClassAttribute->getAttributeCode(),
            ];
        }

        return $result;

    }


}
