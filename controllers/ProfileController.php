<?php
namespace controllers;

use Exception;

use models\Users;
use components\Controller;

class ProfileController extends Controller {

    public function pageIndex() {
        if (LOGGED_IN) {
            $model = Users::findOne(USER_ID);
            return $this->render('index', [
                'model' => $model,
            ]);
        } else {
            return $this->redirect('/');
        }
    }

    public function pageEdit() {
        if (LOGGED_IN) {
            if (isset($_POST) && !empty($_POST)) {
                $model = Users::findOne(USER_ID);
                try {
                    $model->load($_POST);
                    if (isset($_FILES) && !empty($_FILES)) {
                        if ($_FILES["image"]["name"] !== "") {
                            $tmp_name = $_FILES["image"]["tmp_name"];
                            $name = basename($_FILES["image"]["name"]);
                            if ($model->image !== $name) {
                                if (!file_exists(UPLOADS_DIR . "/" . $name)) {
                                    move_uploaded_file($tmp_name, UPLOADS_DIR . "/" . USER_ID . "/" . $name);
                                }
                                $model->image = $name;
                            }
                        }  else {
                            $model->image = "null";
                        }
                    }

                    if ($model->save()) {
                        return $this->render('edit', [
                            'model' => $model,
                            'success' => 'Data saved correctly' . ".",
                        ]);
                    } else {
                        return $this->render('edit', [
                            'model' => $model,
                            'error' => 'There was en error during saving data' . ".",
                        ]);
                    }
                } catch (Exception $e) {
                    echo $e->getMessage();
                }
            } else {
                $model = Users::findOne(USER_ID);
                return $this->render('edit', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->redirect('/');
        }
    }

    public function pageOffers() {
        if (LOGGED_IN) {
            $offers['published'] = Offers::find()->where(['user_id' => USER_ID, 'status' => 'published'])->orderBy('updated_at DESC')->all();
            $offers['sketch'] = Offers::find()->where(['user_id' => USER_ID, 'status' => 'sketch'])->orderBy('updated_at DESC')->all();
            return $this->render('offers', [
                'offers' => $offers,
            ]);
        } else {
            return $this->redirect('/');
        }
    }
}