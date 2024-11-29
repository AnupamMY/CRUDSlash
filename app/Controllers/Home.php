<?php
namespace App\Controllers;
use App\Models\UserModel;
ini_set('max_execution_time',3600);     


class Home extends BaseController
{   
    
    public function __construct(){
        helper(['url']);
        $this->user = new UserModel();
    }
    public function index()
    {   
        $name = $this->request->getVar('name');
        $email = $this->request->getVar('email');
        $id = $this->request->getVar('id');
        
        $data['all_users'] = $this->user->orderby('name','ASC')->findAll();
        $search = $this->request->getVar("search");
        if($search) {
            $this->user->like('name', "$search%", 'after')->orderBy('name', 'ASC');
            //$print_r($data['users']);
        }
 
            $query = $this->user;
            // orwhere joins query with the name,age,email
            // username != 'Anupam' OR age = 50 OR email = 'anupam@gmail.com'
            if($name) {
                $query = $query->where('name', $name);
            }
            if($id) {
                $query = $query->orWhere('id', $id);
            }
            if($email) {
                $query = $query->orWhere('email', $email);
            }
            
    
        $data['users'] = $this->user->orderby('name','ASC')->paginate(5,'group1');
        
        
           echo view('/inc/header');
           //$data['all_users'] = $this->user->paginate(5,"group1");
           $data['pager'] = $this->user->pager;
           echo view('home', $data);
           echo view('/inc/footer');
           
    //     if($this->request->getVar("search")){
    //     $search = $this->request->getVar("search");
    //     echo view('inc/header');
    //    $data['users'] = $this->user->like("name","$search%",'after')->orderby("name","ASC")->findAll();//->paginate(10,'group1');
    //    // $data['users'] = $this->user->like("id","$search%",'after')->paginate(5,'group1');
    //     //$data['users'] = $this->user->like("email","$search%",'after')->paginate(5,'group1');
    //     //$data['pager'] = $this->user->pager; 
    //     echo view('home',$data);
    //     echo view('inc/footer');
    //     }else{
    //     echo view('inc/header');
        
    //      $data['users'] = $this->user->orderby('id',"DESC")->findAll();
    //     //print_r($data['users']);
    //     //$data['pager'] = $this->user->pager; 
    //     echo view('home',$data);
    //     // echo view('inc/footer');
    //     }
       
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
       return redirect()->to(base_url("/home"));
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

        return redirect()->to(base_url("/home"));
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


        echo 1;
    }
    

    // public function filterUser(){
    // $name = $this->request->getVar('name');
    // $email = $this->request->getVar('email');
    // $id = $this->request->getVar('id');

    // //     if($name || $email || $id){
    // //     $data['users'] = $this->user->where("name",$name)->findAll();//paginate(5,'group1');
    // //     $x['users'] = $this->user->where("email",$email)->findAll();//paginate(5,'group1');
    // //     $y['users'] = $this->user->where("id",$id)->findAll();//paginate(5,'group1');
        

    // //     $data['users'] = array_merge($data['users'],$x['users'],$y['users']);
    // //     $new_Array = []; 
    // //      //print_r($y);
    // //     for($i=0;$i<count($data['users']);$i++){
    // //        $item = $data['users'][$i];
    // //        $id = $item['id'];

    // //        if(!array_key_exists($id,$new_Array)){
    // //         $new_Array[$id] = [
    // //             'id' => $item['id'],
    // //             'name' => $item['name'],
    // //             'email' => $item['email'],
    // //         ];
    // //        }
    // //     }
    // //     $data['users'] = $new_Array;
    // //     //print_r($data['users']);
    // //     // $data['users'] = $this->user->pager;
    // //     // echo $data['users'];
    // //         echo view('inc/header');
    // //         //$data['pager'] = $this->user->pager;
    // //         echo view("home",$data);
    // //         echo view('inc/footer');
    // //     //return redirect()->to(base_url("/"));
    // // }

    // }

    public function download(){
        $filename = 'users_data' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");

        $userModel = new UserModel();
        $users = $userModel->findAll();
      
        $fp = fopen('php://output', 'w');
        $csvData = ['ID', 'Name', 'Email']; // Adjust according to your table structur
        
        fputcsv($fp, $csvData);

        foreach ($users as $key => $value) {
            fputcsv($fp, $value);
        }

        fclose($fp);
        exit();
    }



