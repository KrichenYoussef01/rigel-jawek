<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\TikTokRequest;
use Illuminate\Http\Request;

class TikTokController extends Controller
{
    
        public function analyzeComments(Request $request)
{
    $comments = $request->input('comments', []);

    if (empty($comments)) {
        return response()->json(['positive' => [], 'negative' => [], 'neutral' => []]);
    }

    
    $commentText = implode("\n", array_map(
        fn($i, $c) => ($i + 1) . ". " . $c,
        array_keys($comments),
        $comments
    ));

    
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        'Content-Type'  => 'application/json',
    ])->post('https://api.openai.com/v1/chat/completions', [
        'model' => 'gpt-3.5-turbo',
        'messages' => [
            [
                'role'    => 'system',
                'content' => 'Tu es un expert en analyse de sentiments. On te donne une liste de commentaires numérotés. Tu dois retourner UNIQUEMENT un JSON valide avec 3 tableaux : "positive", "negative", "neutral". Chaque tableau contient les textes des commentaires correspondants. Aucun texte avant ou après le JSON.'
            ],
            [
                'role'    => 'user',
                'content' => "Analyse ces commentaires et classe-les :\n\n" . $commentText
            ]
        ],
        'temperature' => 0.2,
        'max_tokens'  => 2000,
    ]);

    if (!$response->successful()) {
        return response()->json(['error' => 'Erreur OpenAI : ' . $response->body()], 500);
    }

    $content = $response->json()['choices'][0]['message']['content'] ?? '{}';

   
    $content = preg_replace('/```json|```/', '', $content);
    $content = trim($content);

    $result = json_decode($content, true);

    if (!$result) {
        return response()->json(['error' => 'Réponse IA invalide', 'raw' => $content], 500);
    }

    return response()->json([
        'positive' => $result['positive'] ?? [],
        'negative' => $result['negative'] ?? [],
        'neutral'  => $result['neutral']  ?? [],
    ]);
}

       public function start(TikTokRequest $request)
{
   
    $validated = $request->validated();
    $link = $validated['link'];

    try {
       
        $this->killNodeProcesses();
        sleep(1);

       
        $file = storage_path('app/tiktok/live.txt');
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        file_put_contents($file, '');

        
        $nodeDir = base_path('node');
        $sanitizedLink = str_replace('"', '""', $link);
        
        $vbsScript = 'Set objShell = CreateObject("WScript.Shell")' . PHP_EOL;
        $vbsScript .= 'objShell.CurrentDirectory = "' . $nodeDir . '"' . PHP_EOL;
        $vbsScript .= 'objShell.Run "node extractor.cjs ""' . $sanitizedLink . '""", 0, False' . PHP_EOL;
        
        $vbsPath = storage_path('app/tiktok/launch.vbs');
        file_put_contents($vbsPath, $vbsScript);
        
       
        exec('wscript "' . $vbsPath . '"');

        return response()->json(['ok' => true]);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Erreur lors du lancement de l\'extracteur : ' . $e->getMessage()
        ], 500);
    }
}
 





    
    public function comments()
    {
        $file = storage_path('app/tiktok/live.txt');
        
        if (!file_exists($file)) {
            return response('', 200)->header('Content-Type', 'text/plain');
        }
        
        return response(file_get_contents($file), 200)->header('Content-Type', 'text/plain');
    }
    

    public function stop()
    {
        $this->killNodeProcesses();
        
        $file = storage_path('app/tiktok/live.txt');
        if (file_exists($file)) {
            file_put_contents($file, '');
        }

        return response()->json(['ok' => true]);
    }

    private function killNodeProcesses()
    {
        exec('taskkill /F /IM node.exe 2>nul');
    }
      private function extractUsernameFromLive()
    {
        $file = storage_path('app/tiktok/live.txt');
        
        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        
        // Chercher la première ligne avec "Live de @username"
        foreach ($lines as $line) {
            if (preg_match('/Live de @([a-zA-Z0-9._]+)/', $line, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}