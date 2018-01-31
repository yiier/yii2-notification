<?php

namespace yiier\notification\models;

use common\models\User;
use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%notification}}".
 *
 * @property int $id
 * @property string $type
 * @property int $from_user_id
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property string $model
 * @property int $model_id
 * @property int $status
 * @property int $created_at
 */
class Notification extends \yii\db\ActiveRecord
{
    /**
     * @var int unread
     */
    const STATUS_UNREAD = 0;

    /**
     * @var int read
     */
    const STATUS_READ = 1;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notification}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'user_id', 'content'], 'required'],
            [['from_user_id', 'user_id', 'model_id', 'status', 'created_at'], 'integer'],
            [['content'], 'string'],
            [['type', 'model'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'type' => Yii::t('app', 'Type'),
            'from_user_id' => Yii::t('app', 'From User ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'model' => Yii::t('app', 'Model'),
            'model_id' => Yii::t('app', 'Model ID'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $this->created_at = time();
            return true;
        } else {
            return false;
        }
    }


    /**
     * @return int
     */
    public static function readAll()
    {
        return self::updateAll(['user_id' => Yii::$app->user->id]);
    }


    /**
     * @param $id
     * @return int
     */
    public static function read($id)
    {
        return self::updateAll(['user_id' => Yii::$app->user->id, 'id' => $id]);
    }


    /**
     * @return int
     */
    public static function delAll()
    {
        return self::deleteAll(['user_id' => Yii::$app->user->id]);
    }


    /**
     * @param $id
     * @return int
     */
    public static function del($id)
    {
        return self::deleteAll(['user_id' => Yii::$app->user->id, 'id' => $id]);
    }

    /**
     * @return int
     */
    public static function unreadCount()
    {
        return self::find()->where(['user_id' => Yii::$app->user->id, 'status' => self::STATUS_UNREAD])->count('id');
    }

    /**
     * @param $type
     * @param $toUserId
     * @param $content
     * @param array $params Optional ['from_user_id', 'title', 'model', 'model_id']
     * @param string $userNotification Optional user Notification field
     * @throws Exception
     */
    public static function create($type, $toUserId, $content, $params = [], $userNotification = '')
    {
        $model = new self();
        $model->setAttributes(array_merge([
            'type' => $type,
            'user_id' => $toUserId,
            'content' => $content,
        ], $params));
        if ($model->save()) {
            if ($userNotification) {
                User::updateAllCounters([$userNotification => 1], ['id' => $toUserId]);
            }
        } else {
            throw new Exception(array_values($model->getFirstErrors())[0]);
        }
    }


    /**
     * @param $type
     * @param $content
     * @param array $params Optional ['from_user_id', 'title', 'model', 'model_id']
     * @param string $userNotification Optional user Notification field
     * @throws Exception
     */
    public static function createToAllUser($type, $content, $params = [], $userNotification = '')
    {
        $items = User::find()->column();
        $rows = [];
        foreach ($items as $key => $item) {
            $rows[$key]['user_id'] = $item;
            $rows[$key]['type'] = $type;
            $rows[$key]['content'] = $content;
            $rows[$key] = array_merge($rows[$key], $params);
        }
        if (!self::saveAll(self::tableName(), $rows)) {
            throw new Exception('create to all user notifications errors');
        }
        if ($userNotification) {
            self::syncUserNotificationCount($userNotification);
        }
    }

    /**
     * @param $userNotification
     */
    public static function syncUserNotificationCount($userNotification)
    {
        $items = User::find()->column();
        foreach ($items as $key => $item) {
            $unreadCount = self::find()->where(['user_id' => $item, 'status' => self::STATUS_UNREAD])->count('id');
            User::updateAll([$userNotification => $unreadCount, 'id' => $item]);
        }
    }

    /**
     * @param $tableName
     * @param array $rows
     * @return bool|int
     */
    public static function saveAll($tableName, $rows = [])
    {
        if ($rows) {
            return \Yii::$app->db->createCommand()
                ->batchInsert($tableName, array_keys(array_values($rows)[0]), $rows)
                ->execute();
        }
        return false;
    }
}
