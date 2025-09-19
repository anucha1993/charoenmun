<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

class UserForm extends Component
{
    public $userId;
    public $state = [];
    public $editMode = false;
    public $resetPassword = false;

    public function mount($user = null)
    {
        if ($user) {
            $this->userId = $user;
            $this->editMode = true;
            $user = User::findOrFail($user);
            $this->state = [
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type
            ];
        }
    }

    #[Title('เพิ่ม/แก้ไขผู้ใช้งาน')]
    public function render()
    {
        return view('livewire.users.user-form');
    }

    public function save()
    {
        $rules = [
            'state.name' => 'required',
            'state.email' => 'required|email|unique:users,email,' . $this->userId,
            'state.type' => 'required|in:admin,member',
        ];

        if (!$this->editMode || ($this->editMode && $this->resetPassword)) {
            $rules['state.password'] = 'required|min:8';
            $rules['state.password_confirmation'] = 'required|same:state.password';
        }

        $this->validate($rules);

        if ($this->editMode) {
            $user = User::findOrFail($this->userId);
            $data = [
                'name' => $this->state['name'],
                'email' => $this->state['email'],
                'type' => $this->state['type'],
            ];

            if ($this->resetPassword) {
                $data['password'] = bcrypt($this->state['password']);
            }
            
            $user->update($data);

            session()->flash('success', 'อัพเดทข้อมูลเรียบร้อย');
        } else {
            User::create([
                'name' => $this->state['name'],
                'email' => $this->state['email'],
                'type' => $this->state['type'],
                'password' => bcrypt($this->state['password']),
            ]);

            session()->flash('success', 'เพิ่มผู้ใช้งานเรียบร้อย');
        }

        return redirect()->route('users.index');
    }
}
