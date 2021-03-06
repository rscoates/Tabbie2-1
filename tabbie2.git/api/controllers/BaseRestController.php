<?php
/**
 * BaseRestController.php File
 *
 * @package  Tabbie2
 * @author   jareiter
 * @version
 */

namespace api\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use api\models\User;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\ActiveController as Controller;
use yii\web\Response;
use Yii;

/**
 * Class BaseRestController
 * @package api\controllers
 */
class BaseRestController extends Controller
{
    /**
     * @var string The model class to map
     */
    public $modelClass;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
            ],
            'authenticator' => [
                'class' => CompositeAuth::className(),
                'authMethods' => [
                    'basicAuth' => [
                        'class' => HttpBasicAuth::className(),
                        'auth' => function ($username, $password) {
                            $user = User::find()->where(['email' => $username])->one();
                            if (Yii::$app->security->validatePassword($password, $user->password_hash))
                                return $user;
                            return null;
                        }
                    ],
                    'bearerAuth' => [
                        'class' => HttpBearerAuth::className()
                    ]
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::className(),
                'enableRateLimitHeaders' => true, //Do not spoil
            ],
        ];
    }
}