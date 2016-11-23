<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "acc_and_gyro".
 *
 * @property integer $id
 * @property integer $test_id
 * @property double $x
 * @property double $y
 * @property double $z
 * @property double $gx
 * @property double $gy
 * @property double $gz
 * @property string $time
 */
class AccAndGyro extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acc_and_gyro';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['test_id'], 'required'],
            [['test_id'], 'integer'],
            [['x', 'y', 'z', 'gx', 'gy', 'gz'], 'number'],
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
            'id' => Yii::t('common', 'ID'),
            'test_id' => Yii::t('common', 'Test ID'),
            'x' => Yii::t('common', 'X'),
            'y' => Yii::t('common', 'Y'),
            'z' => Yii::t('common', 'Z'),
            'gx' => Yii::t('common', 'Gx'),
            'gy' => Yii::t('common', 'Gy'),
            'gz' => Yii::t('common', 'Gz'),
            'time' => Yii::t('common', 'Time'),
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
