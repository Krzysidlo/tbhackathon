<?php
namespace controllers;

use Exception;
use models\Users;

use components\Controller;

class IndexController extends Controller {

    public function pageIndex() {
	    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
		    return $this->redirect('/index/login');
	    } else {
	    	try {
	    		$user = Users::findOne($_SESSION['user_id']);
		    } catch (Exception $e) {
	    		echo $e->getMessage();
	    		die();
		    }
		    return $this->render('index', [
			    'user' => $user,
		    ]);
	    }
    }

    public function pageLogin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
	        if (isset($_POST['username']) && isset($_POST['password'])) {
		        $username = $_POST['username'];
		        $password = $_POST['password'];
		        if ($username !== null && $password !== null) {
			        if ($user = Users::find()->where(['username' => $username])->one()) {
				        $salt = $user->salt;
				        $password = Users::hashPassword($password, $salt);
				        if ($user->password === $password) {
					        $_SESSION['user_id'] = $user->primaryKey;
					        $_SESSION['username'] = $user->username;
				        }
			        }
		        }
		        return $this->redirect('/index');
	        } else {
		        try {
			        $model = new Users;
		        } catch (Exception $e) {
			        echo $e->getMessage();
			        die();
		        }
		        return $this->render('login', [
			        'model' => $model,
		        ]);
	        }
        } else {
	        return $this->redirect('/index');
        }
    }

	public function pageRegister() {
        try {
	        $model = new Users;
        } catch (Exception $e) {
        	echo $e->getMessage();
        	die();
        }
        if (isset($_POST['confirm_password'])) {
        	unset($_POST['confirm_password']);
        }
        if ($model->load($_POST)) {
            $salt = "";
            for ($i = 0; $i < 22; $i++) {
                $rand = rand(0, 9);
                $salt .= $rand;
            }
            $model->salt = $salt;
            $model->password = Users::hashPassword($model->password, $salt);

	        if (isset($_FILES) && !empty($_FILES)) {
		        if ($_FILES["image"]["name"] !== "") {
			        $tmp_name = $_FILES["image"]["tmp_name"];
			        $name = basename($_FILES["image"]["name"]);
			        if ($model->image !== $name) {
			        	move_uploaded_file($tmp_name, IMG_DIR . "/" . $name);
				        $model->image = $name;
			        }
		        } else {
			        $model->image = "null";
		        }
	        }
            if ($model->save()) {
                $_SESSION['user_id'] = $model->primaryKey;
                $_SESSION['username'] = $model->username;
                return $this->redirect('index');
            } else {
                return $this->redirect('register');
            }
        } else {
            return $this->render('register', [
                'model' => $model,
            ]);
        }
    }

    public function pageLogout() {
        session_destroy();
        return $this->redirect('/index');
    }

    public function pageGetWeather() {
    	if (isAjax()) {
    		if (isset($_POST['lat']) && isset($_POST['lng'])) {
    			$lat = $_POST['lat'];
    			$lng = $_POST['lng'];
    			if ($data = file_get_contents("http://api.openweathermap.org/data/2.5/weather?lat=" . $lat . "&lon=" . $lng . "&appid=d0a10211ea3d36b0a6423a104782130e")) {
				    echo json_encode([
					    'success' => true,
					    'data' => $data,
				    ]);
				    die();
			    } else {
				    echo json_encode([
					    'success' => false,
				    ]);
				    die();
			    }
		    } else {
    			echo json_encode([
    				'success' => false,
			    ]);
    			die();
		    }
	    }
    }
}