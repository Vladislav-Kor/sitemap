<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\siteMap\SiteMap;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionSitemap()
    {
        $siteMap = new SiteMap();
        $urls = $siteMap->getUrl();

        return $this->renderPartial('sitemap', ['urls' => $urls]);
    }
}
