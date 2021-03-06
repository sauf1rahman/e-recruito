<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Users;
use Input, Redirect, File, Session,Validator;
use Illuminate\Http\Request;

class UserController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(session()->get('isLogin')){
			$user = session()->get('isLogin');	
			if ($user->role == 0) {
				return redirect('/pengguna');
			} else {
				$users = Users::where('role','=',0)->paginate(10);
				$title = 'List Users';
				return view('admin.users.index', compact('users','title'));
			}
		}else{
			$title='E-recruito Login';
			return Redirect::to('/login')->with('title',$title);
		}
	}

	public function register(){

		$input = Input::all();
		$rules = array(
			'name' =>'required', 
			'email' =>'required|unique:users,email', 
			'password' =>'required|min:8', 
			'repassword' =>'required|same:password'
			);


		$validator = Validator::make($input,$rules);
		if($validator->fails()){
			return Redirect::to('/signup')->withInput()->withErrors($validator->errors());
		}else{
			$user = Users::create([
				'name'=>$input['name'],
				'email'=>$input['email'],
				'password'=>md5($input['password']),
				'foto'=>'default.gif',
				'role'=>0
				]);

			return Redirect::to('/login')->with('message','1');
		}
	}

	public function create()
	{	

		if (session()->get('isLogin')) {
			$user = session()->get('isLogin');
			if ($user->role == 0) {
				return redirect('/pengguna');
			} else {
				$title = 'Tambah User';
				return view('admin.users.create', ['title'=>$title]);
			}
		} else {

			return Redirect::to('/login');
		}		
	}

	/**
	 * Authentication 
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function authentication()
	{

		$input = Input::all();
		$rules = array(
			'email' =>'required|email', 
			'password' =>'required'
			);

		$validator = Validator::make($input,$rules);
		if($validator->fails()){
			return Redirect::to('/login')->withInput()->withErrors($validator->errors());
		}else{
			$email = $input['email'];
			$user = Users::where('email',$email)->first();
			if($user){
				if($user['role'] == 0 && $user['password'] == md5($input['password'])){
					session()->put('isLogin', $user);
					return Redirect::to('/pengguna')->withInput();
				}else if($user['password'] == md5($input['password'])){
					session()->put('isLogin', $user);
					return Redirect::to('/admin')->withInput();
				}else{
					return Redirect::to('/login')->withInput()->with('error',1);
				}
			}else{
				return Redirect::to('/login')->withInput()->with('error',2);
			}

		}
	}

	public function logout(){
		Session::forget('isLogin');
		return Redirect::to('/')->withInput();
	}



		/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
		public function show($id)
		{
			if(session()->get('isLogin')){
				$user = session()->get('isLogin');	
				if ($user->role == 0) {
					return redirect('/pengguna');
				} else {
					$specificUser = Users::find($id);
					if (!$specificUser) {
						return Redirect::route('user.index');
					}
					if ($specificUser->role == 1) {
						return Redirect::route('user.index');
					}
					$title = 'Details of '.$specificUser->name;
					return view('admin.users.show', compact('specificUser','title'));
				}
			}else{
				$title='E-recruito Login';
				return Redirect::to('/login')->with('title',$title);
			}
		}

		public function viewUser($id)
		{
			$user = Users::where('id',$id)->first();
			$title = 'User  '.$user->name;
			return view('user.view', compact('user','title'));
			
		}



		/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
		public function editProfile()
		{
			if(session()->get('isLogin')){
				$user1 = session()->get('isLogin');
				$Input = Input::all();
				$rules = array(
					'name'=>'required',
					'email'=>'required|email',
					'region'=>'required',
					'motto'=>'required',
					'profesi'=>'required',
					'foto'=>'mimes:jpeg,jpg,bmp,png',	
					);
				$validator = Validator::make($Input,$rules);
				if($validator->fails()){
					return Redirect::to('/pengguna/my-profile/'.$user1['id'])->withInput()->withErrors($validator->errors());
				}else{
					
					$user = Users::find($user1['id']);
					$user->name = $Input['name'];
					$user->email = $Input['email'];
					$user->motto = $Input['motto'];
					if($Input['birthdate']){
						$user->birthdate = $Input['birthdate'];
					}
					$user->profesi = $Input['profesi'];
					$user->region = $Input['region'];
					$image = Input::file('foto');
					if(isset($Input['foto'])){
						if($user->foto != 'default.gif'){
							File::delete('public/assets/img/avatar/'.$user->foto);
						}	
						$filename  = rand(1111,9999).time() . '.' . $image->getClientOriginalExtension();
						Input::file('foto')->move('public/assets/img/avatar/',$filename);
						$user ->foto = $filename;
					}

					$user->save(); 
					Session::put('isLogin', $user);
					$loginuser = session()->get('isLogin');
					if($loginuser['id'] == 0){
						return Redirect::to('/pengguna/my-profile')->withInput()->with('isMessage',1);
					}else{
						return Redirect::to('/user')->withInput()->with('isMessage',1);
					}
					
				}
			}else{
				return Redirect::to('/')->withInput();
			}
		}

		/**
		 * Show the form for editing the specified resource.
		 *
		 * @param  int  $id
		 * @return Response
		 */
		public function edit($id) {
			if(session()->get('isLogin')){
				$pengguna = session()->get('isLogin');	
				if ($pengguna->role == 0) {
					return redirect('/pengguna');
				} else {
					$user = Users::find($id);
					if (!$user) {
						return Redirect::route('user.index');
					}
					if ($user->role == 1) {
						return Redirect::route('user.index');
					}
					
					$title = 'Edit data User';
					return view('admin.users.edit', compact('user', 'title'));
				}
			}else{
				$title='E-recruito Login';
				return Redirect::to('/login')->with('title',$title);
			}
		}

		/**
		 * Store a newly created resource in storage.
		 *
		 * @return Response
		 */
		public function store(Request $request)
		{
			if(session()->get('isLogin')){
				$pengguna = session()->get('isLogin');	
				if ($pengguna->role == 0) {
					return redirect('/pengguna');
				} else {
					$rules = array(
						'name' =>'required', 						
						'email' =>'required|email|unique:users,email', 
						'password' =>'required|min:8',
						);
					$this->validate($request, $rules);
					$input = Input::all();
					$input['password'] = md5($input['password']);
					$input['foto'] = 'default.gif';
					Users::create( $input );
					
					return Redirect::route('user.index');
				}
			}else{
				$title='E-recruito Login';
				return Redirect::to('/login')->with('title',$title);
			}		
		}

		/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
		public function update($id)
		{
			if(session()->get('isLogin')){
				$pengguna = session()->get('isLogin');	
				if ($pengguna->role == 0) {
					return redirect('/pengguna');
				} else {
					$user = Users::find($id);
					if (!$user) {
						return Redirect::route('user.index');
					}
					if ($user->role == 1) {
						return Redirect::route('user.index');
					}

					$input = array_except(Input::all(), '_method');
					if ($input['password'] == "") {
						$input['password'] = $user->password;
					} else {
						$input['password'] = md5($input['password']);
					}

					if (!$input['birthdate']) {
						$input['birthdate'] = $user->birthdate;
					}

					$rules = array(
						'name'=>'required',
						'email'=>'required|email',
						'password'=>'required',
						'region'=>'required',
						'motto'=>'required',
						'profesi'=>'required',
						'foto'=>'mimes:jpeg,jpg,bmp,png',
						'birthdate'=>'required'	
						);
					$validator = Validator::make($input,$rules);
					if($validator->fails()){
						$title = 'Edit data User';
						return view('admin.users.edit', compact('user', 'title'))->withErrors($validator->errors());
						//return Redirect::to('/pengguna/my-profile/'.$user1['id'])->withInput()->withErrors($validator->errors());
					} else {
						$image = Input::file('foto');
						if(isset($input['foto'])){
							if($user->foto != 'default.gif'){
								File::delete('public/assets/img/avatar/'.$user->foto);
							}	
							$filename  = rand(1111,9999).time() . '.' . $image->getClientOriginalExtension();
							Input::file('foto')->move('public/assets/img/avatar/',$filename);
							$input['foto'] = $filename;
						}
						$user->update($input);
						return Redirect::route('user.index');
					}
				}
			}else{
				$title='E-recruito Login';
				return Redirect::to('/login')->with('title',$title);
			}
		}

		/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
		public function destroy($id)
		{
			if (session()->get('isLogin')) {
				$user = session()->get('isLogin');
				if ($user->id == 0) {
					return redirect('/pengguna');
				} else {
					$user = Users::find($id);
					if (!$user) {
						return Redirect::route('user.index');
					}
					if ($user->role == 1) {
						return Redirect::route('user.index');
					}

					$user->delete();
					return Redirect::route('user.index');
				}
			} else {
				return Redirect::to('/login')->with('title',$title);;
			}
		}

	}
