<?php
/**
 * author     : forecho <caizhenghai@gmail.com>
 * createTime : 2018/1/31 18:14
 * description:
 */

namespace yiier\notification\actions;

use Yii;
use yii\web\Response;
use yiier\notification\models\Notification;

class NotificationAction extends \yii\base\Action
{
    public function init()
    {
        parent::init();
        \Yii::$app->controller->enableCsrfValidation = false;
    }

    public function run()
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->getResponse()->redirect(\Yii::$app->getUser()->loginUrl)->send();
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (!Yii::$app->request->isPost) {
                return ['code' => 404, 'data' => '', 'message' => 'please use post request'];
            }
            switch (Yii::$app->request->getBodyParam('action')) {
                case Notification::DEL_ACTION:
                    $id = Yii::$app->request->getBodyParam('id');
                    $return = $id ? Notification::del($id) : $id;
                    break;
                case Notification::DEL_ALL_ACTION:
                    $condition = [];
                    if ($ids = Yii::$app->request->getBodyParam('ids')) {
                        $condition = ['id' => explode(',', $ids)];
                    }
                    $return = Notification::delAll($condition);
                    break;

                case Notification::READ_ALL_ACTION:
                    $condition = [];
                    if ($ids = Yii::$app->request->getBodyParam('ids')) {
                        $condition = ['id' => explode(',', $ids)];
                    }
                    $return = Notification::readAll($condition);
                    break;
                default:
                    # code...
                    break;
            }

            if (isset($return)) {
                return ['code' => 200, 'data' => $return, 'message' => 'success'];
            }
            return ['code' => 500, 'data' => '', 'message' => 'fail'];
        }
    }
}