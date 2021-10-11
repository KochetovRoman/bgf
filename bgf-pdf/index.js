const express = require('express')
const bodyParser = require('body-parser')
const puppeteer = require('puppeteer');
const app = express()
const cors = require('cors');
const fs = require('fs');
const path = require('path');
const extractDomain = require('extract-domain');

app.use(cors({
    origin: "*"
}));
// parse application/x-www-form-urlencoded
app.use(bodyParser.urlencoded({ extended: false }))
    // parse application/json
app.use(bodyParser.json())

app.post("/generate-pdf-from-url", async(req, res, __) => {
    try {
        let filePath = await saveToPdf(req.body.url);
        let hash = extractDomain(req.body.url);
        res.download(filePath);
    } catch (error) {
        res.status(error.status || 500);
        res.send(error.message);
    }

});


app.use(cors({
    origin: "*"
}));

var browser;
(async() => {
    browser = await puppeteer.launch({
        headless: true,
        ignoreHTTPSErrors: true,
        args: ['--no-sandbox']
    });
})();

function getFilesizeInBytes(filename) {
    var stats = fs.statSync(filename);
    var fileSizeInBytes = stats.size;
    return fileSizeInBytes;
}

async function saveToPdf(url) {
    console.log('puppeteer running');
    // Browser actions & buffer creator
    const page = await browser.newPage();
    await page.goto(url);
    console.log('go url');
    await page.setViewport({
        width: 1920,
        height: 1020
    });
    await page.addScriptTag({ url: 'https://code.jquery.com/jquery-3.2.1.min.js' });
    console.log('add jquery');
    await page.evaluate(() => {
        if ($('.ant-layout-sider') !== null) {
            $('.ant-layout-sider').remove();
        }
        if ($('.leaflet-control-container') !== null) {
            $('.leaflet-control-container').remove();
        }
        if ($('.Map-areaSelector') !== null) {
            $('.Map-areaSelector').remove()
        }
        if ($('.FilterBlock-filterButtons') !== null) {
            $('.FilterBlock-filterButtons').remove();
        }
    });
    console.log('creating pdf');
    await page.emulateMediaType('screen');
    let hash = extractDomain(url);
    let path = 'public/results/' + Date.now() + '_' + hash + '.pdf';
    const pdf = await page.pdf({
        path: path,
        printBackground: true,
        scale: 1,
        width: 1920,
        height: 1020,
        margin: {
            top: '2.54cm',
            bottom: '2.54cm',
            left: '2.54cm',
            right: '2.54cm'
        },
    });
    await page.close();
    // Return Buffer
    return path;
};

app.listen(3000, () => {
    console.log(`http://localhost:3000/ app is running.`);
})