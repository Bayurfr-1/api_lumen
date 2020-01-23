<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use App\User;

class UserController extends Controller
{
    public function auth(Request $request){
        $username = $request->username;
        $password = $request->password;

        $user = User::where('username', $username)->first();
        if($user){
            if(Crypt::decrypt($user->password) == $password){
                $user = $user->first();
                $token = Str::random(40);
                $user->token = $token;
                $user->save();

                return response([
                    "logged"    => true,
                    "token"     => $token,
                    "is_admin"  => $user->is_admin,
                ]);
            } else {
                return response([
                    "logged"    => false
                ]);
            }
        } else {
            return response([
                "logged"    => false
            ]);
        }
    }

    public function checkLogin(Request $request){
        $token = $request->token;
        $check = User::where("token", $token)->first();
        if($check != NULL){
            return response([
                "auth"      => true,
                "user"    => $check
            ]);
        } else {
            return response([
                "auth"      => false
            ]);
        }
    }

    public function register(Request $request){
        try {
            $action = $request->action;
            if($action == "insert"){
                $user = new User();
                $user->name = $request->name;
                $user->username = $request->username;
                $user->name = $request->name;
                $user->password = Crypt::encrypt($request->password);
                $user->is_admin = $request->is_admin;
                $user->save();

                return response([
                    "status"       => 1,
                    "message"      => "Data berhasil ditambahkan."
                ]);

            }
            else if ($action == "update"){
                $user = User::where("id", $request->id)->first();
                $user->name = $request->name;
                $user->username = $request->username;
                $user->name = $request->name;
                $user->password = Crypt::encrypt($request->password);
                $user->is_admin = $request->is_admin;
                $user->save();

                return response([
                    "status"       => 1,
                    "message"      => "Data berhasil diubah."
                ]);
            }
        } catch (\Exception $e){
            return response([
                "status"       => 0,
                "message"      => $e->getMessage()
            ]);
        }
    }

    public function getAll($limit = 10, $offset = 0){
        $data["count"] = User::count();
        $user = array();

        foreach (User::take($limit)->skip($offset)->get() as $p) {
            $item = [
                "id"          => $p->id,
                "name"        => $p->name,
                "username"    => $p->username,
                "password"    => $p->password,
                "is_admin"    => $p->is_admin,
                "created_at"  => $p->created_at,
                "updated_at"  => $p->updated_at
            ];

            array_push($user, $item);
        }
        $data["user"] = $user;
        return response($data);
    }

    public function find(Request $request, $limit = 10, $offset = 0)
    {
        $find = $request->find;
        $user = User::where("id","like","%$find%")
        ->orWhere("name","like","%$find%")
        ->orWhere("username","like","%$find%");
        $data["count"] = $user->count();
        $users = array();
        foreach ($user->skip($offset)->take($limit)->get() as $p) {
          $item = [
            "id" => $p->id,
            "name" => $p->name,
            "username" => $p->username,
            "password" => $p->password,
            "is_admin" => $p->is_admin,
            "created_at" => $p->created_at,
            "updated_at" => $p->updated_at
          ];
          array_push($users,$item);
        }
        $data["user"] = $users;
        return response($data);
    }

    public function delete($id)
    {
        try{

            User::where("id", $id)->delete();

            return response([
                "message"   => "Data berhasil dihapus."
            ]);
        } catch(\Exception $e){
            return response([
                "message"   => $e->getMessage()
            ]);
        }
    }

    public function decrypt(Request $request)
    {
        return response([
          "password" =>Crypt::decrypt($request->password)
        ]);
    }
}
