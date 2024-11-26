<?php
  namespace App\Controllers;
  use App\Models\RegisterModel;
  use Config\Encryption;

  class Register extends BaseController{
   
    public function __construct(){
      helper(['url']);
      $this->register = new RegisterModel();
    }


    public function index(){
       return view('Register');
    
  }

   public function register(){
    $email = $this->request->getVar('email');
    $password = $this->request->getVar('password');      
    $confirmPassword = $this->request->getVar('confirmpassword');
  

    $hash_password = password_hash($password,PASSWORD_DEFAULT);



    if($password == $confirmPassword){
      $registerData = $this->register->save([
        'email' => $email,
        'password' => $hash_password,
        
      ]);
      return redirect()->to(base_url("/")); 
    }else{
      session()->setFlashData("error","Password and Confirm Password does not Match") ; 
      return redirect()->to(base_url("/register"));
    }
   }
}
?>