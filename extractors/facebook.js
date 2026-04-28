const { Builder, By } = require('selenium-webdriver');
const chrome = require('selenium-webdriver/chrome');
const fs = require('fs');
const path = require('path');

const liveUrl = process.argv[2];
if (!liveUrl || !liveUrl.includes('facebook.com')) {
    console.log('❌ URL Facebook invalide');
    process.exit(1);
}

const outputFile = path.join(__dirname, '../outputs/facebook-live.txt');

// Créer le dossier outputs si nécessaire
const outputDir = path.dirname(outputFile);
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

function time() {
    return new Date().toLocaleTimeString('fr-FR');
}

async function main() {
    const options = new chrome.Options();
    options.addArguments('--disable-blink-features=AutomationControlled');
    
    const driver = await new Builder()
        .forBrowser('chrome')
        .setChromeOptions(options)
        .build();
    
    try {
        fs.writeFileSync(outputFile, `[${time()}] 🎬 Live Facebook\n\n`);
        
        await driver.get('https://www.facebook.com');
        await driver.sleep(45000); // Login manuel
        
        await driver.get(liveUrl);
        await driver.sleep(15000);
        
        console.log('✅ Facebook connecté');
        
        const commentsSeen = new Set();
        
        while (true) {
            const elements = await driver.findElements(By.xpath("//span[@dir='auto']"));
            
            for (const element of elements) {
                try {
                    const text = await element.getText();
                    const trimmed = text.trim();
                    
                    if (trimmed && trimmed.length > 5 && !commentsSeen.has(trimmed)) {
                        commentsSeen.add(trimmed);
                        
                        fs.appendFileSync(
                            outputFile,
                            `[${time()}] ${trimmed}\n`
                        );
                    }
                } catch (err) {}
            }
            
            await driver.sleep(3000);
        }
        
    } catch (error) {
        console.error('❌ Erreur Facebook:', error.message);
    }
}

main();
