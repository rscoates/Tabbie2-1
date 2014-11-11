<?php

namespace frontend\controllers;

use Yii;
use common\models\Tournament;
use common\models\search\TournamentSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\components\filter\TournamentContextFilter;

/**
 * TournamentController implements the CRUD actions for Tournament model.
 */
class TournamentController extends BaseController {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'tournamentFilter' => [
                'class' => TournamentContextFilter::className(),
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tournament models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TournamentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a current Tournament model.
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Tournament model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tournament();

        if (Yii::$app->request->isPost) {
            $file = \yii\web\UploadedFile::getInstance($model, 'logo');
            $model->load(Yii::$app->request->post());
            $path = "/uploads/TournamentLogo-" . $model->url_slug . ".jpg";
            if ($file && $file->saveAs(Yii::getAlias("@frontend/web") . $path))
                $model->logo = $path;
            else
                $model->logo = null;

            if ($model->validate()) {
                $model->save();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Tournament model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost) {

            //Upload File
            $file = \yii\web\UploadedFile::getInstance($model, 'logo');

            //Save Old File Path
            $oldFile = $model->logo;
            //Load new values
            $model->load(Yii::$app->request->post());
            $path = "/uploads/TournamentLogo-" . $model->url_slug . ".jpg";

            if ($file !== NULL) {
                //Save new File
                if ($file->saveAs(Yii::getAlias("@frontend/web") . $path))
                    $model->logo = $path;
            } else
                $model->logo = $oldFile;

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Tournament model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tournament model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tournament the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tournament::findOne(["id" => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}