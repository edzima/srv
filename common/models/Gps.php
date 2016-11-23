<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "gps".
 *
 * @property integer $test_id
 * @property double $longitude
 * @property double $latitude
 * @property double $altitude
 * @property double $speed
 * @property string $time
 * @property integer $id
 */
class Gps extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gps';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'longitude', 'latitude', 'altitude', 'speed'], 'required'],
            [['test_id'], 'integer'],
            [['longitude', 'latitude', 'altitude', 'speed'], 'number'],
            [['time'], 'safe'],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'test_id' => Yii::t('common', 'Test ID'),
            'longitude' => Yii::t('common', 'Longitude'),
            'latitude' => Yii::t('common', 'Latitude'),
            'altitude' => Yii::t('common', 'Altitude'),
            'speed' => Yii::t('common', 'Speed'),
            'time' => Yii::t('common', 'Time'),
            'id' => Yii::t('common', 'ID'),
        ];
    }
	
	 /**
     * @return \yii\db\ActiveQuery
     */
    public function getTest()
    {
        return $this->hasOne(Test::className(), ['id' => 'test_id']);
    }
}
