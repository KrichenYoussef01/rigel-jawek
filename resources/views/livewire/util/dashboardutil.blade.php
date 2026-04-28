<div>

   
    <div class="max-w-6xl mx-auto w-full">

        
        <div class="page-hero">
            <div class="page-hero-left">
               
                <div>
                    <div class="page-hero-title">TikTok Live Control</div>
                    <div class="page-hero-sub">Gestion des commandes TikTok Live</div>
                </div>
            </div>
            
       
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="dashboard-card p-6 flex items-center gap-5">
                <div class="bg-blue-500/10 p-4 rounded-2xl text-blue-500 text-2xl">💬</div>
                <div>
                    <h3 class="text-3xl font-bold" id="countComments">0</h3>
                    <p class="text-gray-400 text-sm">Commentaires</p>
                </div>
            </div>
            <div class="dashboard-card p-6 flex items-center gap-5">
                <div class="bg-purple-500/10 p-4 rounded-2xl text-purple-500 text-2xl">👤</div>
                <div>
                    <h3 class="text-3xl font-bold" id="countClients">0</h3>
                    <p class="text-gray-400 text-sm">Clients</p>
                </div>
            </div>
            <div class="dashboard-card p-6 flex items-center gap-5 border border-emerald-500/20">
                <div class="bg-emerald-500/10 p-4 rounded-2xl text-emerald-500 text-2xl">📦</div>
                <div>
                    <h3 class="text-3xl font-bold" id="countArticles">0</h3>
                    <p class="text-gray-400 text-sm">Articles Uniques</p>
                </div>
            </div>
            <div class="dashboard-card p-6 flex items-center gap-5 border border-cyan-500/20">
                <div class="bg-cyan-500/10 p-4 rounded-2xl text-cyan-500 text-2xl">📞</div>
                <div>
                    <h3 class="text-3xl font-bold" id="countPhones">0</h3>
                    <p class="text-gray-400 text-sm">Numéros Uniques</p>
                </div>
            </div>
        </div>

       
        <div id="planLimitsBar" class="dashboard-card p-4 mb-4 hidden">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <span id="planBadge" class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">—</span>
                    <span id="planExpire" class="text-[10px] text-gray-500"></span>
                </div>
                <div class="w-px h-8 bg-gray-700 hidden md:block"></div>
                <div class="flex-1 min-w-[140px]">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">⚡ Lives</span>
                        <span id="livesText" class="font-bold text-white">0 / —</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div id="livesBar" class="h-2 rounded-full transition-all duration-500 bg-indigo-500" style="width:0%"></div>
                    </div>
                </div>
                <div class="flex-1 min-w-[140px]">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">📦 Commandes</span>
                        <span id="commandesText" class="font-bold text-white">0 / —</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div id="commandesBar" class="h-2 rounded-full transition-all duration-500 bg-emerald-500" style="width:0%"></div>
                    </div>
                </div>
                <div class="flex-1 min-w-[140px]">
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-gray-400">📊 Exports</span>
                        <span id="exportsText" class="font-bold text-white">0 / —</span>
                    </div>
                    <div class="w-full bg-gray-800 rounded-full h-2">
                        <div id="exportsBar" class="h-2 rounded-full transition-all duration-500 bg-teal-500" style="width:0%"></div>
                    </div>
                </div>
                <a href="{{ route('pricing') }}" id="upgradeBtn"
                    class="hidden bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold px-4 py-2 rounded-xl whitespace-nowrap hover:brightness-110 transition-all">
                    ⚡ Upgrader
                </a>
            </div>
        </div>

       
        <div id="limitAlert" class="hidden mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-xl flex items-center gap-3">
            <span class="text-2xl">🚫</span>
            <div>
                <p class="text-red-400 font-bold text-sm" id="limitAlertTitle">Limite atteinte</p>
                <p class="text-red-300 text-xs" id="limitAlertMsg">Vous avez atteint la limite de votre plan.</p>
            </div>
            <a href="{{ route('pricing') }}" class="ml-auto bg-red-500 hover:bg-red-600 text-white text-xs font-bold px-4 py-2 rounded-xl transition-all">
                Upgrader →
            </a>
        </div>

        
        <button type="button" onclick="triggerAIAnalysis(event)" id="aiBtn"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 py-4 rounded-xl font-black text-white shadow-xl transition-all transform hover:-translate-y-1 mb-4">
            🧠 LANCER L'ANALYSE IA DES COMMENTAIRES
        </button>

        
        <div id="aiResultCards" class="hidden grid grid-cols-3 gap-4 mb-8">
            <div onclick="showSentimentComments('positive')"
                class="dashboard-card p-5 border border-green-500/30 cursor-pointer hover:border-green-400 hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">😊</span>
                    <span class="text-green-400 font-bold text-lg" id="percentPositive">0%</span>
                </div>
                <h3 class="text-3xl font-bold" id="countPositive">0</h3>
                <p class="text-gray-400 text-sm">Messages Positifs</p>
                <div class="w-full bg-gray-800 h-1.5 mt-3 rounded-full">
                    <div id="barPositive" class="bg-green-500 h-full transition-all duration-700" style="width:0%"></div>
                </div>
                <p class="text-green-400 text-xs mt-2 text-center font-bold">▶ Voir les commentaires</p>
            </div>
            <div onclick="showSentimentComments('negative')"
                class="dashboard-card p-5 border border-red-500/30 cursor-pointer hover:border-red-400 hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">😠</span>
                    <span class="text-red-400 font-bold text-lg" id="percentNegative">0%</span>
                </div>
                <h3 class="text-3xl font-bold" id="countNegative">0</h3>
                <p class="text-gray-400 text-sm">Messages Négatifs</p>
                <div class="w-full bg-gray-800 h-1.5 mt-3 rounded-full">
                    <div id="barNegative" class="bg-red-500 h-full transition-all duration-700" style="width:0%"></div>
                </div>
                <p class="text-red-400 text-xs mt-2 text-center font-bold">▶ Voir les commentaires</p>
            </div>
            <div onclick="showSentimentComments('neutral')"
                class="dashboard-card p-5 border border-gray-500/30 cursor-pointer hover:border-gray-400 hover:scale-105 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">😐</span>
                    <span class="text-gray-400 font-bold text-lg" id="percentNeutral">0%</span>
                </div>
                <h3 class="text-3xl font-bold" id="countNeutral">0</h3>
                <p class="text-gray-400 text-sm">Messages Neutres</p>
                <div class="w-full bg-gray-800 h-1.5 mt-3 rounded-full">
                    <div id="barNeutral" class="bg-gray-500 h-full transition-all duration-700" style="width:0%"></div>
                </div>
                <p class="text-gray-400 text-xs mt-2 text-center font-bold">▶ Voir les commentaires</p>
            </div>
        </div>

        
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

            <div class="lg:col-span-2 space-y-6">

            
                <div class="dashboard-card p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-purple-600/20 p-2 rounded-lg text-purple-500">⚙️</div>
                        <h2 class="text-lg font-bold">Configuration 1 : Modes d'affichage</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-gray-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-emerald-500/10 p-2 rounded-lg text-emerald-400">📦</div>
                                <div><p class="text-sm font-bold">Code article</p><p class="text-[11px] text-gray-500">Afficher uniquement les codes</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showArticleCode" class="sr-only peer" onchange="handleCheckboxChange('article')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            </label>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-gray-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-cyan-500/10 p-2 rounded-lg text-cyan-400">📞</div>
                                <div><p class="text-sm font-bold">Numéros téléphone</p><p class="text-[11px] text-gray-500">Afficher uniquement les numéros</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showPhoneNumbers" class="sr-only peer" onchange="handleCheckboxChange('phone')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                            </label>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-yellow-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-yellow-500/10 p-2 rounded-lg text-yellow-400">🎯</div>
                                <div><p class="text-sm font-bold">Commandes complètes</p><p class="text-[11px] text-gray-500">Code + Numéro par ligne</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showCombined" class="sr-only peer" onchange="handleCheckboxChange('combined')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
                            </label>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-pink-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-pink-500/10 p-2 rounded-lg text-pink-400">👤📦📞</div>
                                <div><p class="text-sm font-bold">Utilisateur + Code + Numéro</p><p class="text-[11px] text-gray-500">Pseudo + Article + Téléphone par ligne</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showUserCode" class="sr-only peer" onchange="handleCheckboxChange('usercode')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-pink-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

               
                <div class="dashboard-card p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-orange-600/20 p-2 rounded-lg text-orange-500">🛒</div>
                        <h2 class="text-lg font-bold">Configuration 2 : Options du panier</h2>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-blue-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-blue-500/10 p-2 rounded-lg text-blue-400">🛍️</div>
                                <div><p class="text-sm font-bold">Panier avec nom client</p><p class="text-[11px] text-gray-500">Afficher les commandes groupées par client</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showClientName" class="sr-only peer" onchange="handleCheckboxChange('basket')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl flex items-center justify-between border border-gray-800">
                            <div class="flex items-center gap-4">
                                <div class="bg-indigo-500/10 p-2 rounded-lg text-indigo-400">🔢</div>
                                <div><p class="text-sm font-bold">Grouper les répétitions</p><p class="text-[11px] text-gray-500">Ex: Article répété 6 fois → "6x C7"</p></div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="groupRepetitions" class="sr-only peer" onchange="handleCheckboxChange('grouprep')">
                                <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                        <div class="bg-black/20 p-4 rounded-xl border border-red-800">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-4">
                                    <div class="bg-red-500/10 p-2 rounded-lg text-red-400">🔒</div>
                                    <div><p class="text-sm font-bold">Limiter clients par article</p><p class="text-[11px] text-gray-500">Gestion de stock : limite le nombre de clients</p></div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="limitClientsPerArticle" class="sr-only peer" onchange="handleCheckboxChange('limitclients')">
                                    <div class="w-11 h-6 bg-gray-700 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="text-xs text-gray-400">Nombre max de clients :</label>
                                <input type="number" id="maxClientsLimit" min="1" max="100" value="6"
                                    onchange="loadComments()"
                                    class="w-20 bg-black/30 border border-gray-600 rounded-lg px-3 py-2 text-sm text-white focus:border-red-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            
            <div class="lg:col-span-3">
                <div class="dashboard-card h-full flex flex-col">
                    <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                        <h2 class="text-lg font-semibold flex items-center gap-2">
                            <span class="text-blue-400">💬</span>
                            <span id="displayTitle">Commentaires</span>
                        </h2>
                    </div>
                    <div class="p-4 flex-grow" id="outputContainer">
                        <div id="out" class="w-full h-[580px] overflow-y-auto pr-2 space-y-2">
                            <div class="text-gray-500 text-sm italic text-center pt-20">En attente de données...</div>
                        </div>
                        <div id="basketView"    class="hidden overflow-y-auto h-[580px] pr-2"></div>
                        <div id="limitedView"   class="hidden overflow-y-auto h-[580px] pr-2"></div>
                        <div id="sentimentView" class="hidden"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

  
    <div id="endLiveModal" class="modal-overlay hidden">
        <div class="modal-content">
            <div class="modal-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold flex items-center gap-3">🎉 Récapitulatif du Live</h2>
                        <p class="text-sm text-white/80 mt-1" id="modalDateTime"></p>
                    </div>
                    <button onclick="closeEndLiveModal()" class="text-white hover:bg-white/10 p-2 rounded-lg transition-all">✕</button>
                </div>
            </div>
            <div class="modal-body">
                <div class="stats-grid" id="modalStats"></div>
                <h3 class="text-xl font-bold mb-4 flex items-center gap-2">🛒 Tous les Paniers Clients</h3>
                <div id="modalBaskets"></div>
            </div>
            <div class="modal-footer">
                <button onclick="copyAllData()"    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">📋 Copier Tout</button>
                <button onclick="exportToText()"   class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">💾 Télécharger TXT</button>
                <button onclick="exportToCSV()"    class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">📊 Télécharger CSV</button>
                <button onclick="confirmEndLive()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">⭕ Confirmer & Terminer</button>
            </div>
        </div>
    </div>

</div>