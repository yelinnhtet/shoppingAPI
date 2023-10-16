<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use Illuminate\Support\Facades\File;
//use Your Model

/**
 * Class UserRepo.
 */
class UserRepo
{
    /**
     * @return string
     *  Return the model
     */
    public function create($request)
    {
        $data = [];
        //Photo Upload
        $file = $request->file('photo');
        if($file)
        {

            // $file_name = uniqid().$file->getClientOriginalName();
            // $file->move(public_path('/images/user'),$file_name);

            $orgFileName = $file->getClientOriginalName();
            $subFileName = str_replace(" ","",$orgFileName);
            $file_name = uniqid().$subFileName;
            $file->move(public_path('/images/user'),$file_name);

        }
        else
        {
            $defaultPhotoFilename = 'defaultUserPhoto.jpg';
            $defaultPhotoPath = public_path('/images/' . $defaultPhotoFilename);
            $defaultPhotoName = uniqid() . $defaultPhotoFilename;
            $userFolder = public_path('/images/user');
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0755, true);
            }
            copy($defaultPhotoPath, $userFolder . '/' . $defaultPhotoName);

            $file_name = $defaultPhotoName;
        }
        $user = User::create([
            'name' => $request->username,
            'email' => $request->email,
            'photo' => $file_name ,
            'password' => Hash::make($request->password),
            'lat' => $request->lat,
            'long' => $request->long,
            'phone' => $request->phone,
            'agent_code' => $request->agent_code
        ]);
       return $user;
    }

    public function checkUser($request){
        $data = [];
        if(Auth::attempt(['email' => $request->email , 'password' => $request->password]))
        {
            $data['data']['user']= Auth::user();
            return $data;
        }
        $data['data']['error'] = 'Invalid Email and Password';
        return $data;
    }

    public function update($request, $id)
    {
        $user = User::find($id);
        // return $user;
        $user->name = $request->username;
        $user->email = $request->email;
        $user->lat = $request->lat;
        $user->long = $request->long;
        $user->phone = $request->phone;
        $user->role = $request->role;
        $user->agent_code = $request->agent_code;


        $file = $request->file('photo');
        if ($file) {
            unlink(public_path("images/user/".$user->photo));

            // $file_name = uniqid() . $file->getClientOriginalName();
            // $file->move(public_path('/images/user'), $file_name);
            $folderPath = public_path('/images/user/');
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name_parts = explode('.', $_FILES['photo']['name']);
            $file_ext = strtolower(end($file_name_parts));
            $fileName = uniqid() . '.'.$file_ext;
            $file = $folderPath . $fileName;
            move_uploaded_file($file_tmp, $file);
            $user->photo = $fileName;
            // $orgFileName = $file->getClientOriginalName();
            // $subFileName = str_replace(" ","",$orgFileName);
            // $file_name = uniqid().$subFileName;
            // $file->move(public_path('/images/user'),$file_name);
        }
        $user->save();

        return $user;
    }

    public function delete($id)
    {
        $data = User::where('id',$id)->first();
        $photo=$data->photo;
        if(File::exists(public_path().'/images/user/'.$photo))
        {
            File::delete(public_path().'/images/user/'.$photo);
        }

        $photo=$data->delete();
        return $photo;
    }

    public function find($id)
    {
        return User::findOrFail($id);
    }

}
