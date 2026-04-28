<?php

namespace App\Http\Controllers;  // ✅ namespace ajouté

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIController extends Controller  // ✅ Controller (e, pas o)
{
    public function analyze(Request $request)
{
    $comments = $request->input('comments', []);

    if (empty($comments)) {
        return response()->json(['error' => 'Aucun commentaire trouvé'], 400);
    }

    $textToAnalyze = implode("\n", array_slice($comments, 0, 100));

    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
            'Content-Type'  => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => 'llama-3.3-70b-versatile',
            'messages' => [
                [
                    'role'    => 'system',
                    'content' => 'You are an expert sentiment analyst for TikTok Live e-commerce streams targeting Maghreb audiences (Tunisia, Algeria, Morocco). 
Viewers comment in French, English, Modern Arabic, and DARIJA FRANCO-ARABE where numbers replace Arabic letters: 7=ح(h), 3=ع(a), 9=ق(q), 2=ء, 8=غ(gh).

════════════════════════════════
✅ POSITIVE — classify as positive if comment contains ANY of these:
════════════════════════════════

[PURCHASE INTENT]
- "me", "mee", "meee", "meeee" → wants to be picked
- "i want", "i need", "je veux", "je prends", "jaib", "nheb", "nheb nechri", "bghit", "3awni", "3andi", "ana", "ana nheb"
- "commander", "commande", "order", "add", "send", "livraison", "wين تبعث"
- "prix", "combien", "9addech", "ch7al", "how much", "price" → interested in price
- Numbers alone: "1", "2", "3", "10", "100" → ordering quantity

[PRODUCT COMPLIMENTS — DARIJA]
- "7aja behya", "7aja zina", "7aja mli7a", "7aja 3ajba"
- "sel3a behya", "sel3a zina", "sel3a mli7a", "sel3a 3ajba"
- "behi", "behya", "mli7", "mli7a", "zwina", "zwin", "mzyan", "mzyana"
- "3ajba", "3ajbni", "y3ajbni", "3jebni", "t3ajbni"
- "nchalah", "mchallah", "allah ybark", "baraka llah", "tbarkallah"
- "waw", "waaw", "waaaaw"

[PRODUCT COMPLIMENTS — FRENCH/ENGLISH]
- "super", "top", "bien", "très bien", "trop bien", "nickel", "parfait"
- "beau", "belle", "joli", "jolie", "magnifique", "incroyable"
- "bravo", "excellent", "merci", "thank you", "thanks", "thx"
- "good", "great", "nice", "love", "wow", "amazing", "beautiful"

[PRODUCT COMPLIMENTS — ARABIC]
- "جميل", "رائع", "ممتاز", "شكرا", "بارك الله", "ما شاء الله"
- "حلو", "زين", "مليح", "عجبني"

[ENGAGEMENT — viewer is active/interested]
- "hi", "hy", "hello", "salam", "slm", "السلام عليكم", "wech rak", "labas"
- "got coins", "200 coins", "coins" → happy viewer giving gifts

════════════════════════════════
❌ NEGATIVE — classify as negative if comment contains ANY of these:
════════════════════════════════

[PRICE COMPLAINTS]
- "ghali", "ghalia", "8ali", "9ali", "cher", "trop cher", "expensive", "too expensive"
- "غالي", "يسرف", "مبالغة", "prix excessif"

[QUALITY DOUBTS]
- "mchouma" (shame/bad), "3ib" (defect/shame), "hchouma"
- "khayeb", "khayba", "mouch behi", "mouch mli7", "mouch zwin"
- "fake", "faux", "arnaque", "scam", "escroquerie"
- "bad", "ugly", "moche", "horrible", "nul", "pourri"
- "خايب", "كذب", "غش", "مزيف"

[DISTRUST]
- "kdhab", "kazzab", "men3raf", "mouch sah", "maandfhemtch"
- "liar", "not real", "not true", "c faux"

[REFUSAL/DISINTEREST]
- "non", "no", "la", "mouch ena", "pas intéressé", "not interested"
- "trop", "basta", "stop"

════════════════════════════════
😐 NEUTRAL — only if NONE of the above:
════════════════════════════════
- Random emojis only with no text (🥑🎸🦒💐)
- Country/city/location names alone: "South Africa", "Dubai", "Somalia", "Tunisie", "Algérie"
- Flag emojis only: 🇸🇴 🇩🇿 🇹🇳 🇲🇦
- Single incomprehensible words with no sentiment
- Spam/bot messages repeated identically

════════════════════════════════
⚠️ IMPORTANT RULES:
════════════════════════════════
1. When in doubt between positive and neutral → choose POSITIVE (live sales context)
2. A message with BOTH positive and negative words → judge by dominant sentiment
3. Keep the EXACT original message text in your response arrays
4. Respond ONLY with valid JSON, no explanation, no markdown:
{"positive":["msg1","msg2"], "negative":["msg3"], "neutral":["msg4"]}'
                ],
                [
                    'role'    => 'user',
                    'content' => "Analyze these TikTok Live comments and classify each one:\n\n" . $textToAnalyze
                ]
            ],
            'response_format' => ['type' => 'json_object'],
            'temperature' => 0.0
        ]);

        if ($response->failed()) {
            Log::error('Groq error', [
                'status' => $response->status(),
                'body'   => $response->body()
            ]);
            return response()->json(['error' => 'Erreur API Groq : ' . $response->status()], 500);
        }

        $content = $response->json('choices.0.message.content');
        $decoded = json_decode($content, true);

        if (!$decoded || !isset($decoded['positive'])) {
            Log::error('Groq JSON invalide', ['content' => $content]);
            return response()->json(['error' => 'Réponse IA invalide'], 500);
        }

        return response()->json($decoded);

    } catch (\Exception $e) {
        Log::error('Groq exception', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}
