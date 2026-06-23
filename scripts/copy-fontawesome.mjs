import { cpSync, existsSync, mkdirSync } from 'fs';
import { dirname, join } from 'path';
import { fileURLToPath } from 'url';

const root = join(dirname(fileURLToPath(import.meta.url)), '..');
const packageRoot = join(root, 'node_modules', '@fortawesome', 'fontawesome-free');
const destination = join(root, 'public', 'vendor', 'fontawesome');

if (! existsSync(packageRoot)) {
    console.warn('@fortawesome/fontawesome-free is not installed — run npm install');
    process.exit(0);
}

mkdirSync(join(destination, 'css'), { recursive: true });
mkdirSync(join(destination, 'webfonts'), { recursive: true });

cpSync(join(packageRoot, 'css', 'all.min.css'), join(destination, 'css', 'all.min.css'));
cpSync(join(packageRoot, 'webfonts'), join(destination, 'webfonts'), { recursive: true });
cpSync(join(packageRoot, 'svgs'), join(destination, 'svgs'), { recursive: true });

console.log('Font Awesome copied to public/vendor/fontawesome');
