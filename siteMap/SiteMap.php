<?php

declare(strict_types=1);

namespace app\models\siteMap;

use app\repositories\category\CategoryRepository;
use app\repositories\doc\DocRepository;
use app\repositories\Hydrator;
use app\repositories\img\ImgRepository;
use app\repositories\product\ProductRepository;
use app\services\category\CategoryService;
use app\services\product\ProductServices;

class SiteMap
{
    /**
     * getUrl
     * Массив всех url Category.
     */
    public function getCategoryUrl(): array
    {
        $urls = [];

        // Формируем url Category на основе их вложенности
        $categoryService = new CategoryService(new CategoryRepository(new Hydrator()));
        $categories = $categoryService->getAllByActive();
        foreach ($categories as $category) {
            if ($category->getParent()->getValue() > 2) {
                foreach ($categories as $parent) {
                    if ($parent->getId()->getValue() === $category->getParent()->getValue()) {
                        $urls[] .= $parent->getUrl()->getValue().'/'.$category->getUrl()->getValue();

                        break;
                    }
                }
            } else {
                if ($category->getUrl()->getValue() !== '#' && $category->getUrl()->getValue() !== 'catalog') {
                    $urls[] .= 'catalog/'.$category->getUrl()->getValue();
                }
            }
        }

        return ['urls' => $urls, 'categories' => $categories];
    }

    /**
     * getProductUrl
     * Массив всех url Product.
     *
     * @var array<Category>
     */
    public function getProductUrl(array $categories): array
    {
        $urls = [];

        // Формируем url Product на основе их категорий
        $productServices = new ProductServices(
            new ProductRepository(new Hydrator(), new ImgRepository(new Hydrator()), new DocRepository(new Hydrator()))
        );
        $products = $productServices->getAllByActive();
        foreach ($products as $product) {
            if ($product->getCategory()->getValue() > 2) {
                foreach ($categories as $parent) {
                    if ($parent->getId()->getValue() === $product->getCategory()->getValue()) {
                        $urls[] .= $parent->getUrl()->getValue().'/'.$product->getUrl()->getValue();

                        break;
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * getUrl
     * Массив всех url Product & Category.
     */
    public function getUrl(): array
    {
        $categories = $this->getCategoryUrl();
        $urls = $categories['urls'];

        return array_merge($urls, $this->getProductUrl($categories['categories']));
    }
}
