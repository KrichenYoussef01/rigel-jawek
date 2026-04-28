/*const express = require('express');
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const os = require('os');

const app = express();

app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type, Authorization');
    if (req.method === 'OPTIONS') return res.sendStatus(200);
    next();
});

app.use(express.json());

const OUTPUT_FILE = path.join(__dirname, '../storage/app/live_data.txt');

let browser = null;
let page = null;
let intervalId = null;
let scrollIntervalId = null;
let isRunning = false;
let processedCommentIds = new Set();

function getChromePath() {
    const paths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        path.join(os.homedir(), 'AppData\\Local\\Google\\Chrome\\Application\\chrome.exe'),
    ];
    for (const p of paths) {
        if (fs.existsSync(p)) return p;
    }
    return null;
}

async function stopExtraction() {
    if (intervalId) clearInterval(intervalId);
    if (scrollIntervalId) clearInterval(scrollIntervalId);
    if (browser) {
        try { await browser.close(); } catch (e) {}
    }
    intervalId = null;
    scrollIntervalId = null;
    browser = null;
    page = null;
    isRunning = false;
    processedCommentIds.clear();
    console.log('⛔ Extraction arrêtée');
}

async function clickDiscussionTab() {
    try {
        const clicked = await page.evaluate(() => {
            const allElements = document.querySelectorAll('*');
            for (const el of allElements) {
                const text = el.innerText || el.textContent || '';
                if (
                    (text.includes('Discussion en direct') ||
                     text.includes('Live chat') ||
                     text.includes('Live Chat') ||
                     text.includes('دردشة مباشرة')) &&
                    (el.tagName === 'SPAN' || el.tagName === 'DIV' || el.tagName === 'BUTTON') &&
                    text.trim().length < 40
                ) {
                    el.click();
                    return true;
                }
            }
            return false;
        });
        if (clicked) {
            console.log('✅ Onglet "Discussion en direct" cliqué');
        } else {
            console.log('⚠️ Onglet non trouvé, on continue');
        }
    } catch (e) {
        console.log('Erreur click onglet:', e.message);
    }
}

async function doScroll() {
    if (!page) return;
    try {
        await page.evaluate(() => {
            const all = Array.from(document.querySelectorAll('*'));
            let maxScrollable = null;
            let maxHeight = 0;

            for (const el of all) {
                try {
                    const style = window.getComputedStyle(el);
                    const ov = style.overflow + style.overflowY;
                    if ((ov.includes('scroll') || ov.includes('auto')) &&
                        el.scrollHeight > el.clientHeight + 50 &&
                        el.clientHeight > 100) {
                        if (el.scrollHeight > maxHeight) {
                            maxHeight = el.scrollHeight;
                            maxScrollable = el;
                        }
                    }
                } catch(e) {}
            }

            if (maxScrollable) maxScrollable.scrollTop = maxScrollable.scrollHeight;
            window.scrollTo(0, document.body.scrollHeight);
        });
    } catch (e) {}
}

function startScrapingLoop() {
    scrollIntervalId = setInterval(doScroll, 1500);

    intervalId = setInterval(async () => {
        if (!isRunning || !page) return;

        try {
            const data = await page.evaluate(() => {
                const comments = [];
                const seen = new Set();

                // ✅ Méthode 1 : via les liens de noms
                const nameLinks = document.querySelectorAll('a[role="link"]');
                const processedContainers = new Set();

                nameLinks.forEach(link => {
                    try {
                        const nameText = link.innerText.trim();
                        if (!nameText || nameText.length < 2 || nameText.length > 60) return;
                        if (nameText.includes('http') || nameText.includes('www')) return;

                        let container = link;
                        for (let i = 0; i < 6; i++) {
                            if (!container.parentElement) break;
                            container = container.parentElement;
                        }

                        const containerId = nameText + container.innerText.substring(0, 50);
                        if (processedContainers.has(containerId)) return;
                        processedContainers.add(containerId);

                        let fullText = '';
                        container.querySelectorAll('div[dir="auto"], span[dir="auto"]').forEach(div => {
                            if (div.closest('a')) return;
                            if (div.closest('[role="button"]')) return;
                            if (div.closest('[aria-label*="Like"]')) return;
                            if (div.closest('[aria-label*="React"]')) return;
                            if (div.closest('h3, h4')) return;

                            const val = div.innerText.trim();
                            if (!val || val === nameText) return;
                            if (/^\d+\s*(min|h|s|sec)$/.test(val)) return;
                            if (/^(J'aime|Like|Répondre|Reply)$/i.test(val)) return;

                            fullText += ' ' + val;
                        });

                        fullText = [...new Set(
                            fullText.trim().split('\n').map(l => l.trim()).filter(l => l.length > 0)
                        )].join(' ').trim();

                        if (!fullText || fullText === nameText) return;

                        const uniqueId = nameText + '::' + fullText.substring(0, 100);
                        if (seen.has(uniqueId)) return;
                        seen.add(uniqueId);

                        comments.push({ id: uniqueId, name: nameText, message: fullText });
                    } catch(e) {}
                });

                // ✅ Méthode 2 : fallback articles
                document.querySelectorAll('div[role="article"]').forEach(node => {
                    try {
                        let authorName = '';
                        const nameSelectors = [
                            'a[role="link"] > span',
                            'a[role="link"] span',
                            'strong', 'b',
                            'span[style*="font-weight: 600"]',
                            'span[style*="font-weight:600"]',
                        ];
                        for (const sel of nameSelectors) {
                            const el = node.querySelector(sel);
                            if (el && el.innerText.trim().length > 1 && el.innerText.trim().length < 60) {
                                authorName = el.innerText.trim();
                                break;
                            }
                        }
                        if (!authorName) return;

                        let fullText = '';
                        node.querySelectorAll('div[dir="auto"]').forEach(div => {
                            if (div.closest('a')) return;
                            if (div.closest('[role="button"]')) return;
                            const val = div.innerText.trim();
                            if (val && val !== authorName && val.length > 0) fullText += ' ' + val;
                        });

                        fullText = fullText.trim();
                        if (!fullText) return;

                        const uniqueId = authorName + '::' + fullText.substring(0, 100);
                        if (seen.has(uniqueId)) return;
                        seen.add(uniqueId);

                        comments.push({ id: uniqueId, name: authorName, message: fullText });
                    } catch(e) {}
                });

                return comments;
            });

            let newCount = 0;
            data.forEach(c => {
                if (!processedCommentIds.has(c.id)) {
                    processedCommentIds.add(c.id);
                    const isCommand = /\b\d{1,3}\b/.test(c.message) ||
                        /prix|acheter|achat|combien|dispo|vendu|commande|je veux|je prends/i.test(c.message);
                    const prefix = isCommand ? '[COMMANDE]' : '[FB]';
                    fs.appendFileSync(OUTPUT_FILE, `${prefix} ${c.name}: ${c.message}\n`);
                    newCount++;
                    console.log(`💬 ${c.name}: ${c.message.substring(0, 60)}`);
                }
            });

            if (newCount > 0) console.log(`📥 +${newCount} | Total: ${processedCommentIds.size}`);

            if (processedCommentIds.size > 1000) {
                const arr = [...processedCommentIds];
                processedCommentIds = new Set(arr.slice(arr.length - 600));
            }

        } catch (e) {
            if (!e.message.includes('context was destroyed')) {
                console.log('Erreur loop:', e.message);
            }
        }
    }, 2500);
}

app.post('/start', async (req, res) => {
    const url = req.body.link;
    if (!url) return res.status(400).json({ error: 'Lien manquant' });
    if (isRunning) await stopExtraction();

    try {
        fs.mkdirSync(path.dirname(OUTPUT_FILE), { recursive: true });
        fs.writeFileSync(OUTPUT_FILE, `[SYSTEM] Connexion en cours...\n`);
        processedCommentIds.clear();

        const chromePath = getChromePath();
        const launchOptions = {
            headless: false,
            args: [
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-notifications',
                '--window-size=1280,900',
                '--start-maximized',
            ]
        };
        if (chromePath) {
            launchOptions.executablePath = chromePath;
            console.log('✅ Chrome:', chromePath);
        }

        browser = await puppeteer.launch(launchOptions);
        res.json({ success: true, message: 'Extraction démarrée' });

        (async () => {
            try {
                page = await browser.newPage();
                await page.setViewport({ width: 1280, height: 900 });
                await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0 Safari/537.36');

                await page.goto('https://www.facebook.com', { waitUntil: 'domcontentloaded', timeout: 30000 });

                const isLoggedIn = await page.evaluate(() => {
                    return !document.querySelector('input[name="email"]');
                });

                if (!isLoggedIn) {
                    console.log('⏳ Connecte-toi à Facebook (30 secondes)...');
                    fs.appendFileSync(OUTPUT_FILE, `[SYSTEM] ⏳ Connecte-toi à Facebook dans Chrome...\n`);
                    await new Promise(r => setTimeout(r, 30000));
                } else {
                    console.log('✅ Déjà connecté à Facebook !');
                }

                await page.setRequestInterception(true);
                page.on('request', (req) => {
                    if (['image', 'media', 'font'].includes(req.resourceType())) {
                        req.abort();
                    } else {
                        req.continue();
                    }
                });

                console.log('📡 Navigation vers:', url);
                await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 90000 });
                await new Promise(r => setTimeout(r, 3000));

                await clickDiscussionTab();
                await new Promise(r => setTimeout(r, 2000));
                await clickDiscussionTab();
                await new Promise(r => setTimeout(r, 2000));

                const articleCount = await page.evaluate(() =>
                    document.querySelectorAll('div[role="article"]').length
                );
                const linkCount = await page.evaluate(() =>
                    document.querySelectorAll('a[role="link"]').length
                );
                console.log(`🔍 Articles: ${articleCount} | Liens: ${linkCount}`);

                fs.appendFileSync(OUTPUT_FILE, `[SYSTEM] Connecté au Live Facebook ✅\n`);
                isRunning = true;
                startScrapingLoop();

            } catch (err) {
                console.error('Erreur:', err.message);
                fs.appendFileSync(OUTPUT_FILE, `[ERREUR] ${err.message}\n`);
            }
        })();

    } catch (err) {
        return res.status(500).json({ error: err.message });
    }
});

// ✅ Route DEBUG
app.get('/debug', async (req, res) => {
    if (!page) return res.send('Pas de page active');

    try {
        await page.screenshot({
            path: path.join(__dirname, '../storage/app/debug_screenshot.png'),
            fullPage: false
        });

        const debugInfo = await page.evaluate(() => {
            const articles = document.querySelectorAll('div[role="article"]');
            const links = document.querySelectorAll('a[role="link"]');

            let firstArticleHTML = '';
            let firstLinkContext = '';

            if (articles[0]) firstArticleHTML = articles[0].innerHTML.substring(0, 3000);
            if (links[0]) firstLinkContext = links[0].closest('div') ?
                links[0].closest('div').innerHTML.substring(0, 1000) : '';

            // Cherche tous les textes visibles dans la page
            const allTexts = [];
            document.querySelectorAll('div[dir="auto"]').forEach(el => {
                const t = el.innerText.trim();
                if (t && t.length > 2) allTexts.push(t);
            });

            return {
                pageTitle: document.title,
                url: window.location.href,
                articleCount: articles.length,
                linkCount: links.length,
                firstArticleHTML,
                firstLinkContext,
                allTexts: allTexts.slice(0, 30),
            };
        });

        res.json(debugInfo);
    } catch(e) {
        res.send('Erreur debug: ' + e.message);
    }
});

app.get('/comments', (req, res) => {
    try {
        if (!fs.existsSync(OUTPUT_FILE)) return res.send('');
        res.send(fs.readFileSync(OUTPUT_FILE, 'utf-8'));
    } catch (err) {
        res.status(500).send('Erreur lecture');
    }
});

app.get('/status', (req, res) => {
    res.json({ running: isRunning, processed: processedCommentIds.size });
});

app.post('/stop', async (req, res) => {
    await stopExtraction();
    res.json({ success: true });
});

app.listen(3000, () => {
    console.log('🚀 Serveur démarré sur http://localhost:3000');
    console.log('🔍 Debug disponible sur http://127.0.0.1:3000/debug');
});*/
/*const express = require('express');
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const os = require('os');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const OUTPUT_FILE = path.join(__dirname, '../storage/app/live_data.txt');

// Liste noire des noms de pages et termes techniques à ignorer
const BLACKLIST = [
    'Nessma', 'islam.bf', 'Sports Gorkha', 'Ferdie Estrella', 'Haramain Servant',
    'Live Masjid', 'Direct tafsir', 'LECTURE EN COURS', 'Vidéos en direct',
    'Reels similaires', 'Confidentialité', 'Conditions générales', 'Publicités',
    'En direct', 'Informations de compte', 'S+2', 'Budget', 'Agence Tecnocasa'
];

let browser = null;
let page = null;
let intervalId = null;
let isRunning = false;
let processedIds = new Set();

const CHROME_EXE = (() => {
    const paths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        path.join(os.homedir(), 'AppData\\Local\\Google\\Chrome\\Application\\chrome.exe'),
    ];
    for (const p of paths) { if (fs.existsSync(p)) return p; }
    return null;
})();

async function stopExtraction() {
    if (intervalId) clearInterval(intervalId);
    if (browser) { try { await browser.close(); } catch (e) {} }
    intervalId = browser = page = null;
    isRunning = false;
    processedIds.clear();
    console.log('⛔ Extraction arrêtée');
}

function startScrapingLoop() {
    intervalId = setInterval(async () => {
        if (!isRunning || !page) return;
        try {
            const data = await page.evaluate(() => {
                const results = [];
                const articles = document.querySelectorAll('div[role="article"]');
                
                articles.forEach(art => {
                    const rect = art.getBoundingClientRect();
                    
                    // Filtre pour sélectionner uniquement les colonnes de droite
                    if (rect.left < window.innerWidth * 0.55) return; 

                    const nameEl = art.querySelector('strong, span[font-weight="bold"], a[role="link"] span');
                    const msgEl = art.querySelector('div[dir="auto"]');

                    if (nameEl && msgEl) {
                        const name = nameEl.innerText.trim();
                        const msg = msgEl.innerText.trim();
                        
                        if (name && msg && name !== msg && name.length < 50) {
                            results.push({ name, msg, id: name + msg });
                        }
                    }
                });
                return results;
            });

            data.forEach(c => {
                const isPollution = BLACKLIST.some(word =>
                    c.name.includes(word) || c.msg.includes(word)
                );

                if (!isPollution && !processedIds.has(c.id)) {
                    processedIds.add(c.id);
                    
                    // Détection commande (si le message contient un chiffre ou mot clé d'achat)
                    const hasNumbers = /\d+/.test(c.msg);
                    const hasBuyWords = /prix|combien|acheter|dispo|commande|je prends/i.test(c.msg);
                    
                    if (hasNumbers || hasBuyWords) {
                        fs.appendFileSync(OUTPUT_FILE, `[COMMANDE] ${c.name}: ${c.msg}\n`);
                    }
                    // Si vous ne souhaitez pas enregistrer d'autres messages, retirez ou commentez la ligne ci-dessous
                    // else {
                    //     fs.appendFileSync(OUTPUT_FILE, `[FB] ${c.name}: ${c.msg}\n`);
                    // }
                }
            });
        } catch (e) { console.log("Erreur loop:", e.message); }
    }, 2500);
}

app.post('/start', async (req, res) => {
    const url = req.body.link;
    if (!url) return res.status(400).json({ error: 'Lien manquant' });

    await stopExtraction();
    fs.mkdirSync(path.dirname(OUTPUT_FILE), { recursive: true });
    fs.writeFileSync(OUTPUT_FILE, `[SYSTEM] Filtrage strict activé...\n`);

    try {
        browser = await puppeteer.launch({
            headless: false,
            executablePath: CHROME_EXE || undefined,
            args: ['--no-sandbox', '--window-size=1400,900', '--disable-notifications']
        });

        res.json({ success: true, message: 'Nettoyage du flux en cours...' });

        page = (await browser.pages())[0];
        await page.setViewport({ width: 1400, height: 900 });

        await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });

        // Attendre que l'utilisateur puisse cliquer sur "Discussion en direct" si besoin
        await new Promise(r => setTimeout(r, 6000));
        
        isRunning = true;
        startScrapingLoop();
    } catch (err) {
        console.error("Erreur start:", err.message);
    }
});

app.get('/comments', (req, res) => {
    if (fs.existsSync(OUTPUT_FILE)) res.send(fs.readFileSync(OUTPUT_FILE, 'utf-8'));
    else res.send('');
});

app.post('/stop', async (req, res) => {
    await stopExtraction();
    res.json({ success: true });
});

app.listen(3000, () => console.log('🚀 Serveur de filtrage propre sur le port 3000'));*/
const express = require('express');
const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');
const os = require('os');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const OUTPUT_FILE = path.join(__dirname, '../storage/app/live_data.txt');

