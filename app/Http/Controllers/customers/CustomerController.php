<?php

namespace App\Http\Controllers\customers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\globalsets\GlobalSetModel;
use App\Models\addressList\provincesModel;

class CustomerController extends Controller
{
    //

    public function create()
    {

       return view('customers.create');
    }

  
}
