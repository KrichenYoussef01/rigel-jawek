if (!window.tiktokLiveInstance) {
    window.tiktokLiveInstance = true;
    
    function tiktokLive() {
        return {
            view: 'launch',
            activeComments: 'tiktok',
            filterMode: 'all',

            platform: '',
            url: '',
            connecting: false,
            isLive: false,
            error: '',
            

            config: {
                showBasket:  false,
                groupQty:    false,
                limitPerArt: false,
                maxStock:    6
            },

            stats: { comments: 0, clients: 0, articles: 0, phones: 0 },
            // ── Sentiments IA ──
            aiAnalysisDone: false,
            aiCardsVisible: false,
            sentimentFilter: 'all',
            aiSentimentMap: {},
            aiParsedCommentsSnapshot: [],
            aiParsedComments: [],
            sentimentStats: { positive: 0, negative: 0, neutral: 0, total: 0 },
            commentsTiktok: [],
            commentsFacebook: [],
            seenComments: new Set(),
            showEndModal: false,
            endModalDate: '',
            endBaskets:   [],
            saving:       false,

            userProfiles: { tiktok: {}, facebook: {} },

            timer: null,
            pusher: null,
            channel: null,
            aiAnalyzing: false,

            get pageTitle() {
                const t = { launch: 'Lancer un Live', dashboard: 'Dashboard', codes: 'Codes Articles', historique: 'historique',sentiments: 'Sentiments IA' ,codestats: 'Statistiques par code',parametres:'parametres' };
                return t[this.view] || '';
            },

            sendLiveStats() {
                const platform = this.platform || 'tiktok';
                const profiles = Object.values(this.userProfiles[platform] || {});
                const codeCounts = {};
                profiles.forEach(profile => {
                    Object.entries(profile.articles || {}).forEach(([code, qty]) => {
                        codeCounts[code] = (codeCounts[code] || 0) + qty;
                    });
                });
                const labels = Object.keys(codeCounts);
                const data   = Object.values(codeCounts);
                if (!labels.length) return;
                Livewire.dispatch('updateLiveStats', { labels, data });
            },

            get isBusy()     { return this.connecting || this.isLive; },
            get showStatus() { return this.connecting || this.isLive; },

            get statusClass() {
                if (this.connecting) return 'status-connecting';
                if (this.isLive)     return 'status-live';
                return 'status-offline';
            },

            get statusLabel() {
                if (this.connecting) return 'Connexion...';
                if (this.isLive)     return 'Live actif';
                return 'Hors ligne';
            },

            get currentComments() {
                return this.activeComments === 'facebook'
                    ? this.commentsFacebook
                    : this.commentsTiktok;
            },

            get currentProfiles() {
                return this.userProfiles[this.activeComments] || {};
            },

            get displayList() {
                if (this.config.limitPerArt) {
                    return this.stockByArticle;
                }

                if (this.config.showBasket) {
                    return Object.values(this.currentProfiles)
                        .filter(p => p.hasCode && p.hasPhone && p.user && p.user.trim() !== '')
                        .sort((a, b) => b._ts - a._ts);
                }

                switch (this.filterMode) {
                    case 'code':
                        const codeMap = {};
                        this.currentComments.filter(c => c.hasCode && c.user && c.user.trim() !== '').forEach(c => {
                            if (!codeMap[c.articleCode]) codeMap[c.articleCode] = c;
                        });
                        return Object.values(codeMap);
                    case 'phone':
                        const phoneMap = {};
                        this.currentComments.filter(c => c.hasPhone && c.user && c.user.trim() !== '').forEach(c => {
                            if (!phoneMap[c.phoneNumber]) phoneMap[c.phoneNumber] = c;
                        });
                        return Object.values(phoneMap);
                    case 'both':
                        return Object.values(this.currentProfiles)
                            .filter(p => (p.hasCode || p.hasPhone) && p.user && p.user.trim() !== '')
                            .sort((a, b) => b._ts - a._ts);
                    case 'client':
                        return Object.values(this.currentProfiles)
                            .filter(p => p.hasCode && p.hasPhone && p.user && p.user.trim() !== '')
                            .sort((a, b) => b._ts - a._ts);
                    default:
                        return this.currentComments.filter(c => c.user && c.user.trim() !== '');
                }
            },

            get emptyMessage() {
                if (this.config.showBasket)
                    return 'Aucun client avec code article ET numéro de téléphone';
                const msgs = {
                    all:    'Aucun commentaire pour le moment',
                    code:   'Aucun code article détecté',
                    phone:  'Aucun numéro de téléphone détecté',
                    both:   'Aucun utilisateur avec code ou numéro',
                    client: 'Aucune fiche client disponible',
                };
                return msgs[this.filterMode] || '';
            },

            cardClass(item) {
                if (this.config.showBasket)     return 'type-basket';
                if (this.filterMode === 'client') return 'type-client';
                if (this.filterMode === 'both')   return 'type-both';
                if (this.filterMode === 'code')   return 'type-code';
                if (this.filterMode === 'phone')  return 'type-phone';
                if (item.hasCode && item.hasPhone) return 'type-both';
                if (item.hasCode)  return 'type-code';
                if (item.hasPhone) return 'type-phone';
                return 'type-all';
            },

            calculateStock(platform, articleCode) {
                if (!articleCode) return 0;
                const profiles = Object.values(this.userProfiles[platform] || {});
                return profiles.reduce((total, p) => {
                    if (p.articleCode === articleCode) {
                        return total + (p.quantity || 1);
                    }
                    return total;
                }, 0);
            },

            get filteredSentimentComments() {
                return this.aiParsedCommentsSnapshot
                    .map((c, idx) => ({
                        ...c,
                        sentiment: this.aiSentimentMap[idx] || 'neutral'
                    }))
                    .filter(c => this.sentimentFilter === 'all' || c.sentiment === this.sentimentFilter);
            },

            get stockByArticle() {
                const platform = this.activeComments;
                const comments  = platform === 'facebook' ? this.commentsFacebook : this.commentsTiktok;
                const profiles  = this.userProfiles[platform] || {};
                const grouped   = {};

                comments.forEach(c => {
                    if (!c.articleCode || !c.user) return;
                    const code = c.articleCode;

                    if (!grouped[code]) {
                        grouped[code] = { code, clientMap: {}, total: 0 };
                    }

                    if (!grouped[code].clientMap[c.user]) {
                        grouped[code].clientMap[c.user] = {
                            user:     c.user,
                            phone:    profiles[c.user]?.phoneNumber || null,
                            quantity: 0
                        };
                    }

                    if (this.config.groupQty) {
                        grouped[code].clientMap[c.user].quantity++;
                    } else {
                        grouped[code].clientMap[c.user].quantity = 1;
                    }

                    grouped[code].total++;
                });

                return Object.values(grouped).map(article => ({
                    code:    article.code,
                    total:   article.total,
                    clients: Object.values(article.clientMap).map(cl => ({
                        ...cl,
                        phone: profiles[cl.user]?.phoneNumber || cl.phone || null
                    }))
                }));
            },

            // ── Init ──
            init() {
                if (this._initialized) return;
                this._initialized = true;
                this.initPusher();
                this.refreshCodes();
                window.addEventListener('refresh-codes', () => this.refreshCodes());
                window.addEventListener('force-stats-refresh', () => this.sendLiveStats());
                window.addEventListener('stop-live', () => this.stopQuick());
            },

            initPusher() {
                if (typeof Pusher === 'undefined') return;
                try {
                    this.pusher = new Pusher(window.pusherAppKey, { 
                        cluster: window.pusherCluster, 
                        forceTLS: true 
                    });
                    this.channel = this.pusher.subscribe('tiktok-live');
                    this.channel.bind('new-comment', () => {
                        if (!this.isLive) return;
                        const endpoint = this.platform === 'facebook' ? '/facebook/comments' : '/tiktok/comments';
                        fetch(endpoint)
                            .then(r => r.text())
                            .then(t => { if (t.trim()) this.reparseAll(t, this.platform); })
                            .catch(() => {});
                    });
                } catch (e) {}
            },

            escapeRegex(str) {
                return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            },

            isStockExceeded(platform, articleCode) {
                if (!this.config.limitPerArt) return false;
                const currentStock = this.calculateStock(platform, articleCode);
                return currentStock >= this.config.maxStock;
            },

            // ── Main parsing function (corrigée) ──
          reparseAll(rawText, platform) {
    const newProfiles = { ...this.userProfiles[platform] }; // On part des profils existants
    const newComments = platform === 'facebook' ? [...this.commentsFacebook] : [...this.commentsTiktok];
    let totalAddedInThisCycle = 0;

    let rawFixed = rawText.trim();
    rawFixed = rawFixed.replace(/\[(\d{1,2})\n(\d{2}:\d{2})\]/g, '[$1:$2]');
    rawFixed = rawFixed.replace(/\[(\d{1,2}:\d{2})\n(\d{2})\]/g, '[$1:$2]');

    const TIMESTAMP_RE = /^\[(\d{1,2}:\d{2}(?::\d{2})?)\]\s*/;
    const lignes = rawFixed.split('\n').filter(l => l.trim() !== '');

    lignes.forEach(ligne => {
        if (!ligne.trim()) return;
        let time = null, restant = ligne;

        const tsMatch = ligne.match(TIMESTAMP_RE);
        if (tsMatch) { 
            time = tsMatch[1]; 
            restant = ligne.slice(tsMatch[0].length); 
        }

        if (!restant.includes(':')) return;
        const colonIdx = restant.indexOf(':');
        let user = restant.substring(0, colonIdx).trim().replace(/^@/, '');
        const text = restant.substring(colonIdx + 1).trim();

        if (user === '' || !text || user.includes('Live de') || /^\d{2}:\d{2}$/.test(user)) return;

        // --- ANTI-DUPLICATION ---
        // On crée un identifiant unique pour ce message précis
        const commentId = `${time}-${user}-${text}`;
        if (this.seenComments.has(commentId)) return; // On ignore si déjà vu
        this.seenComments.add(commentId);
        // -------------------------

        const matchedCode = (window.codeArticles || []).find(code =>
            new RegExp(`(?<![A-Z0-9])${this.escapeRegex(code)}(?![A-Z0-9])`, 'i').test(text)
        );
        const articleCode = matchedCode || null;
        const hasCode = !!matchedCode;

        const phoneMatch = text.match(/\b([24579]\d{7})\b/);
        const phoneNumber = phoneMatch ? phoneMatch[1] : null;
        const hasPhone = !!phoneMatch;

        if (hasCode && this.isStockExceeded(platform, articleCode)) return;

        if (!newProfiles[user]) {
            newProfiles[user] = {
                user, articles: {}, articleCode: null, phoneNumber: null,
                hasCode: false, hasPhone: false, quantity: 0, time: time || '', _ts: Date.now()
            };
        }

        const profile = newProfiles[user];

        // Dans reparseAll(), après avoir détecté hasCode = true
// Cherchez ce bloc :
if (hasCode) {
    profile.hasCode    = true;
    profile.articleCode = articleCode;
    if (this.config.groupQty) {
        profile.articles[articleCode] = (profile.articles[articleCode] || 0) + 1;
    } else {
        profile.articles[articleCode] = 1;
    }
    profile.quantity = Object.values(profile.articles).reduce((a, b) => a + b, 0);

    // ✅ Incrémenter la commande si c'est la première fois qu'on voit ce code pour cet user
    const commandeKey = `commande-${user}-${articleCode}`;
    if (!this.seenCommandes) this.seenCommandes = new Set();

    if (!this.seenCommandes.has(commandeKey)) {
        this.seenCommandes.add(commandeKey);
        this.incrementCommande(); // appel silencieux
    }
}

        if (hasPhone) {
            profile.phoneNumber = phoneNumber;
            profile.hasPhone = true;
        }

        if (time) profile.time = time;

        // Ajouter en haut de la liste (le plus récent d'abord)
        newComments.unshift({
            user, text, time: time || '',
            hasCode, hasPhone, articleCode, phoneNumber,
            quantity: hasCode ? (profile.articles[articleCode] || 1) : 0,
            _ts: Date.now()
        });
    });

    // Mise à jour de l'état Alpine
    if (platform === 'facebook') this.commentsFacebook = newComments;
    else this.commentsTiktok = newComments;

    this.userProfiles[platform] = newProfiles;
    this.userProfiles = { ...this.userProfiles };

    // Recalcul des stats globales
    const allCurrentProfiles = Object.values(newProfiles);
    this.stats.comments = this.seenComments.size;
    this.stats.clients = allCurrentProfiles.filter(p => p.hasCode && p.hasPhone).length;
    this.stats.articles = new Set(allCurrentProfiles.flatMap(p => Object.keys(p.articles))).size;
    this.stats.phones = new Set(allCurrentProfiles.map(p => p.phoneNumber).filter(Boolean)).size;

    this.aiParsedComments = newComments.map(c => ({ user: c.user, message: c.text }));
    setTimeout(() => this.sendLiveStats(), 50);
},

            // ── Actions ──
           async start() {
    this.error = '';
    if (!this.platform || !this.url) {
        this.error = 'Veuillez sélectionner une plateforme et saisir un lien.';
        return;
    }

    this.connecting = true;
    const csrf = document.querySelector('meta[name=csrf-token]')?.content;

    // ✅ Vérifier et incrémenter le compteur de lives
    try {
        const limitRes = await fetch('/increment/live', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
        });

        if (!limitRes.ok) {
            const limitData = await limitRes.json();
            this.connecting = false;
            this.error = limitData.message || 'Limite de lives atteinte.';

            // Afficher une alerte SweetAlert
            Swal.fire({
                title: '🚫 Limite atteinte',
                text: limitData.message || 'Vous avez atteint votre limite de sessions Live.',
                icon: 'warning',
                confirmButtonColor: '#6366f1',
                confirmButtonText: 'Voir mon abonnement',
            }).then(result => {
                if (result.isConfirmed) {
                    // Rediriger vers les paramètres
                    let el = document.querySelector('.live-overlay');
                    if (el && window.Alpine) Alpine.$data(el).view = 'parametres';
                }
            });
            return;
        }
    } catch (e) {
        this.connecting = false;
        this.error = 'Erreur de vérification des limites.';
        return;
    }

    // ✅ Réinitialisation complète avant un nouveau live
    this.commentsTiktok   = [];
    this.commentsFacebook = [];
    this.userProfiles     = { tiktok: {}, facebook: {} };
    this.seenComments.clear();
if (!this.seenCommandes) this.seenCommandes = new Set();
this.seenCommandes.clear(); 
    this.stats            = { comments: 0, clients: 0, articles: 0, phones: 0 };
    this.aiParsedComments = [];
    this.aiAnalysisDone   = false;
    this.aiCardsVisible   = false;
    this.aiSentimentMap   = {};
    this.aiParsedCommentsSnapshot = [];
    this.sentimentStats   = { positive: 0, negative: 0, neutral: 0, total: 0 };
    this.filterMode       = 'all';
    this.activeComments   = this.platform;

    try {
        const endpoint = this.platform === 'facebook' ? '/facebook/start' : '/tiktok/start';
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ link: this.url })
        });
        const resData = await res.json();
        if (!res.ok) throw new Error(resData.message || 'Erreur serveur');

        this.isLive    = true;
        this.connecting = false;
        this.view      = 'dashboard';
        this.startPolling();
        Alpine.store('live', { isLive: true });
    } catch (e) {
        this.connecting = false;
        this.error = e.message;
    }
},async incrementCommande() {
    try {
        const csrf = document.querySelector('meta[name=csrf-token]')?.content;
        const res  = await fetch('/increment/commande', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
        });

        if (!res.ok) {
            const data = await res.json();
            // Afficher un avertissement non bloquant
            console.warn('Limite commandes:', data.message);

            // Toast non bloquant en haut à droite
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'warning',
                title: data.message || 'Limite de commandes atteinte',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
            });
        }
    } catch (e) {
        console.warn('Erreur increment commande:', e);
    }
},

            startPolling() {
                if (this.timer) clearInterval(this.timer);
                let lastStatsKey = '';
                this.timer = setInterval(async () => {
                    if (!this.isLive) return;
                    try {
                        const endpoint = this.platform === 'facebook' ? '/facebook/comments' : '/tiktok/comments';
                        const res = await fetch(endpoint);
                        const rawText = await res.text();
                        if (rawText.trim()) this.reparseAll(rawText, this.platform);

                        const platform = this.platform || 'tiktok';
                        const profiles = Object.values(this.userProfiles[platform] || {});
                        const codeCounts = {};
                        profiles.forEach(profile => {
                            Object.entries(profile.articles || {}).forEach(([code, qty]) => {
                                codeCounts[code] = (codeCounts[code] || 0) + qty;
                            });
                        });
                        const currentKey = JSON.stringify(codeCounts);
                        if (currentKey !== lastStatsKey && Object.keys(codeCounts).length > 0) {
                            lastStatsKey = currentKey;
                            this.sendLiveStats();
                        }
                    } catch (e) { console.warn(e); }
                }, 2000);
            },

            stopQuick() {
                if (this.timer) { clearInterval(this.timer); this.timer = null; }
                this.isLive = false;
                this.connecting = false;
                Alpine.store('live', { isLive: false });
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const endpoint = this.platform === 'facebook' ? '/facebook/stop' : '/tiktok/stop';
                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
                }).catch(() => {});
            },

            stop() {
                const profiles = Object.values(this.userProfiles[this.platform] || {});
                // ✅ APRÈS — prend TOUS les codes articles du profil
this.endBaskets = profiles
    .filter(p => (p.hasCode || p.hasPhone) && p.user && p.user.trim() !== '')
    .map(p => ({
        client:   p.user,
        articles: Object.keys(p.articles || {}),  // ← CORRECTION ICI
        phones:   p.phoneNumber ? [p.phoneNumber] : [],
        quantity: p.quantity || 1
    }));
                this.endModalDate = new Date().toLocaleDateString('fr-FR', {
                    weekday: 'long', year: 'numeric', month: 'long',
                    day: 'numeric', hour: '2-digit', minute: '2-digit'
                });
                if (this.timer) { clearInterval(this.timer); this.timer = null; }
                this.isLive = false;
                this.showEndModal = true;
                Alpine.store('live', { isLive: false });
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const endpoint = this.platform === 'facebook' ? '/facebook/stop' : '/tiktok/stop';
                fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
                }).catch(() => {});
            },

            cancelEndModal() {
                this.aiAnalysisDone = false;
                this.aiCardsVisible = false;
                this.sentimentFilter = 'all';
                this.aiSentimentMap = {};
                this.aiParsedCommentsSnapshot = [];
                this.aiParsedComments = [];
                this.sentimentStats = { positive: 0, negative: 0, neutral: 0, total: 0 };
                this.showEndModal = false;
                this.url = '';
                this.commentsTiktok = [];
                this.commentsFacebook = [];
                this.userProfiles = { tiktok: {}, facebook: {} };
                this.seenComments.clear();
                this.stats = { comments: 0, clients: 0, articles: 0, phones: 0 };
                this.filterMode = 'all';
                this.activeComments = this.platform; 
                this.config = { showBasket: false, groupQty: false, limitPerArt: false, maxStock: 6 };
                this.view = 'launch';
            },

           async downloadCSV() {
    // ✅ Incrémenter l'export
    const csrf = document.querySelector('meta[name=csrf-token]')?.content;
    try {
        const res = await fetch('/increment/export', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
        });
        if (!res.ok) {
            const data = await res.json();
            Swal.fire({
                title: '🚫 Limite exports atteinte',
                text: data.message,
                icon: 'warning',
                confirmButtonColor: '#6366f1',
            });
            return; // Bloquer le téléchargement
        }
    } catch(e) {}

    // Générer le CSV normalement
    let csv = 'Client,Articles,Téléphones\n';
    this.endBaskets.forEach(b => {
        csv += `"${b.client}","${b.articles.join(' | ')}","${b.phones.join(' | ')}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `live_${this.platform}_${Date.now()}.csv`;
    a.click();
},

async downloadTXT() {
    // ✅ Incrémenter l'export
    const csrf = document.querySelector('meta[name=csrf-token]')?.content;
    try {
        const res = await fetch('/increment/export', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf }
        });
        if (!res.ok) {
            const data = await res.json();
            Swal.fire({
                title: '🚫 Limite exports atteinte',
                text: data.message,
                icon: 'warning',
                confirmButtonColor: '#6366f1',
            });
            return;
        }
    } catch(e) {}

    // Générer le TXT normalement
    let txt = `=== LIVE ${this.platform.toUpperCase()} ===\n`;
    txt += `Date : ${this.endModalDate}\n`;
    txt += `Commentaires : ${this.stats.comments}\n`;
    txt += `Clients : ${this.stats.clients}\n`;
    txt += `Articles : ${this.stats.articles}\n`;
    txt += `Numéros : ${this.stats.phones}\n\n`;
    txt += `=== PANIERS ===\n`;
    this.endBaskets.forEach((b, i) => {
        txt += `\nPanier #${i+1} - ${b.client}\n`;
        b.articles.forEach(a => { txt += `  - ${a}\n`; });
        b.phones.forEach(p   => { txt += `  - ${p}\n`; });
    });
    const blob = new Blob([txt], { type: 'text/plain;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `live_${this.platform}_${Date.now()}.txt`;
    a.click();
},

            async confirmEndLive() {
                this.saving = true;
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const payload = {
                    platform: this.platform,
                    link: this.url,
                    total_comments: this.stats.comments,
                    total_clients: this.stats.clients,
                    total_articles: this.stats.articles,
                    total_phones: this.stats.phones,
                    baskets: this.endBaskets.map(b => ({
                        client_name: b.client,
                        articles: b.articles,
                        phones: b.phones,
                    }))
                };
                try {
                    const res = await fetch('/end-live', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify(payload)
                    });
                    const result = await res.json();
                    if (result.success) {
                        this.showEndModal = false;
                        this.cancelEndModal();
                        Swal.fire({
                            title: '✅ Enregistré avec succès !',
                            html: `<div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                                    <div style="display:flex;justify-content:space-between;background:#f0fdf4;border-radius:10px;padding:10px 16px;font-size:14px;">
                                        <span style="color:#64748b;">💬 Commentaires</span>
                                        <strong style="color:#15803d;">${this.stats.comments}</strong>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;background:#faf5ff;border-radius:10px;padding:10px 16px;font-size:14px;">
                                        <span style="color:#64748b;">👤 Clients</span>
                                        <strong style="color:#7e22ce;">${this.stats.clients}</strong>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;background:#f0fdf4;border-radius:10px;padding:10px 16px;font-size:14px;">
                                        <span style="color:#64748b;">📦 Articles</span>
                                        <strong style="color:#059669;">${this.stats.articles}</strong>
                                    </div>
                                    <div style="display:flex;justify-content:space-between;background:#ecfeff;border-radius:10px;padding:10px 16px;font-size:14px;">
                                        <span style="color:#64748b;">📞 Numéros</span>
                                        <strong style="color:#0891b2;">${this.stats.phones}</strong>
                                    </div>
                                </div>`,
                            icon: 'success',
                            confirmButtonColor: '#6366f1',
                            confirmButtonText: '🏠 Retour à l\'accueil',
                            timer: 6000,
                            timerProgressBar: true,
                            showConfirmButton: true,
                            customClass: { popup: 'rounded-2xl' }
                        });
                    } else {
                        Swal.fire({ title: 'Erreur', text: result.message || 'Une erreur inconnue est survenue.', icon: 'error', confirmButtonColor: '#e8541a' });
                    }
                } catch (e) {
                    Swal.fire({ title: 'Erreur réseau', text: e.message, icon: 'error', confirmButtonColor: '#e8541a' });
                } finally {
                    this.saving = false;
                }
            },

            copyPhone(phone, event) {
                if (!phone) return;
                navigator.clipboard.writeText(phone).then(() => {
                    const btn = event.target;
                    const orig = btn.textContent;
                    btn.textContent = '✅';
                    setTimeout(() => { btn.textContent = orig; }, 1500);
                });
            },

            toggleFilter(mode) {
                this.filterMode = (this.filterMode === mode) ? 'all' : mode;
            },

            logout() {
                if (confirm("Voulez-vous vous déconnecter ?")) {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                    fetch('/logout', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                        credentials: 'same-origin'
                    }).then(() => { window.location.href = '/'; })
                      .catch(err => { console.error('Logout error:', err); window.location.href = '/'; });
                }
            },

           async refreshCodes() {
    try {
        const res = await fetch('/user/codes');
        const codes = await res.json();
        window.codeArticles = codes;

        // ✅ Re-scanner les commentaires existants avec les nouveaux codes
        this.reprocessExistingComments();
    } catch (e) { console.warn('Erreur rafraîchissement codes', e); }
},

