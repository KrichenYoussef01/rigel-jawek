const { TikTokLiveConnection, WebcastEvent } = require('tiktok-live-connector');
const fs = require('fs');
const path = require('path');

const liveUrl = process.argv[2];
if (!liveUrl) process.exit(1);

const username = liveUrl.split('@')[1]?.split('/')[0];
if (!username) process.exit(1);

const outputFile = path.join(__dirname, '../outputs/tiktok-live.txt');

// Créer le dossier outputs si nécessaire
const outputDir = path.dirname(outputFile);
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

const connection = new TikTokLiveConnection(username);

function time() {
    return new Date().toLocaleTimeString('fr-FR');
}

fs.writeFileSync(outputFile, `[${time()}] 🎬 Live TikTok de @${username}\n\n`);

connection.connect()
    .then(() => {
        console.log('✅ TikTok connecté');
    })
    .catch(err => {
        console.error('❌ Erreur TikTok:', err.message);
        process.exit(1);
    });

connection.on(WebcastEvent.CHAT, data => {
    fs.appendFileSync(
        outputFile,
        `[${time()}] ${data.user.uniqueId} : ${data.comment}\n`
    );
});

connection.on(WebcastEvent.STREAMEND, () => {
    fs.appendFileSync(outputFile, `\n[${time()}] 🔴 Live terminé\n`);
    process.exit(0);
});