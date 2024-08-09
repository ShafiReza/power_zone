<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegularCustomer;
use App\Models\IrregularCustomer;
use App\Models\MonthlyBill;
use App\Models\Bill;
use Auth;
use Validator;
class AdminController extends Controller
{
    public function dashboard()
    {
        // Calculate total number of regular customers
        $totalRegularCustomers = RegularCustomer::count();
        $totalIrregularCustomers = IrregularCustomer::count();
        $totalDueAmount = MonthlyBill::where('status', 'Due')->sum('amount');
        $totalPaidAmount = MonthlyBill::where('status', 'Paid')->sum('amount');
        $totalPaid = Bill::where('status', 'paid')->sum('final_amount');
        $totalDue = Bill::where('status', 'pending')->sum('final_amount');

        // Pass the count to the view
        return view("admin.dashboard", compact('totalRegularCustomers','totalIrregularCustomers','totalDueAmount','totalPaidAmount','totalPaid','totalDue'));
    }
    public function login(Request $request ){
        if($request->isMethod('post')){
            $data= $request->all();
            $rules = [
                'email'=> 'required|email|max:255',
                'password'=> 'required|max:30'
            ];

            $customMessages = [
                'email.required'=> 'Email is required',
                'email.email' => 'Valid email is required',
                'password.required' => 'Password is required',


            ];

            $this->validate($request,$rules,$customMessages);
            if(Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']])){
                    return  redirect('admin/dashboard');
            }else{
                return redirect()->back()->with('error_message',"Invalid Email or Password");
            }
        }
        return view("admin.login");

    }

    public function  logout(){
        Auth::guard('admin')->logout();
        return redirect('admin/login');
    }
}
