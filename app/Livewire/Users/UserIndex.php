<?php

namespace App\Livewire\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;

class UserIndex extends Component
{
    use WithPagination;

    public $search = '';
    
    #[Title('จัดการผู้ใช้งาน')]
    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.users.user-index', [
            'users' => $users
        ]);
    }

    public function delete($userId)
    {
        // ป้องกันการลบตัวเอง
        if ($userId === auth()->id()) {
            $this->dispatch('notify', [
                'message' => 'ไม่สามารถลบบัญชีตัวเองได้',
                'type' => 'error'
            ]);
            return;
        }

        User::findOrFail($userId)->delete();
        $this->dispatch('notify', [
            'message' => 'ลบผู้ใช้งานเรียบร้อย',
            'type' => 'success'
        ]);
    }
}
