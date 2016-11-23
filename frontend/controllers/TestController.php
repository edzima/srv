<?php

namespace frontend\controllers;

use Yii;
use common\models\Test;
use common\models\TestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Gps;
use common\models\Vibration;
use common\models\AccAndGyro;

/**
 * TestController implements the CRUD actions for Test model.
 */
class TestController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
			'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all Test models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


	


    /**
     * Creates a new Test model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
	public function actionCreate()
    {
		//test is started
		if ($this->status()){
			$id = Test::getLastID();
			echo 'test its runnning now, do you want stopped it?';
			//return $this->redirect(['test', 'id' => $id]);
			
		}
		//test isn't started, create new test 
		else{
			$model = new Test();
			$model->user_id=Yii::$app->user->identity->id;
			if ($model->load(Yii::$app->request->post()) && $model->save()) {
				
				exec("sudo bash /home/pi/python/srv.sh start $model->id > /dev/null 2>&1 &");
				return $this->redirect(['test', 
					'id' => $model->id
				]);
				
			} else {
				return $this->render('create', [
					'model' => $model,
				]);
			}
		}
    }

    /**
     * Updates an existing Test model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Test model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
	
	// @param boolean status 
	public function status()
	{
		$script = '/home/pi/python/srv.sh status';
		$cmd="bash $script";
		$scriptStatus = shell_exec($cmd);
		$scriptStatus =intval($scriptStatus);
		
		return $scriptStatus;

	}
	
	//exexuce script to stop test 
	public function actionStop($id){
		
		$model = $this->findModel($id);
		$model->finish_at = '';
		if(@exec("sudo bash /home/pi/python/srv.sh stop") && $model->save()){
			Yii::$app->session->setFlash('stopTest', Yii::t('common', 'Stoped test'));
            return Yii::$app->getResponse()->redirect(['test/view', 
					'id' => $model->id
				]);
		}
		else {
			Yii::$app->session->setFlash('stopTest', Yii::t('common', 'Stoped test'));
			return Yii::$app->getResponse()->redirect(['test/test', 
					'id' => $model->id
				]);
		}
	}
	
	/**
     * Displays a single Test model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {	
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
	
	//during make Test
	public function actionTest($id){
		
		if($this->status()){
			return $this->render('test', [
				'model' => $this->findModel($id),
			]);
		}
		else return $this->redirect(['create']);
   
	}
	
	
	public function actionSpeed($id){
		header("Content-type: text/json");
		$gps = Gps::find()->where("test_id = $id AND speed > 2")->asArray()->all();
		
		$data = array();
		foreach($gps as $speed){
			$dane = array(strtotime($speed['time'])*1000,(float)$speed['speed']);
			array_push($data, $dane);
		}
		echo json_encode($data);
	}

	public function actionAcc($id){
		
		header("Content-type: text/json");
		//$acc = AccAndGyro::find()->where("(NOW() - INTERVAL 5 MINUTE) < time")->asArray()->all();
		$acc = AccAndGyro::find()->where("test_id = $id")->asArray()->all();		
		$data = array();
		foreach($acc as $val){
			array_push($data, array($val['time'],(float)$val['x'],(float)$val['y'],(float)$val['z'],(float)$val['gx'],(float)$val['gy'],(float)$val['gz']));
		}
		echo json_encode($data);
	}
	
	
	public function actionVibration($id){
		
		header("Content-type: text/json");
		$model = Vibration::find()->where("test_id = $id")->asArray()->all();		
		$data = array();
		foreach($model as $val){
			array_push($data, array($val['time'],(float)$val['peakForce']));
		}
		echo json_encode($data);
	}

    /**
     * Finds the Test model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Test the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Test::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
