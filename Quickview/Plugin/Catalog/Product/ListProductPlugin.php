<?php
declare(strict_types=1);
/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Plugin\Catalog\Product;

use Magento\Framework\Registry;
use RLTSquare\Quickview\Model\Renderer;

/**
 * Class ListProductPlugin append html to list
 */
class ListProductPlugin
{
    /** @var Registry */
    private Registry $registry;

    /** @var Renderer */
    private Renderer $renderer;

    /**
     * ListProduct constructor.
     * @param Registry $registry
     * @param Renderer $renderer
     */
    public function __construct(
        Registry $registry,
        Renderer $renderer
    ) {
        $this->registry = $registry;
        $this->renderer = $renderer;
    }

    /**
     * @param  $subject
     * @param $result
     * @return string
     */
    public function afterToHtml(
        $subject,
        $result
    ) {
        if (!$this->registry->registry('rltsquare_category_observer')
            && !$subject->getIsRLTSquareQuickViewObserved()
        ) {
            $products = $subject->getLoadedProductCollection();
            if (!$products) {
                $products = $subject->getProductCollection();
            }

            if ($products) {
                foreach ($products as $product) {
                    $result .= $this->renderer->renderProductQuickViewButton($product, 'category');
                }
                $subject->setIsRLTSquareQuickViewObserved(true);
            }
        }

        return $result;
    }
}
