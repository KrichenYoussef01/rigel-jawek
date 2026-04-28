<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class FacebookController extends Controller
{
    

    

   public function start(Request $request)
{
    $link = $request->input('link');
    if (!$link) return response()->json(['error' => 'Lien manquant'], 400);

    if (!$this->isNodeRunning()) {
        $scriptPath = base_path('node/facebook_server.cjs');
        $logFile    = storage_path('logs/node_server.log');

        if (PHP_OS_FAMILY === 'Windows') {
           
            $vbs = 'Set objShell = CreateObject("WScript.Shell")' . "\n"
                 . 'objShell.Run "cmd /c node """ & "' . addslashes($scriptPath) . '"" >> """ & "' . addslashes($logFile) . '""" & " 2>&1", 0, False';

            $vbsPath = storage_path('logs/run_node.vbs');
            file_put_contents($vbsPath, $vbs);

            pclose(popen('wscript.exe "' . $vbsPath . '"', 'r'));

        } else {
            $cmd = 'node "' . $scriptPath . '" >> "' . $logFile . '" 2>&1 &';
            shell_exec($cmd);
        }

        
        $ready = false;
        for ($i = 0; $i < 30; $i++) {
            usleep(500_000);
            if ($this->isNodeRunning()) { $ready = true; break; }
        }

        if (!$ready) return response()->json(['error' => 'Node.js n\'a pas démarré'], 500);
    }

    try {
        $response = Http::timeout(30)->post('http://127.0.0.1:3000/start', ['link' => $link]);
        return response()->json($response->json(), $response->status());
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erreur Node', 'message' => $e->getMessage()], 500);
    }
}

    public function comments() {
        try {
            $res = Http::timeout(5)->get('http://127.0.0.1:3000/comments');
            return response($res->body(), 200)->header('Content-Type', 'text/plain; charset=utf-8');
        } catch (\Exception $e) {
            return response('', 200);
        }
    }
    
   

 



private function extractFacebookUsernameFromLive()
{
    
    $file = storage_path('app/facebook/live.txt');
    
    if (!file_exists($file)) {
        return null;
    }

    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    
    
    foreach ($lines as $line) {
       
        if (preg_match('/Live de @([a-zA-Z0-9._]+)/', $line, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/Page\s*:\s*([a-zA-Z0-9._]+)/i', $line, $matches)) {
            return $matches[1];
        }
       
        if (preg_match('/facebook\.com\/([a-zA-Z0-9._]+)/', $line, $matches)) {
            return $matches[1];
        }
    }

    return null;
}
    private function stopNode() {
        try { Http::timeout(2)->post('http://127.0.0.1:3000/stop'); } catch (\Exception $e) {}
        if (PHP_OS_FAMILY === 'Windows') {
            shell_exec('for /f "tokens=5" %a in (\'netstat -aon ^| findstr :3000\') do taskkill /F /PID %a 2>nul');
        } else {
            shell_exec('fuser -k 3000/tcp 2>/dev/null || true');
        }
    }

    private function isNodeRunning(): bool {
        try {
            $res = Http::timeout(2)->get('http://127.0.0.1:3000/comments');
            return $res->successful();
        } catch (\Exception $e) { return false; }
    }
}