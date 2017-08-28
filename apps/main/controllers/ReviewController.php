<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 27.08.17
 * Time: 19:13
 */

namespace main\controllers;

use main\components\ProjectConfig;
use yii\web\Controller;
use Yii;

class ReviewController extends Controller
{

    public function actionIndex() {
        /** @var ProjectConfig $projectConfig */
        $projectConfig = Yii::$app->projectConfig;
        $pages = $projectConfig->getPagesWithResolutions();

        return $this->render('index.html.twig', compact('pages'));
    }

    public function actionCompare($width, $name) {
        $this->layout = 'clean';

        /** @var ProjectConfig $projectConfig */
        $projectConfig = Yii::$app->projectConfig;
        $pages = $projectConfig->getPagesWithResolutions();

        $other_design_files = $projectConfig->getOtherDesignFiles($name);
        $other_global_design_files = $projectConfig->getOtherGlobalDesignFiles();
        $other_live_files = $projectConfig->getOtherLiveFiles($name, $width);

        $version = $projectConfig->config['global']['version'];

        if ($type = Yii::$app->request->get('type')) {
            $live = "/review/live/v{$version}/$name/$width-$type.png";
        } else {
            $live = "/review/live/v{$version}/$name/$width.png";
        }

        $design = "/review/design//$name/$width.png";

        return $this->render('compare.html.twig', [
            'design' => $design,
            'live' => $live,
            'name' => $name,
            'width' => $width,
            'pages' => $pages,
            'other_design_files' => $other_design_files,
            'other_global_design_files' => $other_global_design_files,
            'other_live_files' => $other_live_files,
        ]);
    }
}