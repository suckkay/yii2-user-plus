<?php
namespace johnitvn\userplus\base\actions;

use Yii;
use johnitvn\userplus\base\Action;
use johnitvn\userplus\base\traits\AjaxValidationTrait;
use app\models\School;

/**
* Login action will be handler user login request
* @author John Martin <john.itvn@gmail.com>
* @since 1.0.0
*/
class LoginAction extends Action{
    
    use AjaxValidationTrait;
    
    /**
     * @var string the view file to be rendered. If not set, it will take the value of [[id]].
     * That means, if you name the action as "error" in "SiteController", then the view name
     * would be "error", and the corresponding view file would be "views/site/error.php".
     */
    public $view;

    /**
     * Runs the login action.
     * Afther login action application will to redirect to back url user request
     * ````php
     * $this->controller->goBack()
     * ````
     * @return string result content
     */
    public function run() {
        $model = $this->userPlusModule->createModelInstance('LoginForm'); 
                
        $this->performAjaxValidation($model);
        
        if ($model->load(Yii::$app->request->post()) && $model->login()){
            if (Yii::$app->user->can('Administrator_permission')){
                    return $this->controller->goHome();
                }
            else if($this->CheckSchool() == true){
                return $this->controller->goHome();
            }
            else {
                return Yii::$app->getResponse()->redirect(Yii::$app->request->baseUrl.'/site/wizard');
            }
        } else {
            $view = $this->view == null ? $this->id : $this->view;
            return $this->controller->render($view, [
                        'model' => $model,
            ]);
        }
    }

    public function CheckSchool(){
        $data = School::find()
            ->where(['creator_id' => Yii::$app->user->identity->id])
            ->one();
        if($data == null) return false; else return true;
    }


}