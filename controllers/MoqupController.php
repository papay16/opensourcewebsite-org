<?php

namespace app\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use app\models\User;
use app\models\Moqup;
use app\models\MoqupSearch;
use app\models\Css;
use yii\db\Query;

class MoqupController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['design-add', 'design-list', 'design-view', 'design-edit', 'design-delete', 'design-preview'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'design-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    
    /**
     * Shows a list of the registered moqups
     */
    public function actionDesignList($viewYours = false)
    {
        $searchModel = new MoqupSearch();
        $params = Yii::$app->request->queryParams;

        if ($viewYours) {
            $params['viewYours'] = true;
        }

        $dataProvider = $searchModel->search($params);

        $countYours = Moqup::find()->where(['user_id' => Yii::$app->user->identity->id])->count();
        $countAll = Moqup::find()->where(['!=', 'user_id', Yii::$app->user->identity->id])->count();
        
        return $this->render('design-list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'viewYours' => $viewYours,
            'countYours' => $countYours,
            'countAll' => $countAll,
        ]);
    }

    public function actionDesignView($id)
    {
        $moqup = Moqup::findOne($id);
        $css = $moqup->css;
        
        return $this->render('design-view', ['moqup' => $moqup, 'css' => $css]);
    }

    /**
     * Create or edit a moqup
     * @param integer $id The id of the moqup to be updated
     */
    public function actionDesignEdit($id = null)
    {
        if ($id == null) {
            $moqup = new Moqup(['user_id' => Yii::$app->user->identity->id]);
            $css = new Css;
        } else {
            $moqup = Moqup::findOne($id);

            if ($moqup == null) {
                throw new \yii\web\NotFoundHttpException;
            }

            if ($moqup->css != null) {
                $css = $moqup->css;
            } else {
                $css = new Css;
            }
        }

        if ($moqup->load(Yii::$app->request->post()) && $css->load(Yii::$app->request->post())) {
            $success = false;
            $transaction = Yii::$app->db->beginTransaction();

            if ($moqup->save()) {
                $success = true;

                if ($css->css != '') {
                    $css->moqup_id = $moqup->id;

                    if ($css->save()) {
                        $success = true;
                    } else {
                        $success = false;
                    }
                }

                if ($success) {
                    $transaction->commit();
                    return $this->redirect(['moqup/design-list', 'viewYours' => true]);
                } else {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('design-edit', [
            'moqup' => $moqup,
            'css' => $css,
        ]);
    }

    /**
     * Deletes a moqup
     * @param integer $id The id of the moqup being deleted
     */
    public function actionDesignDelete($id)
    {
        $moqup = Moqup::findOne($id);

        $css = Css::find()
            ->where(['moqup_id' => $id])
            ->one();

        if ($moqup != null) {
            if ($css != null) {
                if (!$css->delete()) {
                    return false;
                }
            }

            if ($moqup->delete()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Renders a page to preview the moqups
     */
    public function actionDesignPreview()
    {
        $this->layout = 'adminlte-moqup-preview';
        return $this->render('design-preview');
    }

    /**
     * Do tasks before the action is executed
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (Yii::$app->user->isGuest) {
            $this->layout = 'adminlte-guest';
        } else {
            $this->layout = 'adminlte-main';
        }

        return true;
    }

}
