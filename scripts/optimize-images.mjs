// Usage: npm run images
// Reads resources/images/**, writes responsive AVIF + WebP to public/images and a manifest.json
// used by <x-picture>. Runs on your computer (needs sharp), so cPanel needs no image libraries for static images.
import sharp from 'sharp';
import fs from 'node:fs/promises';
import path from 'node:path';

const SRC = 'resources/images';
const OUT = 'public/images';
const WIDTHS = [480, 768, 1024, 1440, 1920, 2560];
const RASTER = /\.(jpe?g|png|webp)$/i;

async function* walk(dir) {
    for (const e of await fs.readdir(dir, { withFileTypes: true })) {
        const p = path.join(dir, e.name);
        if (e.isDirectory()) yield* walk(p);
        else yield p;
    }
}

const newer = async (out, src) => {
    try { return (await fs.stat(out)).mtimeMs >= (await fs.stat(src)).mtimeMs; } catch { return false; }
};

await fs.mkdir(OUT, { recursive: true });
const manifest = {};

for await (const file of walk(SRC)) {
    const rel = path.relative(SRC, file).split(path.sep).join('/');
    const dest = path.join(OUT, path.dirname(rel));
    await fs.mkdir(dest, { recursive: true });

    if (!RASTER.test(file)) { // svg, ico, etc: copy as is
        await fs.copyFile(file, path.join(OUT, rel));
        continue;
    }

    const { width, height } = await sharp(file).metadata();
    let widths = WIDTHS.filter((w) => w <= width);
    if (!widths.length || (width <= 2560 && width > Math.max(...widths))) widths.push(width);

    const base = rel.replace(RASTER, '');
    for (const w of widths) {
        for (const fmt of ['webp', 'avif']) {
            const out = path.join(OUT, `${base}-${w}.${fmt}`);
            if (await newer(out, file)) continue;
            const img = sharp(file).resize({ width: w, withoutEnlargement: true });
            await (fmt === 'webp' ? img.webp({ quality: 78 }) : img.avif({ quality: 55, effort: 4 })).toFile(out);
        }
    }
    manifest[rel] = { base, width, height, widths };
    console.log(`${rel}: ${widths.join(', ')}`);
}

await fs.writeFile(path.join(OUT, 'manifest.json'), JSON.stringify(manifest, null, 2));
console.log(`Done. ${Object.keys(manifest).length} image(s).`);
