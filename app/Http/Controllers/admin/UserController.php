<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
//use App\Mail\UserPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Password;


class UserController extends Controller
{

    function __construct()
    {
         // $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         // $this->middleware('permission:user-create', ['only' => ['create','store']]);
         // $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         // $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        $data = User::latest()->paginate(10);

        return view('admin.users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(): View
    {
        $roles = Role::where('name', '<>', 'superadmin')->pluck('name', 'name')->all();
        return view('admin.users.create',compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): RedirectResponse
    {

        $this->validate($request, [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required',
        ]);

       try{

            $user=new User();
            $user->name=$request->input('fname');
            $user->f_name=$request->input('fname');
            $user->l_name=$request->input('lname');
            $user->email=$request->input('email');
            $token=Str::random(64);
            $user->password = Hash::make($token);
            $user->save();
            $user->assignRole($request->input('roles'));

            //Mail::to($user->email)->send(new UserPasswordMail($user,$token));
            
            return redirect()->route('admin.users.index')
                            ->with('status','User created successfully');
        } catch (Exception $e) {           
            Log::error($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): View
    {
        $user = User::find($id);
        return view('admin.users.show',compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id): View
    {
        $usStates = array(
            'AL' => 'Alabama',
            'AK' => 'Alaska',
            'AZ' => 'Arizona',
            'AR' => 'Arkansas',
            'CA' => 'California',
            'CO' => 'Colorado',
            'CT' => 'Connecticut',
            'DE' => 'Delaware',
            'FL' => 'Florida',
            'GA' => 'Georgia',
            'HI' => 'Hawaii',
            'ID' => 'Idaho',
            'IL' => 'Illinois',
            'IN' => 'Indiana',
            'IA' => 'Iowa',
            'KS' => 'Kansas',
            'KY' => 'Kentucky',
            'LA' => 'Louisiana',
            'ME' => 'Maine',
            'MD' => 'Maryland',
            'MA' => 'Massachusetts',
            'MI' => 'Michigan',
            'MN' => 'Minnesota',
            'MS' => 'Mississippi',
            'MO' => 'Missouri',
            'MT' => 'Montana',
            'NE' => 'Nebraska',
            'NV' => 'Nevada',
            'NH' => 'New Hampshire',
            'NJ' => 'New Jersey',
            'NM' => 'New Mexico',
            'NY' => 'New York',
            'NC' => 'North Carolina',
            'ND' => 'North Dakota',
            'OH' => 'Ohio',
            'OK' => 'Oklahoma',
            'OR' => 'Oregon',
            'PA' => 'Pennsylvania',
            'RI' => 'Rhode Island',
            'SC' => 'South Carolina',
            'SD' => 'South Dakota',
            'TN' => 'Tennessee',
            'TX' => 'Texas',
            'UT' => 'Utah',
            'VT' => 'Vermont',
            'VA' => 'Virginia',
            'WA' => 'Washington',
            'WV' => 'West Virginia',
            'WI' => 'Wisconsin',
            'WY' => 'Wyoming'
        );
        $user = User::find($id);
        $roles = Role::where('name', '<>', 'superadmin')->pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name','name')->all();

        return view('admin.users.edit',compact('user','roles','userRole', 'usStates'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'roles' => 'required'
        ]);


        $user = User::find($id);
        $address = $user->address;
        $user->update([
            'name' =>$request->input('fname'),
            'f_name' =>$request->input('fname'),
            'l_name' =>$request->input('lname'),
            'email' =>$request->input('email'),
        ]);
        
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        
        $user->assignRole($request->input('roles'));

        return redirect()->route('admin.users.index')
                        ->with('status','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();
        return redirect()->route('admin.users.index')
                        ->with('status','User deleted successfully');
    }

    public function showPasswordCreateForm($token,$email)
    {
        return view('auth.passwords.newCreate', ['token' => $token, 'email'=>$email]);
    }

    public function passwordCreation(Request $request)
    {
        $tokenData = UserRegistrationToken::where('token', $request->input('token'))
        ->where('email', $request->input('email'))
        ->whereNull('expires_at')
        ->first();
        if(!$tokenData){
            return redirect()->back()->with('error', 'Invalid or mismatched token');
        }
        $user=User::where('email',$request->input('email'))->first();
        if(!$user){
            return redirect()->back()->with('error', 'user not found');
        }
        $user->password = Hash::make($request->input('password'));
        $user->save();
        $tokenData->update(['expires_at'=>'1']);
        return redirect()->route('login')->with('status', 'Password Created Successfully');

    }
}
