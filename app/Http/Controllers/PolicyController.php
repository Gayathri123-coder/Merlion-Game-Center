<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolicyController extends Controller
{
     public function contact()
    {
       
        return view('contact');
    }
     public function privacy()
    {
       
        return view('privacy');
    }
     public function refund()
    {
       
        return view('refund');
    }
     public function terms_condition()
    {
       
        return view('terms_condition');
    }
}