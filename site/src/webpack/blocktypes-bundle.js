// Initialize nf.blockTypes object
global.nf = global.nf || {}; 
nf.blockTypes = nf.blockTypes || {};

// Method to add new blocktypes
nf.registerBlockType = function(name, blockType){ 
  nf.blockTypes[name] = blockType; 
}

// Method to load a stored blocktype
nf.loadBlockType = function(name){ 
	if ( nf.blockTypes.hasOwnProperty(name) ) {
		let blockType = nf.blockTypes[name];
		wp.blocks.registerBlockType(name, blockType);
	} 
}

// Import all ./*/blocktype.js files
function importAll (r) { r.keys().forEach(r) }
importAll(require.context('../blocks', true, /\/blocktype\.js$/));
