<?php

namespace frontend\controllers;

use Yii;
use common\models\Test;
use common\models\TestSearch;
use common\models\Vechicle;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Gps;
use common\models\Vibration;
use common\models\AccAndGyro;

use yii\helpers\ArrayHelper;
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
                $vechicles = ArrayHelper::map(Vechicle::find()->all(),'id','name');
				return $this->render('create', [
					'model' => $model,
                    'vechicles' => $vechicles
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
            exec("bash /home/pi/python/GPSstop.sh");
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

        $gps = Gps::find()->where("test_id = $id AND speed > 1")->asArray()->all();
        $position =[];

        if($gps){
          array_push($position, array("Position", "details"));
      		for($i=0;$i<count($gps);$i+=10)
      		{
      				$pos = array($gps[$i]['latitude'].','.$gps[$i]['longitude'], $gps[$i]['time']);
      				array_push($position, $pos);
      		}
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
            'position' => $position

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
			array_push($data, array((float)$val['time'],(float)$val['x'],(float)$val['y'],(float)$val['z'],(float)$val['gx'],(float)$val['gy'],(float)$val['gz']));
		}
		echo json_encode($data);
	}

  public function actionGps($id){

    header("Content-type: text/json");
    //$acc = AccAndGyro::find()->where("(NOW() - INTERVAL 5 MINUTE) < time")->asArray()->all();
    $acc = Gps::find()->where("test_id = $id")->asArray()->all();
    $data = array();
    foreach($acc as $val){
      array_push($data, array($val['time'],(float)$val['speed']));
    }
    echo json_encode($data);
  }

  public function actionVibration($id){

      header("Content-type: text/json");
      $model = Vibration::find()
        ->select(['sensor_id', 'time','peakForce'])
        ->where("test_id = $id")
        ->asArray()
        ->all();
      $data = array();

      echo json_encode($model);
  }


  public function actionBrakingdistance($id){

  //  header("Content-type: text/json");
    //$acc = AccAndGyro::find()->where("(NOW() - INTERVAL 5 MINUTE) < time")->asArray()->all();
    $gps = Gps::find()->where("test_id = $id")->asArray()->all();

    //from the end
    $gps = array_reverse($gps);


    //sum distance
    $sum = 0;
    $min = $gps[0]['speed'];
    $minTime = 0;
    $isMinTime = false;
    //array to results
    $represions = array();
    $represion = array();
    for($i = 0; $i < count($gps)-1; ++$i) {
        if($gps[$i]['speed']<$gps[$i+1]['speed']){

          //Check that the vehicle at the end of the test is not standing
          if(!count($represions)&&!$isMinTime){
            $dif = round($gps[$i+1]['speed']-$gps[$i]['speed']);
            if($dif>0){
              $minTime = $gps[$i]['time'];
              $isMinTime = true;
            }

          }

          if($gps[$i]['speed']<$min)  {
            $min = $gps[$i]['speed'];
            $minTime = $gps[$i]['time'];
          }

          $distance = $this->distance($gps[$i]['longitude'],$gps[$i+1]['longitude'],$gps[$i]['latitude'],$gps[$i+1]['latitude']);
          $sum +=  $distance;
        }
        else if($sum && $minTime){
            // limit of small diffrence
            if(($gps[$i]['speed']-$min)>1){
                $represion = [
                  'minSpeed' => $min,
                  'maxSpeed' => $gps[$i]['speed'],
                  'time' => $minTime,
                  'distance' => round($sum*1000) // km to m
                ];
                $represions[] = $represion;
            }

          $min = $gps[$i]['speed'];
          $minTime = $gps[$i]['time'];
          $sum = 0;
        }

    }
    // time is reverse
    $represions = array_reverse($represions);

    echo json_encode($represions);

  }

  private function distance($lat1,$lat2,$lon1,$lon2){
    $R = 6371; // km
    $dLat = deg2rad($lat2-$lat1);
    $dLon = deg2rad($lon2-$lon1);
    $lat1 = deg2rad($lat1);
    $lat2 = deg2rad($lat2);

    $a = sin($dLat/2) * sin($dLat/2) +
         sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $d = $R * $c;

    return $d;
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
