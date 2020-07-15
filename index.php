<?php
class Demon_Auth{
    	public $auth_key = "";
	
    	public $logged_in = false;
	
	public function __construct($key){
		$this->auth_key = self::sanitize($key);
	}
	
	static function sanitize($val){
		return htmlspecialchars($val);
	}
	
	public function login(){
		if(!empty($this->auth_key) && !$this->logged_in){
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL =>sprintf("https://demonforums.net/auth.php?dfauth=%s", $this->auth_key),
				CURLOPT_POST => 1
			]);
			$resp = curl_exec($curl);
			curl_close($curl);
			$return = json_decode($resp, true);
			if($return['Auth']['Status'] == "Active") return "Login Successfull!<br>Welcome : <b>".$return['Auth']['Username']."</b>";
			else return "Failed To Login<br>Reason : ".$return['Auth']['Status'];
		}
		else
			return "No Auth Key Set!";
	}
	
	public function is_banned(){
		if(!empty($this->auth_key)){
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL =>sprintf("https://demonforums.net/auth.php?dfauth=%s", $this->auth_key),
				CURLOPT_POST => 1
			]);
			$resp = curl_exec($curl);
			curl_close($curl);
			$return = json_decode($resp, true);
			switch($return['Auth']['Banned']){
				case 0: return $return['Auth']['Username']." Is Not Banned!"; break;
				case 1: return $return['Auth']['Username']." Is Banned!"; break;
				default: return "We Failed To Check If ".$return['Auth']['Username']." Is Banned!"; break;
			}
		}
		else
			return "No Auth Key Set / Not Logged In!";
	}
}


if(isset($_POST['login'])){
	session_start();
	ob_start();
	$DFAUTH = new Demon_Auth($_POST['auth_key']);
	$DFResp = sprintf("<div class=\"alert alert-primary\">%s<hr>%s</div>", $DFAUTH->login(), $DFAUTH->is_banned());
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <title>Demon Auth</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
	  <style>
	     .alert-primary {
			color: white;
			background-color: #337ab7;
			border-color: #337ab7;
		}
	  </style>
   </head>
   <body>
      <div class="container text-center">
         <h2>Demon Auth</h2>
         <div class="panel-group">
            <div class="panel panel-primary">
               <div class="panel-heading">
                  Login With Your Demon Auth Key
               </div>
               <form method="POST">
                  <div class="panel-body">
					 <?php if(!empty($DFResp)){ echo $DFResp; } ?>
                     <div class="form-group">
                        <label for="auth_key">Enter Your Auth Key</label>
                        <input type="text" name="auth_key" id="auth_key" class="form-control text-center" placeholder="XXXX-XXXX-XXXX-XXXX">
                     </div>
                  </div>
                  <div class="panel-footer">
                     <button type="submit" name="login" id="login" class="btn btn-block btn-primary">Login</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </body>
</html>
