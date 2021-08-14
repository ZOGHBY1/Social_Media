<?php
namespace app\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Comments;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Posts;
use App\Models\Reports;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class employeeController extends Controller{



    function Users(){

        $Reports=Reports::all();

        $data=[];
        foreach($Reports as $rep){
        $user=User::where('id',$rep->userid)->get();
            foreach($user as $u){
                array_push($data,$u);
            }

        }
        return view('Users',compact('data'));

    }


    function UserDetails($id){
        $data=User::where('id',$id)->get();
        $Reports=Reports::where('userid',$id)->get();
        $count=0;
        foreach($Reports as $rep){
            $count++;
        }

        return view('UserDetails',compact('data','Reports','count'));
    }



    function BandedUsers(){
        $data=User::where('ban','1')->paginate(1000);
        return view('BandedUsers',compact('data'));

    }


    function NewReports(){
        $data=Reports::orderBy('created_at','DESC')->paginate(1000);
        return view('Reports',compact('data'));

    }


    function SeeProfile($id){
        $data=User::where('id',$id)->get();

         $userposts=[];
         $poststatus=true;
         foreach($data as $ufi){
            if(count($ufi->posts)>0){
                foreach($ufi->posts as $post){
                    array_push($userposts,$post);
                }
            }else{
                $poststatus=false;
            }
            }

        return view('Profile',compact('data','userposts','poststatus'));
    }

    function SeePost($postid){
        $data=Posts::where('postid',$postid)->get();
        return view('Post',compact('data'));
    }

    function SeeComment($commentid){
        $data=Comments::where('commentid',$commentid)->get();
        return view('Comment',compact('data'));
    }

    function BanUser($id){
        $udata=User::find($id);
       if($udata->ban=="0"){
        $udata->ban='1';
        $udata->banDurationByDays='1';
        $udata->banReason='reported many times';
        $udata->save();
       }
       $data=User::where('ban','1')->paginate(1000);
       return view('BandedUsers',compact('data'));
    }

    function unBanUser($id){
        $udata=User::find($id);
        if($udata->ban=="1"){
         $udata->ban='0';
         $udata->banDurationByDays='0';
         $udata->banReason=date('dmy');
         $udata->save();
        }
        $data=Reports::orderBy('created_at','DESC')->paginate(1000);
        return view('Reports',compact('data'));
    }




    function Employees(){
        $data=Employee::orderBy('created_at','DESC')->paginate(1000);
        return view('Employees',compact('data'));

    }

    function EmpLogIn(){
        return view("EmpLogInPage");
    }

    function EPage(){

        return view('customersRep');
    }


    function EmpInfo(){

        return view('addEmployee');
    }

    function addEmployee(Request $req){
        $add=Employee::create([
            'firstname' => $req->fname,
            'lastname' => $req->lname,
            'email' => $req->email,
            'password' => Hash::make($req->password),
            'gender' => $req->gender,
            'phonenumber' => $req->phonenumber,
            'region' => 'Turkey'
        ]);
        if($add){
            $data=Employee::orderBy('created_at','DESC')->paginate(1000);
            return view('Employees',compact('data'));

        }
    }

    function deleteEmployee($id){
        $delEmp=Employee::where('id',$id)->delete();

    if($delEmp){
        $data=Employee::orderBy('created_at','DESC')->paginate(1000);
        return view('Employees',compact('data'));
    }
    }

    function editEmp($id){
        $data=Employee::where('id',$id)->get();
        return view('editEmpInfo',compact('data'));
    }

    function editEmpInfo($id,Request $req){
        $emp=Employee::find($id);
        $emp->firstname=$req->fname;
        $emp->lastname=$req->lname;
        $emp->email=$req->email;
        $emp->gender=$req->gender;
        $emp->phonenumber=$req->phonenumber;
        $emp->save();
        $data=Employee::orderBy('created_at','DESC')->paginate(1000);
        return view('Employees',compact('data'));
    }

    function ResetPass($id){
        $data=Employee::where('id',$id)->get();
        return view('ResetPass',compact('data'));
    }
    function ResetEmpPass($id,Request $req){
        $emp=Employee::find($id);
        $emp->password=Hash::make($req->password);
        $emp->save();
        $data=Employee::orderBy('created_at','DESC')->paginate(1000);
        return view('Employees',compact('data'));
    }



}


