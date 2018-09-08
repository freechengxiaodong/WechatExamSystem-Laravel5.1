<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Student;

class IndexController extends Controller
{

    public function getIndex()
    {
        $students=Student::get();
        dd($students);
        return view('admin.index.index');
    }

}
