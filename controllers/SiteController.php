<?php

namespace app\controllers;

use app\forms\FinderUserAgentForm;
use app\grids\FinderUserAgentGrid;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

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

}
