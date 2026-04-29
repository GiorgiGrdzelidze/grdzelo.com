/**
 * Bake brand SVGs to PNGs for Open Graph, favicons and external use.
 *
 * Run:  npm run brand
 *
 * Why: many platforms (Slack, iOS home-screen, Twitter cards, certain RSS
 * readers) prefer PNG over SVG. SVGs that contain <text> elements also
 * render differently across browsers because they fall back to system fonts.
 * Baking to PNG locks in the exact look.
 *
 * Requires: npm i -D sharp
 */

import fs from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const PUBLIC = path.join(process.cwd(), 'public');

const jobs = [
  // [source SVG,                         output PNG,                              width]
  ['favicon-light.svg',                   'favicon-light.png',                      512],
  ['favicon-dark.svg',                    'favicon-dark.png',                       512],
  ['apple-touch-icon-light.svg',          'apple-touch-icon-light.png',             180],
  ['apple-touch-icon-dark.svg',           'apple-touch-icon-dark.png',              180],
  ['apple-touch-icon.svg',                'apple-touch-icon.png',                   180],
  ['brand/monogram.svg',                  'brand/monogram.png',                     512],
  ['brand/wordmark.svg',                  'brand/wordmark.png',                    1280],
  ['brand/og-default.svg',                'brand/og-default.png',                  1200],
];

console.log('Baking brand assets…\n');

for (const [src, dst, width] of jobs) {
  const inputPath  = path.join(PUBLIC, src);
  const outputPath = path.join(PUBLIC, dst);

  try {
    const svg = await fs.readFile(inputPath);
    await sharp(svg, { density: 300 })
      .resize({ width })
      .png({ compressionLevel: 9 })
      .toFile(outputPath);
    console.log(`  ✓ ${src.padEnd(32)} → ${dst} (${width}px)`);
  } catch (err) {
    console.error(`  ✗ ${src} — ${err.message}`);
    process.exitCode = 1;
  }
}

// Multi-size .ico from the favicon — uses the LIGHT variant as the canonical .ico
// (most legacy tools assume light tab bars). Modern browsers respect the adaptive
// favicon.svg via <link rel="icon"> below, so .ico is just a fallback.
try {
  const pngToIco = (await import('png-to-ico')).default;
  const sizes = await Promise.all([16, 32, 48, 64].map(size =>
    sharp(path.join(PUBLIC, 'favicon-light.svg'), { density: 300 })
      .resize(size, size)
      .png()
      .toBuffer()
  ));
  const ico = await pngToIco(sizes);
  await fs.writeFile(path.join(PUBLIC, 'favicon.ico'), ico);
  console.log(`  ✓ favicon.ico (16/32/48/64, light variant)`);
} catch {
  console.warn(`  ⚠ favicon.ico skipped — install with: npm i -D png-to-ico`);
}

console.log('\nDone.');
