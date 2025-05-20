<?php

namespace App\Livewire;

use Livewire\Component;

class Dashboards extends Component
{
  public function render()
{
    return view('livewire.dashboards')        // ← ตรวจตรงนี้
        ->layout('layouts.horizontal');
}
}