const BLACKLIST = [
    'Nessma', 'islam.bf', 'Sports Gorkha', 'Ferdie Estrella', 'Haramain Servant',
    'Live Masjid', 'Direct tafsir', 'LECTURE EN COURS', 'Vidéos en direct',
    'Reels similaires', 'Confidentialité', 'Conditions générales', 'Publicités',
    'En direct', 'Informations de compte', 'S+2', 'Budget', 'Agence Tecnocasa'
];

let browser     = null;
let page        = null;
let intervalId  = null;
let isRunning   = false;
let isNavigating = false;
let processedIds = new Set();

const CHROME_EXE = (() => {
    const paths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        path.join(os.homedir(), 'AppData\\Local\\Google\\Chrome\\Application\\chrome.exe'),
    ];
    for (const p of paths) { if (fs.existsSync(p)) return p; }
    return null;
})();

async function stopExtraction() {
    if (intervalId) {
        if (typeof intervalId === 'object') {
            clearInterval(intervalId.loop);
            clearInterval(intervalId.scroll);
        } else {
            clearInterval(intervalId);
        }
    }
    if (browser) { try { await browser.close(); } catch(e) {} }
    intervalId   = null;
    browser      = null;
    page         = null;
    isRunning    = false;
    isNavigating = false;
    processedIds.clear();
    console.log('⛔ Extraction arrêtée');
}

