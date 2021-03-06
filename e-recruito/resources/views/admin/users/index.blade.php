<!DOCTYPE html>
<html>
<head>
	@include('head')
</head>
<body>
	@include ('nav')
	<div class="containter-fluid">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<div class="panel panel-default">
					<div class="panel-heading">Data Users</div>
					<div class="panel-body">
						@if(session()->get('isMessage') == 1)
						<div class="alert alert-success">
							Update profile success
						</div>
						@endif
						<table class="table table-hover table-striped">
							<tr><th>ID</th><th>Name</th><th>Email</th><th>Details</th><th>Edit</th><th>Delete</th></tr>
							@foreach($users as $user)
							<tr>
								{!! Form::open(['class'=>'form-inline', 'method'=>'DELETE', 'onsubmit' => 'return confirm("Are you sure?")', 'route'=>['user.destroy', $user->id]]) !!}
								<td>{{ $user->id }}</td>
								<td>{{ $user->name }}</td>
								<!-- <td><a href="{{ route('user.show', $user->id) }}">{{ $user->name }}</a></td> -->
								<td>{{ $user->email }}</td>
								<td>{!! link_to_route('user.show', 'Details', [$user->id], ['class'=> 'btn btn-info']) !!}</td>
								<td>{!! link_to_route('user.edit', 'Edit', [$user->id], ['class'=>'btn btn-primary']) !!}</td>
								<td>{!! Form::submit('Delete', ['class'=>'btn btn-danger']) !!}
									{!! Form::close() !!}
								</tr>
								@endforeach
							</table>
							<p> {!! link_to_route('user.create', 'Tambah User baru') !!}</p>
							{!! $users->setPath('user') !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		@include('footer')

	</body>
	</html>