reprocessExistingComments() {
    ['tiktok', 'facebook'].forEach(platform => {
        const comments = platform === 'tiktok' ? this.commentsTiktok : this.commentsFacebook;
        const profiles = this.userProfiles[platform] || {};

        comments.forEach(c => {
            const matchedCode = (window.codeArticles || []).find(code =>
                new RegExp(`(?<![A-Z0-9])${this.escapeRegex(code)}(?![A-Z0-9])`, 'i').test(c.text)
            );

            if (matchedCode && !c.hasCode) {
                // ✅ Mettre à jour le commentaire
                c.hasCode = true;
                c.articleCode = matchedCode;

                // ✅ Mettre à jour le profil utilisateur
                if (profiles[c.user]) {
                    profiles[c.user].hasCode = true;
                    profiles[c.user].articleCode = matchedCode;
                    if (!profiles[c.user].articles) profiles[c.user].articles = {};
                    profiles[c.user].articles[matchedCode] = (profiles[c.user].articles[matchedCode] || 0) + 1;
                    profiles[c.user].quantity = Object.values(profiles[c.user].articles).reduce((a, b) => a + b, 0);
                }
            }
        });

        // ✅ Forcer la réactivité Alpine
        if (platform === 'tiktok') this.commentsTiktok = [...comments];
        else this.commentsFacebook = [...comments];
    });

    // ✅ Recalculer les stats
    this.userProfiles = { ...this.userProfiles };
    const allProfiles = Object.values(this.userProfiles[this.platform] || {});
    this.stats.clients  = allProfiles.filter(p => p.hasCode && p.hasPhone).length;
    this.stats.articles = new Set(allProfiles.flatMap(p => Object.keys(p.articles || {}))).size;

    setTimeout(() => this.sendLiveStats(), 50);
},

            async triggerAIAnalysis(event) {
                if (this.aiCardsVisible) {
                    this.aiCardsVisible = false;
                    this.aiAnalysisDone = false;
                    this.aiSentimentMap = {};
                    this.aiParsedCommentsSnapshot = [];
                    this.sentimentStats = { positive: 0, negative: 0, neutral: 0, total: 0 };
                    this.sentimentFilter = 'all';
                    return;
                }
                if (this.aiParsedComments.length === 0) {
                    alert('⚠️ Aucun commentaire à analyser.');
                    return;
                }
                const btn = event.currentTarget;
                this.aiAnalyzing = true;
                try {
                    const messages = this.aiParsedComments.map(c => c.message);
                    const csrf = document.querySelector('meta[name=csrf-token]')?.content;
                    const response = await fetch('/ai/analyze', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ comments: messages })
                    });
                    const data = await response.json();
                    if (data.error) { alert('Erreur IA : ' + data.error); btn.disabled = false; return; }
                    this.aiParsedCommentsSnapshot = [...this.aiParsedComments];
                    let pos = 0, neg = 0, neu = 0;
                    this.aiSentimentMap = {};
                    this.aiParsedCommentsSnapshot.forEach((c, idx) => {
                        const msg = c.message;
                        let s = 'neutral';
                        if (data.positive && data.positive.some(m => m.trim() === msg.trim())) { s = 'positive'; pos++; }
                        else if (data.negative && data.negative.some(m => m.trim() === msg.trim())) { s = 'negative'; neg++; }
                        else { neu++; }
                        this.aiSentimentMap[idx] = s;
                    });
                    this.sentimentStats = { positive: pos, negative: neg, neutral: neu, total: pos + neg + neu };
                    this.aiAnalysisDone = true;
                    this.aiCardsVisible = true;
                    this.view = 'sentiments';
                } catch (err) {
                    alert('Erreur réseau : ' + err.message);
                } finally {
                    this.aiAnalyzing = false;
                }
            },

            closeOverlay() { window.location.href = '/'; }
        };
    }
}