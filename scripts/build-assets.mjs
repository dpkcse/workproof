import { createHash } from 'node:crypto';
import { mkdir, readFile, rm, writeFile } from 'node:fs/promises';
import path from 'node:path';

const buildDirectory = path.join(process.cwd(), 'public', 'build');
const assetsDirectory = path.join(buildDirectory, 'assets');

const entries = [
  {
    source: path.join(process.cwd(), 'resources', 'css', 'app.css'),
    manifestKey: 'resources/css/app.css',
    extension: 'css',
  },
  {
    source: path.join(process.cwd(), 'resources', 'js', 'app.js'),
    manifestKey: 'resources/js/app.js',
    extension: 'js',
  },
];

await rm(buildDirectory, { force: true, recursive: true });
await mkdir(assetsDirectory, { recursive: true });

const manifest = {};

for (const entry of entries) {
  const contents = await readFile(entry.source);
  const hash = createHash('sha256').update(contents).digest('hex').slice(0, 8);
  const fileName = `app-${hash}.${entry.extension}`;
  const outputPath = path.join(assetsDirectory, fileName);

  await writeFile(outputPath, contents);

  manifest[entry.manifestKey] = {
    file: `assets/${fileName}`,
    src: entry.manifestKey,
    isEntry: true,
  };
}

await writeFile(
  path.join(buildDirectory, 'manifest.json'),
  `${JSON.stringify(manifest, null, 2)}\n`,
);

console.log(`Built ${entries.length} assets into public/build.`);
