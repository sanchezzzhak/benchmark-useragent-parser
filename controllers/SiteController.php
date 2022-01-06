<?php

namespace app\controllers;

use app\commands\MatomoParserController;
use app\commands\Mimmi20ParserController;
use app\forms\FinderUserAgentForm;
use app\grids\FinderUserAgentGrid;
use app\helpers\ParserConfig;
use app\models\BenchmarkResult;
use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new FinderUserAgentForm();
        $grid = new FinderUserAgentGrid([
            'provider' => $model->search(Yii::$app->request->get()),
        ]);

        return $this->render('index', compact('model', 'grid'));
    }

    public function actionDetect(int $id, int $parser)
    {
        $this->enableCsrfValidation = false;

        $name = ParserConfig::getNameById($parser);
        $row = BenchmarkResult::findOne(['id' => $id]);

        if ($row === null) {
            return $this->asJson([
                'success' => false,
                'error' => sprintf('Benchmark result record not found id[%s]', $id)
            ]);
        }

        switch ($name) {
            case ParserConfig::PROJECT_MATOMO_DEVICE_DETECTOR:
                $controller = new MatomoParserController($this->id, $this->module, []);
                $controller->saveParseResult($row, $parser);
                break;
            case ParserConfig::PROJECT_MIMMI20_BROWSER_DETECTOR:
                $controller = new Mimmi20ParserController($this->id, $this->module, []);
                $controller->saveParseResult($row, $parser);
                break;
        }

        return $this->asJson([

        ]);
    }

}
