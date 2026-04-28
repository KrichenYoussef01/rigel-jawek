const express = require('express');
const { exec } = require('child_process');
const fs = require('fs');
const path = require('path');
const app = express();

app.use(express.json());
app.use(require('cors')()); // Pour autoriser les requêtes du Dashboard

const OUTPUT_FILE = path.join(__dirname, '../storage/app/live_data.txt');

app.post('/start', (req, res) => {
    const { link } = req.body;
    let command = "";

    if (link.includes("tiktok.com")) {
        command = `node extractor.cjs "${link}"`;
    } else if (link.includes("facebook.com")) {
        command = `node extractorr.cjs "${link}"`;
    } else {
        return res.status(400).json({ error: "Plateforme non supportée" });
    }

    // On lance l'extraction en arrière-plan
    exec(command, { cwd: __dirname });
    res.json({ success: true, platform: link.includes("tiktok") ? "TikTok" : "Facebook" });
});

app.get('/comments', (req, res) => {
    if (!fs.existsSync(OUTPUT_FILE)) return res.send("");
    res.send(fs.readFileSync(OUTPUT_FILE, 'utf8'));
});

app.listen(3000, () => console.log("🚀 Bridge DKSoft (TT + FB) sur port 3000"));