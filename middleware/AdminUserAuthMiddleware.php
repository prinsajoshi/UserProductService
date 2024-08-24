<?php
class Middleware{
    
    public static function validateRequest($data)
    {
        try{
            if (empty($data['username']) || empty($data['password'])){
                throw new Exception("Username and password are required");
            }

            return[
                'username'=> $data['username'],
                'password'=> $data['password'],
                'role'=> ($data['username'] === 'admin' && $data['password']==='admin') ? 'admin' : 'user'
            ];
        }
        catch(Exception $e){
            echo json_encode(['error'=> $e->getMessage()]);
            exit;
        }
    }
}


?>