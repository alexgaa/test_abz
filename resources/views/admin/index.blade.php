@extends('template.main-template')
@section('title')
    Users
@endsection
@section('body')
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="#" class="nav-link px-2 text-secondary">Home</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">Features</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">Pricing</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
                    <li><a href="#" class="nav-link px-2 text-white">About</a></li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container-fluid pb-2">
        <div class="row g-2">
            <div class="col-3">
                <div id='menu-left' class="p-3 m-2 border bg-light rounded-3">
                    <div class="container">
                        <div class="row g-2  m-2">
                            <div class="col-6">
                                <h2><strong>Users</strong></h2>
                            </div>
                            <div class="col-6 text-end">
                                <a class="center-block" href="">
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="text-center p-2">
                        <a href="{{route('users.getAll')}}?page=1&count=6" class="btn btn-sm btn-warning btn-min-width border-secondary text-bold"
                           id="showAllUsers">
                            Show all users
                        </a>
                    </div>
                    <div class="text-center p-2">
                        <a href="" class="btn btn-sm btn-warning btn-min-width border-secondary text-bold"
                           data-bs-toggle="modal" data-bs-target="#addUserModal">
                            Registration user
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div id='center-content' class="p-3 m-2 bg-light border rounded-3 text-center">
                    <table  class="table table-hover">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Position</th>
                                <th>Photo</th>
                            </tr>
                        </thead>
                        <tbody id='showAllUsersTable'>
                        </tbody>
                    </table>
                    <a id="showMoreUsers" href="{{route('users.getAll')}}?page=1&count=6" class="btn btn-sm btn-warning btn-min-width border-secondary text-bold ">Show more >> </a>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal registration User-->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="sendForm" action="{{route('users.store')}}" method="post" class="form-group" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="text-center ms-4"><strong id="category_add_error" class="h3"></strong></div>
                    <div class="modal-body">
                        <input id="urlGetToken" type="hidden" name="getToken" value="{{route('token.get')}}">
                        <div class="mb-3">
                            <input name="name" id="name" type="text"
                                   class="form-control"
                                   placeholder="Name" value="name">
                        </div>
                        <div class="mb-3">
                            <input name="email" id="email" type="email"
                                   class="form-control"
                                   placeholder="Email" value ="alex@alex.com">
                        </div>
                        <div class="mb-3">
                            <input name="phone" id="phone" type="text"
                                   class="form-control"
                                   placeholder="+380666666666" value="+380666666661">
                        </div>
                        <div class="mb-3">
                            <select name="position_id" class="form-control" id="position_id">
                                @foreach($positions as $position)
                                    <option value="{{$position->id}}">{{$position->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input name="photo" id="photo" type="file"
                                   class="form-control"
                                   placeholder="image">
                        </div>
                        <div class="row m-1">
                            <div class="col text-start">
                                <span id="other_error" class=""><strong></strong></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-4" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"> Save </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal form result--}}
    <div id="resultModal" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-warning">
                <div class="modal-header">
                    <h4 class="modal-title ">Result</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body danger text-center display-5">
                    <p>User Added!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary min-width-button" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <footer class="py-1 my-0">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="/" class="nav-link px-2 text-muted">Home</a></li>
            </ul>
            <p class="text-center text-muted">© 2022 Gamov Aleksey</p>
        </footer>
    </div>
@endsection
