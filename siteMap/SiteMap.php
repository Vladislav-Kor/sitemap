<?php

declare(strict_types=1);

namespace app\models\siteMap;

use app\repositories\category\CategoryRepository;
use app\repositories\doc\DocRepository;
use app\repositories\Hydrator;
use app\repositories\img\ImgRepository;
use app\repositories\product\ProductCrmRepository;
use app\repositories\product\ProductOfferRepository;
use app\repositories\product\ProductRepository;
use app\services\category\CategoryService;
use app\services\product\ProductOfferServices;
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
        $offerServices = new ProductOfferServices(
            new ProductOfferRepository(new Hydrator(), new ImgRepository(new Hydrator())),
            new ProductCrmRepository(new Hydrator())
        );
        $products = $productServices->getAllByActive();
        foreach ($products as $product) {
            // Проверяю что категория товара не главная страница и не каталог
            if ($product->getCategory()->getValue() > 2) {
                foreach ($categories as $parent) {
                    if ($parent->getId()->getValue() === $product->getCategory()->getValue()) {
                        // Определяю отдельные страницы предложения товара
                        if ($product->getPrefix()->getValue()) {
                            $offers = $offerServices->getAllOfferByProduct($product->getId());
                            foreach ($offers as $offer) {
                                $urls[] .= $parent->getUrl()->getValue().'/'.$product->getUrl()->getValue().'/'.$offer->getId()->getValue();
                            }

                            break;
                        }
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
        if (!\Yii::$app->cache->get('sitemap')) {
            $categories = $this->getCategoryUrl();
            // сохраняю url всех активные категрии
            $urls = $categories['urls'];
            // нахожу все актвиные продукты в категориях и сохраняю их url
            $urls = array_merge($urls, $this->getProductUrl($categories['categories']));
            // кеширую данные
            \Yii::$app->cache->set('sitemap', $urls, 3600 * 6);
            return $urls;
            
        }
        return  \Yii::$app->cache->get('sitemap');
    }

    /**
     * changeUrl
     * Изменяю Массив всех url Product & Category.
     */
    public function changeUrl(): void
    {
        $categories = $this->getCategoryUrl();
        $urls = $categories['urls'];
        $urls = array_merge($urls, $this->getProductUrl($categories['categories']));
        \Yii::$app->cache->set('sitemap', $urls, 3600 * 6);
    }
}
