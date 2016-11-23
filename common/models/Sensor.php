<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sensor".
 *
 * @property integer $id
 * @property string $name
 */
class Sensor extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sensor';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
        ];
    }
	
	/**
     * @return \yii\db\ActiveQuery
     */
    public function getVibrations()
    {
        return $this->hasMany(Vibration::className(), ['sensor_id' => 'id']);
    }
}