    public function uploadFile() {

        $file = $this->request->getFile('uploadFile');
        //echo $file;
        // Check if there was an upload error
        
    
        // Validate file type
        $ext = $file->getClientExtension();
        if ($ext !== 'csv') {
            return redirect()->back()->with('error', 'Only CSV files are allowed');
        }
    
        try {
            // Create uploads directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
    
            // Move uploaded file
            $newName = $file->getClientName();
            if (!$file->move($uploadPath, $newName)) {
                return redirect()->back()->with('error', 'Failed to move uploaded file');
            }
    
            $filepath = $uploadPath . $newName;


            $mongoData = [];
            $emptyData = [];

            // Process CSV file
            if (($handle = fopen($filepath, "r")) !== FALSE) {
                $userModel = new UserModel();
                $db = \Config\Database::connect();
                $db->transStart(); // Start transaction
    
                $firstRow = true;
                $successCount = 0;
                $errorCount = 0;
                
               
                while (($filedata = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Skip header row
                    if ($firstRow) {
                        $firstRow = false;
                        continue;
                    }
    
                    // Ensure we have all required fields
                    if (count($filedata) >= 2) { // Adjust based on your CSV structure
                        $data = [
                            'name' => trim($filedata[0]), // Assuming first column is name
                            'email'    => trim($filedata[1])  // Assuming second column is email
                        ];
                       
                        // $empty_value = [];
                        // if(empty($data['email']) || empty($data['name'])){
                        //     array_push($empty_value,$data);
                        // }
                        // print_r($empty_value);
                       // Basic validation
                        if (!empty($data['email']) && !empty($data['name'])) {
                                $existingUser = $userModel->where('email', $data['email'])->first();
                         
                                if ($existingUser) {
                                    $userModel->update($existingUser['id'], $data);
                                } 
                                else {
                                    $userModel->insert($data);
                                    $id = $this->user->insertID();
                                    $mongoData[$successCount] = [
                                        '_id' => $id,
                                        'name' => $data['name'],
                                        'email' => $data['email']
                                    ] ;
                                }
                                $successCount++;
                            
                        }else{
                            $errorCount++;
                            $emptyData[] = $data;
                            print_r($emptyData);


                        }

                     
                    }
                }
                //print_r($existingUser);
            //print_r($mongoData);
                fclose($handle);
                unlink($filepath); // Delete the temporary file
    
                $db->transComplete(); // Complete transaction
    
                if ($db->transStatus() === FALSE) {
                    return redirect()->to(base_url('/home'))
                        ->with('error', 'Transaction failed. Some records may not have been imported.');
                }
    
                $message = "Import completed. Successfully processed $successCount records.";
                if ($errorCount > 0) {
                    $message .= " Failed to process $errorCount records.";
                }
                

                $url = "http://localhost:5000/users/insertMany";


                $ch = curl_init();
                
                curl_setopt($ch, CURLOPT_URL, $url);    
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($mongoData));
                $response = curl_exec($ch);
                curl_close($ch);
                
               // if($emptyData){
                    // $filename = 'users_data' . date('Ymd') . '.csv';
                    // header("Content-Description: File Transfer");
                    // header("Content-Disposition: attachment; filename=$filename");
                    // header("Content-Type: application/csv; ");
            
                    // $userModel = new UserModel();
                    // $users = $userModel->findAll();
                  
                    // $fp = fopen('php://output', 'w');
                    // $csvData = [ 'Name', 'Email']; // Adjust according to your table structur
                    
                    // fputcsv($fp, $csvData);
            
                    // foreach ($users as $key => $value) {
                    //     fputcsv($fp, $value);
                    // }
                    // fclose($fp);
                    // exit();
                }
                
               return redirect()->to(base_url('/home'))->with('success', $message);
    
               



        } catch (\Exception $e) {
            log_message('error', 'CSV import error: ' . $e->getMessage());
            //return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
                   
    }

    public function logout(){
        $session = session();
        $session->destroy();
        return redirect()->to(base_url("/home"));
    }

}
