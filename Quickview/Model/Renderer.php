<?php
declare(strict_types=1);
/**
 * author:awais.rehman@rltsquare.com
 */

namespace RLTSquare\Quickview\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Profiler;
use Magento\Framework\View\LayoutInterface;
use RLTSquare\Quickview\Block\QuickViewButton;

/**
 * Class Renderer for render button Quick View on list
 */
class Renderer
{
    /**
     *
     */
    const MODE_CATEGORY = 'category';

    /** @var LayoutInterface */
    private LayoutInterface $layout;

    /** @var null|QuickViewButton */
    private $buttonBlock = null;

    /**
     * Renderer constructor.
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param Product $product
     * @param string $mode
     * @return string
     */
    public function renderProductQuickViewButton(Product $product, string $mode = self::MODE_CATEGORY): string
    {
        $html = '';
        Profiler::start('__RenderRLTSquareProductQuickViewButton__');
        $html .= $this->generateHtml($product);
        Profiler::stop('__RenderRLTSquareProductQuickViewButton__');

        return $html;
    }

    /**
     * @param Product $product
     * @return string
     */
    private function generateHtml(Product $product): string
    {
        if ($this->buttonBlock === null) {
            $this->buttonBlock = $this->layout->createBlock(QuickViewButton::class);
        }
        return $this->buttonBlock->setProduct($product)->toHtml();
    }
}
