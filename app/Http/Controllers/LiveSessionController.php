<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class LiveSessionController extends Controller
{
    
public function endLive(Request $request)
{
    $data     = $request->all();
    $platform = $data['platform'] ?? 'tiktok';
    $userId   = Auth::id();

    DB::beginTransaction();
    try {
        if ($platform === 'tiktok') {
            $sessionId = DB::table('live_sessions')->insertGetId([
                'user_id'          => $userId,
                'tiktok_link'      => $data['link']           ?? '',
                'total_comments'   => $data['total_comments'] ?? 0,
                'total_clients'    => $data['total_clients']  ?? 0,
                'total_articles'   => $data['total_articles'] ?? 0,
                'total_phones'     => $data['total_phones']   ?? 0,
                'started_at'       => now()->subHour(),
                'ended_at'         => now(),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            foreach ($data['baskets'] ?? [] as $basket) {
                DB::table('live_baskets')->insert([
                    'live_session_id' => $sessionId,
                    'client_name'     => $basket['client_name'],
                    'articles'        => json_encode($basket['articles']),
                    'phones'          => json_encode($basket['phones']),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        } else {
            $sessionId = DB::table('facebook_sessions')->insertGetId([
                'user_id'        => $userId,
                'live_link'      => $data['link']           ?? '',
                'total_comments' => $data['total_comments'] ?? 0,
                'total_clients'  => $data['total_clients']  ?? 0,
                'total_articles' => $data['total_articles'] ?? 0,
                'total_phones'   => $data['total_phones']   ?? 0,
                'started_at'     => now()->subHour(),
                'ended_at'       => now(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            foreach ($data['baskets'] ?? [] as $basket) {
                DB::table('live_baskets_fb')->insert([
                    'facebook_session_id' => $sessionId,
                    'client_name'         => $basket['client_name'],
                    'articles'            => json_encode($basket['articles']),
                    'phones'              => json_encode($basket['phones']),
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }

        DB::commit();
        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
}
public function destroy($id)
{
    $userId = Auth::id();

    // Vérifier si c'est TikTok
    $tiktok = DB::table('live_sessions')->where('id', $id)->where('user_id', $userId)->first();
    if ($tiktok) {
        DB::table('live_baskets')->where('live_session_id', $id)->delete();
        DB::table('live_sessions')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // Vérifier si c'est Facebook
    $facebook = DB::table('facebook_sessions')->where('id', $id)->where('user_id', $userId)->first();
    if ($facebook) {
        DB::table('live_baskets_fb')->where('facebook_session_id', $id)->delete();
        DB::table('facebook_sessions')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    return response()->json(['success' => false, 'message' => 'Session introuvable'], 404);
}
}
