<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "test".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $vehicle_id
 * @property string $start_at
 * @property string $finish_at
 */
class Test extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'vehicle_id'], 'integer'],
            [['start_at', 'finish_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'vehicle_id' => Yii::t('common', 'Vehicle ID'),
            'start_at' => Yii::t('common', 'Start At'),
            'finish_at' => Yii::t('common', 'Finish At'),
        ];
    }

	// when test is started, get it id
	public function getLastID(){
		return Test::find()
				->where(['user_id' => Yii::$app->user->identity->id])
				->orderBy(['id' => SORT_DESC])
				->one()->id;
	}
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccAndGyros()
    {
        return $this->hasMany(AccAndGyro::className(), ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGps()
    {
        return $this->hasMany(Gps::className(), ['test_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVibrations()
    {
        return $this->hasMany(Vibration::className(), ['test_id' => 'id']);
    }

    public function getVehicle(){
      return $this->hasOne(Vechicle::className(), ['id'=>'vehicle_id']);
    }

    public function getUser(){
      return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
