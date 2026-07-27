@extends('templates.dashboard')
@section('isi')
    <div class="email-wrap">
        <div class="row">
            <div class="col-md-12 project-list">
            <div class="card">
                <div class="row">
                    <div class="col-md-6 mt-2 p-0 d-flex">
                        <h4>{{ $title }}</h4>
                    </div>
                    <div class="col-md-6 p-0">    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
          <div class="email-right-aside">
            <div class="card email-body">
              <div class="email-profile">
                <div>
                  <div class="pe-0 b-r-light"></div>
                  <div class="email-top">
                    <div class="row">
                      <div class="col-12">
                        <div class="d-flex align-items-center">
                          <div class="flex-grow-1 d-flex flex-wrap align-items-center gap-2">                                                                       
                            <div class="dropdown mr-2">
                              <button class="btn btn-primary dropdown-toggle" id="dropdownMenuButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter</button>
                              <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="{{ url('/notifications') }}">All</a>
                                <a class="dropdown-item" href="{{ url('/notifications/read') }}">Read</a>
                                <a class="dropdown-item" href="{{ url('/notifications/unread') }}">Unread</a>
                              </div>
                            </div>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ url('/notifications/mark-all-read') }}" class="btn btn-warning text-dark ml-2 d-inline-flex align-items-center" style="font-weight: 600; border-radius: 6px;">
                              <i class="fa fa-bell-slash-o mr-2"></i> ✔️ Tandai Semua Sudah Dibaca & Matikan Suara Alarm
                            </a>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="inbox">
                    @foreach ($inboxs as $inbox)
                        @php
                            $user = isset($inbox->data['user_id']) ? App\Models\User::find($inbox->data['user_id']) : null;
                        @endphp
                        <a href="{!! !$inbox->read_at ? url('/notifications/read-message/'.$inbox->id) : url($inbox->data['action'] ?? '/'); !!}" class="d-flex" style="{{ !$inbox->read_at ? 'background-color: rgb(241, 241, 241)' : '' }}">
                            <div class="d-flex-size-email">                                       
                                <label class="d-block mb-0">
                                @if ($user && $user->foto_karyawan)
                                    <img class="me-3 rounded-circle" src="{{ url('/storage/'.$user->foto_karyawan) }}" alt="">
                                @else
                                    <img class="me-3 rounded-circle" src="{{ url('assets/img/foto_default.jpg') }}" alt="image">
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6>{{ $user ? $user->name : 'Sistem' }} </h6>
                                <p>{{ $inbox->data['message'] ?? '' }}</p><span>{{ date('d M Y H:i:s',strtotime($inbox->created_at)) }}</span>
                            </div>
                        </a>
                    @endforeach
                    <div class="d-flex justify-content-end me-4 mt-4">
                        {{ $inboxs->links() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    

@endsection
