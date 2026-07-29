<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $filter = request()->input('filter','');
        $title = 'Notifications';

        $inboxs = auth()->user()->notifications()
                        ->when($filter == 'read', fn($query)=>$query->whereNotNull('read_at'))
                        ->when($filter == 'unread', fn($query)=>$query->whereNull('read_at'))
                        ->paginate(10)
                        ->withQueryString();

        if (auth()->user()->is_admin == 'admin') {
            return view('notifications.indexAdmin', compact(
                'inboxs',
                'title'
            ));
        } else {
            return view('notifications.index', compact(
                'inboxs',
                'title'
            ));
        }
    }

    public function read()
    {
        $filter = request()->input('filter','');
        $title = 'Notifications';

        $inboxs = auth()->user()->notifications()
                        ->whereNotNull('read_at')
                        ->paginate(10)
                        ->withQueryString();

        return view('notifications.indexAdmin', compact(
            'inboxs',
            'title'
        ));
    }

    public function unread()
    {
        $filter = request()->input('filter','');
        $title = 'Notifications';

        $inboxs = auth()->user()->notifications()
                        ->whereNull('read_at')
                        ->paginate(10)
                        ->withQueryString();

        return view('notifications.indexAdmin', compact(
            'inboxs',
            'title'
        ));
    }

    public function readMessage($id)
    {
        $notifikasi = auth()->user()->notifications()->where('id', $id)->whereNull('read_at')->first();
        if ($notifikasi) {
            $action = $notifikasi->data["action"] ?? '/notifications';
            
            // Periksa jika action adalah URL cuti
            if (str_contains($action, 'data-cuti?user_id=')) {
                $url_components = parse_url($action);
                if (isset($url_components['query'])) {
                    parse_str($url_components['query'], $params);
                    $user_id = $params['user_id'] ?? null;
                    $mulai = $params['mulai'] ?? null;
                    $akhir = $params['akhir'] ?? null;
                    
                    if ($user_id && $mulai && $akhir) {
                        $exists = \App\Models\Cuti::where('user_id', $user_id)
                                    ->whereBetween('tanggal', [$mulai, $akhir])
                                    ->exists();
                        if (!$exists) {
                            $notifikasi->delete();
                            return redirect()->back()->with('error', 'Data pengajuan cuti tersebut sudah tidak ada / telah dihapus.');
                        }
                    }
                }
            } else if (str_contains($action, '/cuti?mulai=')) {
                $url_components = parse_url($action);
                if (isset($url_components['query'])) {
                    parse_str($url_components['query'], $params);
                    $mulai = $params['mulai'] ?? null;
                    $akhir = $params['akhir'] ?? null;
                    
                    if ($mulai && $akhir) {
                        $exists = \App\Models\Cuti::where('user_id', auth()->user()->id)
                                    ->whereBetween('tanggal', [$mulai, $akhir])
                                    ->exists();
                        if (!$exists) {
                            $notifikasi->delete();
                            return redirect()->back()->with('error', 'Data pengajuan cuti Anda sudah tidak ada / telah dihapus.');
                        }
                    }
                }
            }

            $notifikasi->markAsRead();
            return redirect($action);
        }
        
        // Jika notifikasi sudah dibaca, cek apakah itu notifikasi cuti yang bodong
        $readNotif = auth()->user()->notifications()->where('id', $id)->first();
        if ($readNotif) {
             $action = $readNotif->data["action"] ?? '/notifications';
             
             if (str_contains($action, 'data-cuti?user_id=')) {
                $url_components = parse_url($action);
                if (isset($url_components['query'])) {
                    parse_str($url_components['query'], $params);
                    if (isset($params['user_id']) && isset($params['mulai']) && isset($params['akhir'])) {
                        $exists = \App\Models\Cuti::where('user_id', $params['user_id'])
                                    ->whereBetween('tanggal', [$params['mulai'], $params['akhir']])
                                    ->exists();
                        if (!$exists) {
                            $readNotif->delete();
                            return redirect()->back()->with('error', 'Data pengajuan cuti tersebut sudah tidak ada / telah dihapus.');
                        }
                    }
                }
             } else if (str_contains($action, '/cuti?mulai=')) {
                $url_components = parse_url($action);
                if (isset($url_components['query'])) {
                    parse_str($url_components['query'], $params);
                    if (isset($params['mulai']) && isset($params['akhir'])) {
                        $exists = \App\Models\Cuti::where('user_id', auth()->user()->id)
                                    ->whereBetween('tanggal', [$params['mulai'], $params['akhir']])
                                    ->exists();
                        if (!$exists) {
                            $readNotif->delete();
                            return redirect()->back()->with('error', 'Data pengajuan cuti Anda sudah tidak ada / telah dihapus.');
                        }
                    }
                }
             }
             return redirect($action);
        }

        return redirect('/notifications');
    }

    public function markAllRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca dan alarm suara dinonaktifkan.');
    }

}
