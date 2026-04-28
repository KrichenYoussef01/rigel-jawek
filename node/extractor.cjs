const { TikTokLiveConnection, WebcastEvent } = require('tiktok-live-connector');
const fs = require('fs');
const path = require('path');

const liveUrl = process.argv[2];
if (!liveUrl) process.exit(1);

const username = liveUrl.split('@')[1]?.split('/')[0];
if (!username) process.exit(1);

const outputFile = path.join(__dirname, '../storage/app/tiktok/live.txt');

const connection = new TikTokLiveConnection(username);

function time() {
    return new Date().toLocaleTimeString('fr-FR');
}

fs.writeFileSync(outputFile, `[${time()}] 🎬 Live de @${username}\n`);

connection.connect();

connection.on(WebcastEvent.CHAT, data => {
    fs.appendFileSync(
        outputFile,
        `[${time()}] ${data.user.uniqueId} : ${data.comment}\n`
    );
});

connection.on(WebcastEvent.LIVE_END, () => {
    fs.appendFileSync(outputFile, `[${time()}] 🔴 Live terminé\n`);
    process.exit(0);
});
