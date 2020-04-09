// Import all ./*/block.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('../blocks', true, /\/block\.js$/));
