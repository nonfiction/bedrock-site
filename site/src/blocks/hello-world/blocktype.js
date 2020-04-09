const { registerBlockType } = nf;
const { RichText } = wp.blockEditor;

registerBlockType( 'nf/hello-world', {

	title: 'Hello World',
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
			<RichText
				tagName="p"
				className={ className }
				onChange={ onChangeContent }
				value={ attributes.content }
			/>
		);
	},

	save: (props) => {
		return <RichText.Content tagName="p" value={ props.attributes.content } />;
	},

} );
