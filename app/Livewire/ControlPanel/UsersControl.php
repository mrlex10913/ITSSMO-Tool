<?php

namespace App\Livewire\ControlPanel;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UsersControl extends Component
{
    public $id_number;
    public $name;
    public $email;
    public $department;
    public $role;
    public $password;
    public $temporaryPassword;
    public $updateAccessID;
    public $openSpecificUserModal = false;

    public $NewUserAccessModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'department' => 'required|string|max:255',
        'role' => 'required|string|max:255',
        'temporaryPassword' => 'required|string',
        'id_number' => 'required|string'
    ];

    public function openSpecificUserAccessModal($userId){
        $this->openSpecificUserModal = true;
        $userFind = User::find($userId);
        $this->updateAccessID = $userId;
        $this->email = $userFind->email;
        $this->name = $userFind->name;
        if($userFind->temporary_password == NULL){
            $this->temporaryPassword = 'Password Already Change';
        }else{
            $this->temporaryPassword = $userFind->temporary_password;
        }
        $this->department = $userFind->department;
        $this->id_number = $userFind->id_number;
        $this->role = $userFind->role;

    }
    public function updateSpecificUserAccessModal(){
        try{

            $updateAccess = User::find($this->updateAccessID);
            $updateAccess->password = $this->temporaryPassword;
            $updateAccess->temporary_password = $this->temporaryPassword;
            $updateAccess->is_temporary_password_used = false;
            $updateAccess->save();

            flash()->success('Account has been reset');
            $this->openSpecificUserModal = false;
        }catch(ValidationException $e){
            flash()->error('Oops! Something went wrong!');
            throw $e;
        }
    }
    public function generateTemporaryPassword(){

        $this->temporaryPassword = strtoupper(Str::random(10));
    }

    // private function generateRandomString($length){

    //     $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //     $charactersLength = strlen($characters);
    //     $randomString = '';
    //     for ($i = 0; $i < $length; $i++) {
    //         $randomString .= $characters[rand(0, $charactersLength - 1)];
    //     }
    //     return $randomString;
    // }

    public function closeSpecificUserAccessModal(){
        $this->openSpecificUserModal = false;
    }

    public function OpenNewUserAccessModal(){
        $this->temporaryPassword = strtoupper(Str::random(10));
        $this->NewUserAccessModal = true;
        $this->resetForm();
    }

    public function closeNewUserAccessModal(){
        $this->NewUserAccessModal = false;
    }

    public function saveUser(){
        try{
            $this->validate();

            User::create([
                'id_number' => $this->id_number,
                'name' => $this->name,
                'email' => $this->email,
                'department' => $this->department,
                'role' => $this->role,
                'password' => Hash::make($this->temporaryPassword),
                'temporary_password' => $this->temporaryPassword,
                'is_temporary_password_used' => false,
            ]);

            $this->reset(['name','email','department','role', 'temporaryPassword', 'id_number']);
            $this->closeNewUserAccessModal();
            flash()->success('New User has given access');
        }catch(ValidationException $e){
            flash()->error('Please fill in all field');
            throw $e;
        }

    }

    public function breadCrumbUsersPanel(){
        session(['breadcrumb' => ' User Controller']);

    }
    public function resetForm(){
        $this->reset([
            'id_number',
            'name',
            'email',
            'department',
            'role',
            'updateAccessID',
            'temporaryPassword'
        ]);
    }
    public function render()
    {
        $userAccess = User::all();
        $this->breadCrumbUsersPanel();
        return view('livewire.control-panel.users-control', compact('userAccess'));
    }
}
