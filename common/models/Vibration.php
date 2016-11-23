<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vibration".
 *
 * @property integer $id
 * @property integer $test_id
 * @property integer $sensor_id
 * @property string $time
 * @property double $peakForce
 */
class Vibration extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vibration';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id', 'peakForce'], 'required'],
            [['test_id', 'sensor_id'], 'integer'],
            [['time'], 'safe'],
            [['peakForce'], 'number'],
            [['sensor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sensor::className(), 'targetAttribute' => ['sensor_id' => 'id']],
            [['test_id'], 'exist', 'skipOnError' => true, 'targetClass' => Test::className(), 'targetAttribute' => ['test_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'test_id' => Yii::t('common', 'Test ID'),
            'sensor_id' => Yii::t('common', 'Sensor ID'),
            'time' => Yii::t('common', 'Time'),
            'peakForce' => Yii::t('common', 'Peak Force'),
        ];
    }
}
