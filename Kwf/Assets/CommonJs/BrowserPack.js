var pack = require('browser-pack')();
process.stdin.pipe(pack).pipe(process.stdout);
