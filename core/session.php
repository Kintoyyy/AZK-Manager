<?php
// A class to help work with sessions
// In our case, primarily to manages logging users in and out

// Keep in mind when working with sessions that it is generally
//inadvisable to store DB-related objects in sessions

class Session{

    private $logged_in;
    public $user_id;
    public $message;
	public $username;

    function __construct(){
        if (empty($_SESSION)  && !isset($_SESSION))  {
            session_start();
        }
       
        $this->check_login();

        if($this->logged_in){
            //actions to take right away if user is logged in
        }else{
            //actions to take right away if user is not logged in
        }

    }
    public function is_logged_in(){
        return $this->logged_in;
    }


    public function login($username, $id){
        //database should find user based on username / password

        if($username){
            $userdata = array('username' => $username, 'userid' => $id);            
            $this->username     = $_SESSION['userdata']     = $userdata;            
            $this->logged_in    = true;
        }
    }
    public function logout(){
        unset ($_SESSION['userdata']);        
        $this->logged_in = false;
    }
    public function message($msg=""){
        if(!empty($msg)){
            //then this is set message
            //make sure you understand why $this->message=$msg wouldn't work
            $_SESSION['message'] = $msg;
        }  else {
            //then this is get message
            return $this->message;
        }
    }
    public function check_login(){
        if(isset($_SESSION['userdata'])){           
            $this->logged_in = true;
            return true;
        }else{          
            $this->logged_in = false;
            return false;
        }
    }
    private function check_message(){
        //is there a message stored in the session
        if(isset($_SESSION['message'])){
            //add it as an attribute and erase the stored version
            $this->message = $_SESSION['message'];
            unset($_SESSION['message']);
        }else{
            $this->message= "";
        }
    }
}
$session = new Session();

?>