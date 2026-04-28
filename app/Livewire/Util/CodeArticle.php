<?php

namespace App\Livewire\Util;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Code;
use Illuminate\Support\Facades\Auth;

class CodeArticle extends Component
{
    use WithFileUploads;


    protected $skipModal = true;

    public $codes;
    public $newCode;
    public $importFile;
    public $showAddModal = false; 
    public $editingId = null;
    public $editCode  = '';
    protected $rules = [
        'newCode' => 'required|string|unique:codes,code',
    ];

    public function mount()
    {
        $this->loadCodes();
    }

    public function loadCodes()
    {
        $this->codes = Code::where('user_id', Auth::id())->get();
    }

   public function addCode()
{
    if (empty($this->newCode)) {
        $this->addError('newCode', 'Le code est requis.');
        return;
    }


    if (Code::where('code', $this->newCode)->where('user_id', Auth::id())->exists()) {
        $this->addError('newCode', 'Ce code existe déjà pour votre compte.');
        return;
    }

    Code::create([
        'code' => $this->newCode,
        'user_id' => Auth::id(),
    ]);

    $this->reset('newCode');
    $this->showAddModal = false;
    $this->loadCodes();
    session()->flash('message', 'Code ajouté avec succès.');
    $this->dispatch('refresh-codes');
}

    public function deleteCode($id)
    {
        Code::where('id', $id)->where('user_id', Auth::id())->delete();
        $this->loadCodes();
        session()->flash('message', 'Code supprimé.');
         $this->dispatch('refresh-codes');
        }

    public function export()
    {
        $codes = Code::where('user_id', Auth::id())->get(['code']);

        $filename = 'codes_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
        ];

        $callback = function () use ($codes) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['code']); // en-tête
            foreach ($codes as $code) {
                fputcsv($handle, [$code->code]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function startEdit($id)
    {
        $code = Code::where('id', $id)->where('user_id', Auth::id())->first();
        if ($code) {
            $this->editingId = $code->id;
            $this->editCode  = $code->code;
        }
    }

    // ✅ Correction de saveEdit
    public function saveEdit()
{
    // Nettoyage (Espaces en moins et tout en MAJUSCULES)
    $this->editCode = strtoupper(trim($this->editCode));

    $this->validate([
        'editCode' => 'required|string|max:50'
    ]);

    // 1. On récupère l'article qu'on veut modifier
    $code = Code::where('id', $this->editingId)
                ->where('user_id', Auth::id())
                ->first();

    if (!$code) {
        $this->editingId = null;
        return;
    }

    
    $existing = Code::where('code', $this->editCode)
                    ->where('user_id', Auth::id())
                    ->where('id', '!=', $this->editingId)
                    ->exists();

    if ($existing) {
        $this->addError('editCode', 'Ce code existe déjà.');
        return;
    }

    
    $code->update(['code' => $this->editCode]);

    
    $this->editingId = null;
    $this->editCode  = '';
    $this->loadCodes();
    
    
    session()->flash('message', 'Code modifié avec succès.');
     $this->dispatch('refresh-codes');
    }


  
     public function import()
{
    try {
        $path = $this->importFile->getRealPath();
        if (!$path || !file_exists($path)) {
            throw new \Exception("Fichier introuvable.");
        }

        
        $content = file_get_contents($path);
        if (substr($content, 0, 3) === "\xEF\xBB\xBF") {
            $content = substr($content, 3);
        }
        
        $lines = explode("\n", $content);
        $rows = array_map('str_getcsv', $lines);
        
        if (empty($rows)) {
            throw new \Exception("Le fichier CSV est vide ou invalide.");
        }
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($rows as $index => $row) {
            $code = trim($row[0] ?? '');
            if ($code === '') {
                $skipped++;
                continue;
            }
            
           
            if ($index === 0 && strtolower($code) === 'code') {
                continue;
            }
            
            $normalized = strtoupper($code);
            $exists = Code::where('user_id', Auth::id())
                          ->where('code', $normalized)
                          ->exists();
            if (!$exists) {
                Code::create([
                    'code'    => $normalized,
                    'user_id' => Auth::id(),
                ]);
                $imported++;
            } else {
                $skipped++;
            }
        }
        
        $this->reset('importFile');
        $this->loadCodes();
        
        $message = "$imported code(s) importé(s).";
        if ($skipped > 0) {
            $message .= " $skipped ligne(s) ignorée(s) (vides, en-tête ou déjà existantes).";
        }
        session()->flash('message', $message);
        $this->dispatch('refresh-codes');
        
    } catch (\Exception $e) {
        $this->reset('importFile');
        session()->flash('error', 'Erreur lors de l\'import : ' . $e->getMessage());
    }
}
    public function updatedImportFile()
{
    $this->validate([
        'importFile' => 'required|file|mimes:csv,txt|max:5120',
    ]);
    
    $this->import();
}
    public function render()
    {
        return view('livewire.util.code-article');
    }
    public function deleteAll()
{
    Code::where('user_id', Auth::id())->delete();
    $this->loadCodes();
    session()->flash('message', 'Tous vos codes ont été supprimés.');
     $this->dispatch('refresh-codes');
    }
}