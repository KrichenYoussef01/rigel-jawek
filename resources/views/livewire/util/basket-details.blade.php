<div>
    @if(count($baskets) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($baskets as $basket)
                <div class="client-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-yellow-500/20 p-2 rounded-xl text-xl">🛒</div>
                            <div>
                                <p class="font-bold text-white">{{ $basket->client_name }}</p>
                                <p class="text-xs text-gray-500">
                                    Panier #{{ $loop->iteration }}
                                    @if($basket->time)
                                        · ⏱ {{ $basket->time }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <button onclick="openMessageModal('{{ addslashes($basket->client_name) }}')"
                                class="bg-indigo-600/20 hover:bg-indigo-600/40 text-indigo-400 text-xs px-3 py-1.5 rounded-lg transition">
                            💬 Message
                        </button>
                    </div>

                    @if(!empty($basket->articles))
                        <div class="flex flex-wrap gap-2 mb-2">
                            @foreach($basket->articles as $article)
                                <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-bold px-3 py-1 rounded-full">
                                    📦 {{ $article }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($basket->phones))
                        <div class="flex flex-wrap gap-2">
                            @foreach($basket->phones as $phone)
                                <span onclick="copyToClipboard('{{ $phone }}')"
                                      class="bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 text-xs font-bold px-3 py-1 rounded-full cursor-pointer hover:bg-cyan-500/20">
                                    📞 {{ $phone }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500 italic text-center py-10">Aucun panier enregistré pour cette session.</p>
    @endif
</div>