<?php
namespace App\Controllers;
use App\Models\UserModel;
error_reporting(E_ALL);
ini_set('display_erros',1);


class Home extends BaseController
{   
    
    public function __construct(){
        helper(['url']);
        $this->user = new UserModel();
    }
    public function index()
    {
        echo view('inc/header');
        $data['users'] = $this->user->orderby('id',"DESC")->paginate(10,'group1');
        $data['pager'] = $this->user->pager; 
        echo view('home',$data);
        echo view('inc/footer');
    }

    public function saveUser(){
       $username = $this->request->getVar('name');
       $email = $this->request->getVar('email');
      

       $this->user->save(["name" => $username,"email"=>$email]);
       $mongoId = $this->user->insertID();

      // CURL api to create user
      $ch = curl_init();
      $newdata = [
          "_id"=>   $mongoId ,
          "name" => $username,
          "email" => $email
      ];
      $url = "http://localhost:5000/users/create";
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newdata));

      $response = curl_exec($ch);
      curl_close($ch);

      session()->setFlashData("sucess","Data added Sucessfully");
       return redirect()->to(base_url());
    }

    public function getSingleUser($id){
        $data = $this->user->where('id',$id)->first();
        echo json_encode($data);
    }

    public function updateUser(){
        $id = $this->request->getVar('updateId');
        $username = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $data['name'] = $username;
        $data['email'] = $email;     
        $this->user->update($id,$data);
       
        //Curl update user
        $ch = curl_init();
        $url = "http://localhost:5000/users/update/".$id;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        curl_close($ch);

        return redirect()->to(base_url());
    }

    public function deleteUser(){
        $id = $this->request->getVar('id');
        $this->user->delete($id);
        echo 1;

        $url = "http://localhost:5000/users/delete/".$id;


        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);    
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       
        $response = curl_exec($ch);

        curl_close($ch);
        //  return redirect()->to(base_url("/"));
    }
     

    public function deleteAllUser(){
        
        $ids = $this->request->getVar('ids');
        for($i=0;$i<count($ids);$i++){
             $this->user->delete($ids[$i]);
            
        }
        $assosiate = ['ids'=>$ids];
        $url = "http://localhost:5000/users/deleteAll";


        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);    
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($assosiate));
        $response = curl_exec($ch);

        curl_close($ch);
       
    }

}
