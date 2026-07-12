@extends('templates.dashboard')
@section('isi')
    <div class="row">
        <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">
                        <a href="{{ url('/status-pajak/tambah') }}" class="btn btn-primary">+ Tambah</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <form action="{{ url('/status-pajak') }}">
                        <div class="row mb-2">
                            <div class="col-10">
                                <input type="text" placeholder="Search...." class="form-control" value="{{ request('search') }}" name="search">
                            </div>
                            <div class="col">
                                <button type="submit" id="search"class="border-0 mt-3" style="background-color: transparent;"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="mytable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Status</th>
                                    <th>Nilai PTKP</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($status_pajak as $key => $pajak)
                                    <tr>
                                        <td>{{ ($status_pajak->currentpage() - 1) * $status_pajak->perpage() + $key + 1 }}.</td>
                                        <td>{{ $pajak->name }}</td>
                                        <td>Rp {{ number_format($pajak->ptkp) }}</td>
                                        <td>
                                            <ul class="action">
                                                <li class="edit">
                                                    <a href="{{ url('/status-pajak/edit/'.$pajak->id) }}"><i class="fa fa-solid fa-edit"></i></a>
                                                </li>
                                                <li class="delete">
                                                    <form action="{{ url('/status-pajak/delete/'.$pajak->id) }}" method="post" class="d-inline">
                                                        @method('delete')
                                                        @csrf
                                                        <button class="border-0" style="background-color: transparent;" onClick="return confirm('Are You Sure')"><i class="fa fa-solid fa-trash"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        {{ $status_pajak->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
@endsection