// ✅ SCROLL AMÉLIORÉ POUR ÉVITER LE FLOU (Lazy Loading)
async function doScroll() {
    if (!page || isNavigating) return;
    try {
        await page.evaluate(() => {
            const scrollables = Array.from(document.querySelectorAll('*')).filter(el => {
                try {
                    const style = window.getComputedStyle(el);
                    const ov = style.overflow + style.overflowY;
                    return (ov.includes('scroll') || ov.includes('auto'))
                        && el.scrollHeight > el.clientHeight + 50
                        && el.clientHeight > 100
                        && el.getBoundingClientRect().left > window.innerWidth * 0.5;
                } catch(e) { return false; }
            });

            scrollables.forEach(el => {
                el.scrollTop = el.scrollHeight - 10;
                setTimeout(() => { el.scrollTop = el.scrollHeight; }, 50);
            });
            window.scrollBy(0, 10);
        });
    } catch(e) {}
}

function startScrapingLoop() {
    const scrollId = setInterval(() => doScroll(), 2000);

    const loopId = setInterval(async () => {
        if (!isRunning || !page || isNavigating) return;

        try {
            const data = await page.evaluate(() => {
                const results = [];
                const seen    = new Set();

                // ✅ MULTI-SÉLECTEURS : articles standards + vos propres messages
                const articles = document.querySelectorAll('div[role="article"]');

                const ownMessages = document.querySelectorAll(
                    'div[data-testid="ufi_comment_box_body"], ' +
                    'div[data-testid="UFI2Comment/root_depth_0"], ' +
                    'div[aria-label][role="article"]'
                );

                const allItems = [...new Set([...articles, ...ownMessages])];

                allItems.forEach(art => {
                    try {
                        let name = '';

                        // ✅ SÉLECTEURS ÉTENDUS incluant vos propres messages
                        const nameSelectors = [
                            'strong',
                            'a[role="link"] > span',
                            'span[style*="font-weight: 600"]',
                            'h3 span',
                            'span[data-testid="UFI2Comment/author_name"]',
                            'a[href*="facebook.com"] > span',
                            'span.x193iq5w',   // classe fréquente sur les noms FB
                            'span.xt0psk2',
                            'span.x1lliihq'
                        ];

                        for (const sel of nameSelectors) {
                            const el = art.querySelector(sel);
                            if (el) {
                                const txt = el.innerText?.trim();
                                if (txt && txt.length >= 2 && txt.length <= 60) {
                                    name = txt;
                                    break;
                                }
                            }
                        }

                        // ✅ FALLBACK : chercher dans les liens de profil
                        if (!name) {
                            const profileLink = art.querySelector(
                                'a[href*="facebook.com"], a[href*="profile.php"]'
                            );
                            if (profileLink) {
                                const txt = profileLink.innerText?.trim();
                                if (txt && txt.length >= 2 && txt.length <= 60) {
                                    name = txt;
                                }
                            }
                        }

                        // ✅ FALLBACK FINAL
                        if (!name) {
                            const rawText = art.innerText.split('\n')[0];
                            if (rawText && rawText.length > 2 && rawText.length < 50) {
                                name = rawText.trim();
                            } else {
                                name = "Client_Facebook";
                            }
                        }

                        const msgParts = [];
                        art.querySelectorAll('div[dir="auto"], span[dir="auto"]').forEach(el => {
                            if (el.closest('a') || el.closest('[role="button"]')) return;
                            const txt = el.innerText?.trim();
                            if (!txt || txt === name) return;
                            if (/^\d+\s*(min|h|s|sec)$/.test(txt)) return;
                            if (/^(J'aime|Like|Répondre|Reply)$/i.test(txt)) return;
                            msgParts.push(txt);
                        });

                        const msg = [...new Set(msgParts)].join(' ').trim();
                        if (!msg) return;

                        const uid = name + '::' + msg.substring(0, 50);
                        if (seen.has(uid)) return;
                        seen.add(uid);

                        results.push({ name, msg, id: uid });
                    } catch(e) {}
                });

                return results;
            });

            if (!data) return;

            data.forEach(c => {
                // ✅ LOG DE DÉBOGAGE (désactivez en production en commentant cette ligne)
                console.log(`🔍 Détecté: "${c.name}" | blacklisté: ${BLACKLIST.some(w => c.name.includes(w))} | déjà vu: ${processedIds.has(c.id)}`);

                const isPollution = BLACKLIST.some(word => c.name.includes(word));
                if (!isPollution && !processedIds.has(c.id)) {
                    processedIds.add(c.id);
                    fs.appendFileSync(OUTPUT_FILE, `[COMMENTAIRE] ${c.name}: ${c.msg}\n`);
                    console.log(`💬 ${c.name}: ${c.msg}`);
                }
            });

        } catch(e) {}
    }, 1500);

    intervalId = { loop: loopId, scroll: scrollId };
}

async function clickDiscussionTab() {
    try {
        await page.evaluate(() => {
            const terms = ['Discussion en direct', 'Live chat', 'Live Chat', 'دردشة مباشرة'];
            const all = document.querySelectorAll('span, div, button');
            for (const el of all) {
                if (terms.some(term => el.innerText?.includes(term)) && el.innerText.length < 30) {
                    el.click();
                    return true;
                }
            }
        });
    } catch(e) {}
}

app.post('/start', async (req, res) => {
    const url = req.body.link;
    if (!url) return res.status(400).json({ error: 'Lien manquant' });

    await stopExtraction();
    fs.mkdirSync(path.dirname(OUTPUT_FILE), { recursive: true });
    fs.writeFileSync(OUTPUT_FILE, `[SYSTEM] Connexion...\n`);

    try {
        browser = await puppeteer.launch({
            headless: false,
            executablePath: CHROME_EXE || undefined,
            args: ['--no-sandbox', '--disable-notifications', '--start-maximized']
        });

        res.json({ success: true, message: 'Extraction démarrée' });

        (async () => {
            try {
                page = (await browser.pages())[0];
                await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0 Safari/537.36');

                await page.goto(url, { waitUntil: 'networkidle2', timeout: 60000 });
                await new Promise(r => setTimeout(r, 5000));

                await clickDiscussionTab();

                isRunning = true;
                startScrapingLoop();
                fs.appendFileSync(OUTPUT_FILE, `[SYSTEM] Connecté ✅\n`);
            } catch(err) {
                stopExtraction();
            }
        })();
    } catch(err) {
        res.status(500).json({ error: err.message });
    }
});

app.get('/comments', (req, res) => {
    if (!fs.existsSync(OUTPUT_FILE)) return res.send('');
    res.send(fs.readFileSync(OUTPUT_FILE, 'utf-8'));
});

app.post('/stop', async (req, res) => {
    await stopExtraction();
    res.json({ success: true });
});

app.listen(3000, () => {
    console.log('🚀 Serveur DKSoft (Fix LazyLoad + Fix Own Messages) sur port 3000');
});