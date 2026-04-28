const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

const url = process.argv[2];
const OUTPUT_FILE = path.join(__dirname, '../storage/app/live_data.txt');

(async () => {
    const browser = await puppeteer.launch({ 
        headless: "new",
        args: ['--no-sandbox', '--disable-setuid-sandbox'] 
    });
    const page = await browser.newPage();
    
    // On simule un utilisateur réel
    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    
    await page.goto(url, { waitUntil: 'networkidle2' });
    console.log("Page Facebook chargée");
    fs.appendFileSync(OUTPUT_FILE, `[SYSTEM] Connecté à Facebook Live\n`);

    // Boucle de lecture des commentaires
    setInterval(async () => {
        try {
            const comments = await page.evaluate(() => {
                // On cherche les blocs de commentaires Facebook
                const items = document.querySelectorAll('div[role="article"]');
                return Array.from(items).map(item => {
                    const name = item.querySelector('strong, span[font-weight="bold"]')?.innerText || "Client";
                    const text = item.innerText.split('\n').pop(); // Récupère le dernier texte du bloc
                    return `[FB] ${name}: ${text}`;
                }).slice(-5);
            });

            // On écrit les nouveaux messages
            comments.forEach(c => fs.appendFileSync(OUTPUT_FILE, `${c}\n`));
        } catch (e) {
            console.log("Erreur de lecture FB");
        }
    }, 4000);
})();