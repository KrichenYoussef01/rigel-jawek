<div x-data="{
    platform: '',
    liveUrl: '',
    isConnecting: false,
    isLive: false,
    errorMessage: '',
    polling: null,
    statusKey: '',
    retryCount: 0,
    maxRetries: 15,        // ⏱️ Timeout après 30 secondes (15 × 2s)
    
    platforms: {
        facebook: { label: 'Facebook Live', icon: '📘' },
        tiktok:   { label: 'TikTok Live',   icon: '🎵' }
    },

    async startLive() {
        if (!this.platform) {
            this.errorMessage = 'Veuillez choisir une plateforme.'
            return
        }
        if (!this.liveUrl) {
            this.errorMessage = 'Veuillez entrer un lien.'
            return
        }

        this.errorMessage = ''
        this.isConnecting = true
        this.retryCount = 0  // 🔄 Reset compteur

        try {
            const res = await fetch('/live/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    platform: this.platform,
                    liveUrl: this.liveUrl
                })
            })

            // 🆕 Vérifier si la réponse est OK avant JSON
            if (!res.ok) {
                const errorData = await res.json().catch(() => ({}))
                throw new Error(errorData.message || `Erreur serveur: ${res.status}`)
            }

            const data = await res.json()
            this.statusKey = data.key
            this.startPolling()

        } catch (e) {
            console.error('Erreur startLive:', e)
            this.errorMessage = e.message || 'Erreur réseau. Vérifiez votre connexion.'
            this.isConnecting = false
        }
    },

    startPolling() {
        this.polling = setInterval(async () => {
            try {
                this.retryCount++
                
                // 🆕 Timeout après maxRetries tentatives
                if (this.retryCount > this.maxRetries) {
                    this.stopPolling()
                    this.isConnecting = false
                    this.errorMessage = '⏱️ Délai dépassé. Le live est peut-être hors ligne ou indisponible.'
                    return
                }

                const res = await fetch('/live/status/' + this.statusKey)
                
                // 🆕 Vérifier erreur HTTP
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status}`)
                }

                const data = await res.json()

                if (data.status === 'success') {
                    this.stopPolling()
                    this.isConnecting = false
                    this.isLive = true
                    this.initPusher()  // 🆕 Démarrer Pusher une fois connecté
                } else if (data.status === 'error') {
                    this.stopPolling()
                    this.isConnecting = false
                    this.errorMessage = data.message ?? 'Erreur lors de la connexion au live.'
                }
                // Si status = 'connecting', on continue le polling
                
            } catch (e) {
                console.error('Erreur polling:', e)
                this.stopPolling()
                this.isConnecting = false
                this.errorMessage = '❌ Erreur de connexion au serveur. Veuillez réessayer.'
            }
        }, 2000)
    },

    stopPolling() {
        if (this.polling) {
            clearInterval(this.polling)
            this.polling = null
        }
    },

    // 🆕 NOUVEAU: Initialiser Pusher pour recevoir les commentaires
    initPusher() {
        if (typeof Pusher === 'undefined') {
            console.error('Pusher non chargé')
            return
        }

        const pusher = new Pusher('a2fc94bc4c0ee52d2a04', {
            cluster: 'mt1',
            forceTLS: true
        })

        const channel = pusher.subscribe('tiktok-live')
        
        channel.bind('new-comment', (data) => {
            console.log('💬 Nouveau commentaire:', data)
            // 🆕 Ici vous pouvez ajouter le commentaire à une liste
            // this.comments.unshift(data)
        })

        pusher.connection.bind('connected', () => {
            console.log('✅ Pusher connecté')
        })

        pusher.connection.bind('error', (err) => {
            console.error('❌ Erreur Pusher:', err)
        })
    },

    stopLive() {
        this.stopPolling()
        this.isLive = false
        this.isConnecting = false
        this.platform = ''
        this.liveUrl = ''
        this.statusKey = ''
        this.retryCount = 0
        this.errorMessage = ''
    }
}"
class="p-6">

    <div class="bg-white rounded-lg shadow-md p-6 max-w-xl mx-auto">

        <h2 class="text-2xl font-bold mb-1">📡 Démarrer un Live</h2>
        <p class="text-gray-500 text-sm mb-6">Choisissez votre plateforme et lancez l'extraction en direct.</p>

        <!-- 🆕 Meilleur affichage des erreurs -->
        <div x-show="errorMessage"
             x-transition
             class="flex items-center gap-2 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-5">
            <span>⚠️</span>
            <span x-text="errorMessage"></span>
            <!-- 🆕 Bouton pour fermer l'erreur -->
            <button @click="errorMessage = ''" class="ml-auto text-red-500 hover:text-red-700">✕</button>
        </div>

        <div x-show="isLive"
             x-transition
             class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 flex items-center gap-2">
            <span>✅</span>
            <span>Live démarré avec succès !</span>
        </div>

        <!-- 🆕 Affichage du compteur de retry (optionnel, pour debug) -->
        <div x-show="isConnecting && retryCount > 0" class="text-xs text-gray-500 mb-2 text-center">
            Tentative <span x-text="retryCount"></span>/<span x-text="maxRetries"></span>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Plateforme</label>
            <div class="grid grid-cols-2 gap-3">
                <template x-for="(data, key) in platforms" :key="key">
                    <button type="button"
                        @click="if (!isLive && !isConnecting) platform = key"
                        :disabled="isLive || isConnecting"
                        :class="platform === key ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                        class="flex items-center gap-2 p-3 border-2 rounded-xl transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="text-2xl" x-text="data.icon"></span>
                        <span class="font-medium text-sm" x-text="data.label"></span>
                        <span x-show="platform === key" class="ml-auto text-indigo-600">✔</span>
                    </button>
                </template>
            </div>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Lien du live</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">🔗</span>
                <input type="url"
                    x-model="liveUrl"
                    :disabled="isLive || isConnecting"
                    placeholder="https://www.tiktok.com/@username/live"
                    class="w-full pl-9 pr-4 py-3 border border-gray-300 rounded-xl
                           focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                           outline-none transition text-sm
                           disabled:bg-gray-100 disabled:cursor-not-allowed"
                />
            </div>
        </div>

        <div class="flex items-center gap-3 mb-5 p-3 bg-gray-50 rounded-xl border border-gray-200">
            <span class="relative flex h-3 w-3 flex-shrink-0">
                <span :class="{
                        'bg-yellow-400': isConnecting,
                        'bg-red-400': isLive,
                        'hidden': !isLive && !isConnecting
                      }"
                      class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75">
                </span>
                <span :class="{
                        'bg-yellow-500': isConnecting,
                        'bg-red-500': isLive,
                        'bg-gray-400': !isLive && !isConnecting
                      }"
                      class="relative inline-flex rounded-full h-3 w-3">
                </span>
            </span>
            <span class="text-xs font-semibold uppercase tracking-wider"
                  :class="{
                      'text-yellow-600': isConnecting,
                      'text-red-600': isLive,
                      'text-gray-500': !isLive && !isConnecting
                  }">
                <span x-show="isConnecting">Connexion en cours...</span>
                <span x-show="isLive">Extraction en cours...</span>
                <span x-show="!isLive && !isConnecting">Hors ligne</span>
            </span>
        </div>

        <button x-show="isConnecting" disabled
            class="w-full flex items-center justify-center gap-2 bg-yellow-500 opacity-75 cursor-not-allowed text-white font-bold px-6 py-3 rounded-xl text-base">
            ⏳ Connexion en cours...
        </button>

        <button x-show="!isConnecting && !isLive"
            @click="startLive()"
            class="w-full flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-3 rounded-xl transition text-base">
            🚀 Démarrer l'extraction
        </button>

        <button x-show="isLive && !isConnecting"
            @click="stopLive()"
            class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition text-base">
            ⏹ Arrêter l'extraction
        </button>

    </div>
</div>