const { registerBlockType } = nf;
const { RichText, PlainText } = wp.blockEditor;

registerBlockType( 'nf/divider', {

	title: 'Divider',
	icon: 'universal-access-alt',
	category: 'common',

	attributes: {
		content: {
			type: 'array',
			source: 'children',
			selector: 'p',
		},
	},

	example: {
		attributes: {
			content: 'Hello World',
		},
	},

	edit: (props) => {
		const { attributes, setAttributes, className } = props;
		const onChangeContent = ( newContent ) => {
			setAttributes( { content: newContent } );
		};
		return (
			<PlainText
				className={ className }
				onChange={ onChangeContent }
				value={ attributes.content }
			/>
		);
	},

} );
